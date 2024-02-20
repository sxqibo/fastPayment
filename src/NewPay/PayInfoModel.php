<?php

namespace Sxqibo\FastPayment\NewPay;

/**
 * 5.9 微信&支付宝扫码（B扫C）
 *
 * https://www.yuque.com/chenyanfei-sjuaz/uhng8q/zokydupg793yle6v#bGx4G
 */
final class PayInfoModel extends BaseModel
{
    const IS_NOT_FIELD = [
        ['tranAmt', '支付金额 不能为空'],
        ['scanCodeId', '条形码 不能为空'],
        ['notifyUrl', '商户异步通知地址 不能为空'],
        ['subMchId', '报备编号 不能为空'],
        ['terminalId', '商户设备编号 不能为空'],
    ];

    const CIPHER_DATA = [
        'tranAmt', 'scanCodeId', 'notifyUrl', 'riskExpand', 'subject', 'goodsInfo', 'merIp', 'subMchId'
    ];

    private $tranAmt;
    private $scanCodeId;
    private $notifyUrl;
    private $riskExpand;
    private $subject;
    private $goodsInfo;
    private $merIp;
    private $subMchId;
    private $terminalId;
    private $terminalIp;
    private $location;
    private $holderName;
    private $identityType;
    private $identityCode;
    private $minAge;

    public function __construct()
    {
        $this->notifyUrl = 'http://xxx.xxx.com/xxx';
        $this->reskExpand = '';
        $this->subject = '';
        $this->goodsInfo = '';
        $this->merIp = '';
    }

    public function getModelData(): array
    {
        $data = parent::getData(__CLASS__, $this);

        $signData = [];

        foreach (self::CIPHER_DATA as $key) {
            $signData[$key] = $data[$key];
        }

        return $signData;
    }

    /**
     * 数据赋值
     *
     * @param $data
     * @return void
     */
    public function copy($data)
    {
        foreach ($data as $key => $value) {
            $this->$key = $value;
        }
    }

    public function verify()
    {
        foreach (self::IS_NOT_FIELD as $field) {
            $name = $field[0];
            if (empty($this->$name)) {
                return $field[1];
            }
        }

        return '';
    }

    public function getMsgCipherText($publicKey)
    {
        $msgText = $this->getModelData();

        $msgCiphertext = json_encode($msgText, JSON_UNESCAPED_UNICODE);

        return $this->publicEncrypt($msgCiphertext, $publicKey);
    }

    private function publicEncrypt($input, $pk)
    {
        $split = str_split($input, 117);

        $crypto = '';

        foreach ($split as $chunk) {
            $isOkey = openssl_public_encrypt($chunk, $output, $pk, OPENSSL_PKCS1_PADDING);
            if (!$isOkey) {
                return false;
            }
            $crypto .= $output;
        }

        return base64_encode($crypto);
    }
}
