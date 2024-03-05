<?php

namespace Sxqibo\FastPayment\NewPay;

use Sxqibo\FastPayment\NewPay\BaseModel;

/**
 * 5.3 支付宝H5
 *
 * https://www.yuque.com/chenyanfei-sjuaz/uhng8q/efqwi8
 */
final class H5InfoModel extends BaseModel
{
    const IS_NOT_FIELD = [
        ['tranAmt', '支付金额 不能为空'],
        ['payType', '付款方式 不能为空'],
        ['orderSubject', '订单标题 不能为空'],
        ['merchantId', '报备编号 不能为空'],
    ];

    private $tranAmt;
    private $payType;
    private $exPayMode;
    private $cardNo;
    private $holderName;
    private $identityCode;
    private $merUserId;
    private $orderExpireTime;
    private $frontUrl;
    private $notifyUrl;
    private $riskExpand;
    private $goodsInfo;
    private $orderSubject;
    private $orderDesc;
    private $merchantId;
    private $bizProtocolNo;
    private $payProtocolNo;
    private $merUserIp;
    private $payLimit;

    public function __construct()
    {
        $this->payType = 'HnaALL'; // 默认
        $this->frontUrl = 'http://xxx.xxx.com/xxx';
        $this->notifyUrl = 'http://xxx.xxx.com/xxx';
        $this->merUserIp = '192.168.0.1';

        $this->exPayMode = '';
        $this->cardNo = '';
        $this->holderName = '';
        $this->identityCode = '';
        $this->merUserId = '';
        $this->orderExpireTime = 120;
        $this->riskExpand = '';
        $this->goodsInfo = '';
        $this->orderDesc = '';
        $this->merchantId = '';
        $this->bizProtocolNo = '';
        $this->payProtocolNo = '';
        $this->payLimit = '';
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

        if ($this->payType == 'HnaALL') {
            $this->exPayMode = '1';
        }

        $this->merchantId = json_encode(['02' => $data['merchantId']], JSON_UNESCAPED_UNICODE);
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
