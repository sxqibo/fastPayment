<?php

namespace Sxqibo\FastPayment\NewPay;

/**
 * 5.1 微信公众号&支付宝生活号
 *
 * https://www.yuque.com/chenyanfei-sjuaz/uhng8q/hekpg2
 */
final class InChargeInfoModel extends BaseModel
{
    const IS_NOT_FIELD = [
        ['tranAmt', '支付金额不能为空'],
        ['orgCode', '目标资金机构代码不能为空'],
        ['goodsInfo', '商品信息不能为空'],
        ['orderSubject', '订单标题不能为空'],
        ['merchantId', '报备编号	0-30不能为空'],
    ];

    private $tranAmt;
    private $orgCode;
    private $notifyServerUrl;
    private $merUserIp;
    private $expirTime;
    private $riskExpand;
    private $goodsInfo;
    private $orderSubject;
//    private $orderDesc;
//    private $payLimit;
    private $appId;
    private $openId;
    private $aliAppId;
    private $buyerLogonId;
    private $buyerId;
    private $merchantId;
//    private $holderName;
//    private $identityType;
//    private $identityCode;
//    private $minAge;

    public function __construct()
    {
        $this->notifyServerUrl = 'http://xxx.xxx.com/xxx';
        $this->merUserIp = '211.12.38.88';
        $this->appId = '';
        $this->openId = '';
        $this->aliAppId = '';
        $this->buyerLogonId = '';
        $this->buyerId = '';
        $this->merchantId = '';
        $this->expirTime = 120;
        $this->riskExpand = '';
        $this->goodsInfo = '';

    }

    public function getModelData(): array
    {
        $data = parent::getData(__CLASS__, $this);

        unset($data['holderName']);
        unset($data['identityType']);
        unset($data['identityCode']);
        unset($data['minAge']);

        return $data;
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
