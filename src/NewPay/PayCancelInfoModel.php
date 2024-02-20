<?php
namespace Sxqibo\FastPayment\NewPay;

/**
 * 5.9 微信&支付宝扫码（B扫C）
 *
 * https://www.yuque.com/chenyanfei-sjuaz/uhng8q/zokydupg793yle6v#bGx4G
 */
final class PayCancelInfoModel extends BaseModel
{
    const IS_NOT_FIELD = [
        ['orgTranAmt', '原订单交易金额 不能为空'],
        ['orgMerOrderId', '原订单商户订单号 不能为空'],
        ['orgSubmitTime', '原订单商户提交时间 不能为空'],
        ['hnapayOrderId', '原支付订单的新生订单号 不能为空'],
        ['notifyUrl', '商户异步通知地址 不能为空'],
    ];

    private $orgTranAmt;
    private $orgMerOrderId;
    private $orgSubmitTime;
    private $hnapayOrderId;
    private $notifyUrl;
    private $riskExpand;
    private $merIp;

    public function __construct()
    {
        $this->notifyUrl = 'http://xxx.xxx.com/xxx';
    }

    public function getModelData(): array
    {
        return parent::getData(__CLASS__, $this);
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
