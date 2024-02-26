<?php

namespace Sxqibo\FastPayment\NewPay;

use AlibabaCloud\Tea\Model;

/**
 * 5.1 微信公众号&支付宝生活号
 *
 * https://www.yuque.com/chenyanfei-sjuaz/uhng8q/hekpg2
 */
final class InChargeModel extends BaseModel
{
    const REQUEST_URL = 'https://gateway.hnapay.com/ita/inCharge.do';

    /** @var string 版本 */
    const VERSION = '2.0';

    /** @var string 交易代码 */
    const TRANCODE = 'ITA10';

    const IS_NOT_FIELD = [
        ['merId', '商户ID 不能为空'],
        ['merOrderId', '商户订单号 不能为空'],
    ];

    /** @var string[] 签名字段 */
    const SIGN_FIELD =  [
        'version', 'tranCode', 'merId',
        'merOrderId', 'submitTime', 'msgCiphertext',
    ];

    /** @var string[] 验签字段 */
    const VERIFY_FIELD =  [
        'version', 'tranCode', 'merOrderId', 'merId',
        'charset', 'signType', 'resultCode', 'errorCode',
        'hnapayOrderId', 'payInfo'
    ];

    private $version;
    private $tranCode;
    private $merId;
    private $merOrderId;
    private $submitTime;
    private $msgCiphertext;
    private $signType;
//    private $remark;
    private $merAttach;
    private $charset;
    private $signValue;

    private $inChargeInfoModel;

    public function __construct($config = [])
    {
        $this->version = self::VERSION;
        $this->tranCode = self::TRANCODE;
        $this->signType = self::SIGNTYPE_RSA;
        $this->charset = self::CHARSET_UTF8;
        $this->submitTime = date('YmdHis', time());
        $this->merAttach = '';

        $this->inChargeInfoModel = new InChargeInfoModel();
    }

    /**
     * 数据赋值
     *
     * @param $data
     * @return void
     */
    public function copy($data)
    {
        $this->merId = $data['merId'];
        $this->merOrderId = $data['merOrderId'];

        unset($data['merId']);
        unset($data['merOrderId']);

        $this->inChargeInfoModel->copy($data);

        $this->msgCiphertext = $this->inChargeInfoModel->getMsgCipherText($this->publicKey);

        $this->signValue = RsaUtil::buildSignForBase64(
            $this->getSignData(), $this->privateKey);
    }

    public function getModelData(): array
    {
        return parent::getData(__CLASS__, $this);
    }

    public function getSignData()
    {
        return Util::getStringData(self::SIGN_FIELD, $this->getModelData());
    }

    public function verify()
    {
        foreach (self::IS_NOT_FIELD as $field) {
            $name = $field[0];
            if (empty($this->$name)) {
                return $field[1];
            }
        }

        return $this->refundInfoModel->verify();
    }

    public function verifySign($responseData)
    {
        $data = $responseData;
        $data['payInfo'] = str_replace('\\', '', json_encode($responseData['payInfo'], JSON_UNESCAPED_UNICODE));

        return RsaUtil::verifySignForBase64($responseData['signValue'],
            $this->publicKey,
            Util::getStringData(self::VERIFY_FIELD, $data));
    }
}
