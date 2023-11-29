<?php

namespace Sxqibo\FastPayment\NewPay;

use Exception;

/**
 * 微信&支付宝扫码（C扫B）
 * 新生支付文档 5.2
 * 文档地址：https://www.yuque.com/chenyanfei-sjuaz/uhng8q/uoce7b#wZTrE
 */
final class ScanPayModel extends BaseModel
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

    private $config = [
        'tranIP' => '114.114.114.114',
        'notifyUrl' => 'http://xxx.xxx.com/xxx'
    ];

    public function __construct($config = [])
    {
        $this->version = self::VERSION;
        $this->tranCode = self::TRANCODE;
        $this->submitTime = date('YmdHis', time());
        $this->payType = self::PAY_TYPE;
        // 此处从配置读
        $this->tranIP = '114.114.114.114';
        // 此处从配置读
        $this->notifyUrl = 'http://xxx.xxx.com/xxx';
        $this->charset = self::CHARSET_UTF8;
        $this->signType = self::SIGNTYPE_RSA;
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

        $err = $this->verify();

        if (!empty($err)) {
            throw new Exception($err);
        }

        $this->signMsg = RsaUtil::buildSignForBin2Hex($this->getSignData(), $this->privateKey);
    }

    public function getModelData(): array
    {
        $data = parent::getData(__CLASS__, $this);
        unset($data['config']);

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
        return Util::getStringData(self::SIGN_FIELD, $this->getModelData());
    }

    public function verifySign($responseData): bool
    {
        // 对返回值的验签
        return RsaUtil::verifySignForHex2Bin($responseData['signMsg'],
            $this->publicKey,
            Util::getStringData(self::VERIFY_FIELD, $responseData));
    }
}
