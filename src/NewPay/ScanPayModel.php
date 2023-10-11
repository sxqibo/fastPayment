<?php

namespace Sxqibo\FastPayment\NewPay;

use ReflectionClass;

final class ScanPayModel
{
    const REQUEST_URL = 'https://gateway.hnapay.com/website/scanPay.do';

    /** @var string 交易代码 */
    const TRANCODE = 'WS01';

    /** @var string 版本号 */
    const VERSION = '2.1';

    /** @var string 支付方式 */
    const PAY_TYPE = 'QRCODE_B2C';

    /** @var string 目标资金机构代码 */
    /** @var string 微信支付 */
    const ORGCODE_WECHATPAY = 'WECHATPAY';
    /** @var string 阿里支付 */
    const ORGCODE_ALIPAY = 'ALIPAY';
    /** @var string 腾讯支付 */
    const ORGCODE_TENPAY = 'TENPAY';
    /** @var string 银联二维码 */
    const ORGCODE_UNIONPAY = 'UNIONPAY';

    /** @var int 编码方式 */
    const CHARSET = 1;

    /** @var int 签名类型 */
    /** @var int RSA */
    const SIGNTYPE_RSA = 1;

    /** @var string[] 签名字段 */
    const SIGN_FIELD =  ['tranCode', 'version',
        'merId', 'submitTime', 'merOrderNum', 'tranAmt', 'payType',
        'orgCode', 'notifyUrl', 'charset', 'signType'];

    /** @var string[] 验签字段 */
    const VERIFY_FIELD =  ['tranCode', 'version',
        'merId', 'merOrderNum', 'tranAmt', 'submitTime', 'qrCodeUrl',
        'hnapayOrderId', 'resultCode', 'charset', 'signType'];

    private $version;
    private $tranCode;
    private $merId;
    private $merOrderNum;
    private $tranAmt;
    private $submitTime;
    private $payType;
    private $orgCode;
    private $goodsName;
    private $goodsDetail;
    private $tranIP;
    private $notifyUrl;
    private $remark;
    private $weChatMchId;
    private $payLimit;
    private $identityType;
    private $minAge;
    private $vasType;
    private $holderName;
    private $identityCode;
    private $mobileNo;
    private $charset;
    private $signType;
    private $signMsg;

    private $publicKey;
    private $privateKey;

    public function __construct()
    {
        $this->version = self::VERSION;
        $this->tranCode = self::TRANCODE;
        $this->submitTime = date('YmdHis', time());
        $this->payType = self::PAY_TYPE;
        // 此处从配置读
        $this->tranIP = '114.114.114.114';
        // 此处从配置读
        $this->notifyUrl = 'http://xxx.xxx.com/xxx';
        $this->charset = self::CHARSET;
        $this->signType = self::SIGNTYPE_RSA;
    }

    public function setPublicKey(string $publicKey)
    {
        // 收款公私钥/网关公钥（收款）.pem
        $this->publicKey = KeyUtils::makePublicKey($publicKey);
    }

    public function setPrivateKey(string $privateKey)
    {
        // 收款公私钥/商户私钥（收款）.pem
        $this->privateKey = KeyUtils::makePrivateKey($privateKey);
    }

    public function __get($name)
    {
        return $this->$name;
    }

    public function __set($name, $value)
    {
        $this->$name = $value;
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

    /**
     * 属性转数组
     *
     * @return array
     */
    public function getData()
    {
        $data = [];

        $reflectionClass = new ReflectionClass(__CLASS__);
        $reflectionProperties = $reflectionClass->getProperties();

        foreach ($reflectionProperties as $property) {
            $propertyName = $property->getName();
            $data[$propertyName] = $this->$propertyName;
        }

        return $data;
    }

    /**
     * 关键数据验证
     *
     * @return string
     */
    public function verify()
    {
        if (empty($this->merId)) {
            return 'merId 不能为空';
        }

        if (empty($this->merOrderNum)) {
            return 'merOrderNum 不能为空';
        }

        if (empty($this->tranAmt)) {
            return 'tranAmt 不能为空';
        }

        if (empty($this->orgCode)) {
            return 'orgCode 不能为空';
        }

        if (empty($this->weChatMchId)) {
            return 'weChatMchId 不能为空';
        }

        if (!empty($this->vasType)) {
            if (empty($this->holderName)) {
                return 'vasType 不为空时，holderName 必填';
            }
            if (empty($this->identityCode)) {
                return 'vasType 不为空时，identityCode 必填';
            }
            if (empty($this->mobileNo)) {
                return 'vasType 不为空时，mobileNo 必填';
            }
        }

        return '';
    }

    public function getSignData(): string
    {
        return Util::getStringData(self::SIGN_FIELD, $this->getData());
    }
}
