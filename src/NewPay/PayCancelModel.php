<?php

namespace Sxqibo\FastPayment\NewPay;

/**
 * 5.9 微信&支付宝扫码（B扫C）
 *
 * https://www.yuque.com/chenyanfei-sjuaz/uhng8q/zokydupg793yle6v#bGx4G
 */
final class PayCancelModel extends BaseModel
{
    const REQUEST_URL = 'https://gateway.hnapay.com/scp/payCancel.do';

    /** @var string 版本 */
    const VERSION = '2.0';

    /** @var string 交易代码 */
    const TRANCODE = 'SCP02';

    const IS_NOT_FIELD = [
        ['merId', '商户ID 不能为空'],
        ['merOrderId', '商户订单号 不能为空'],
    ];

    /** @var string[] 签名字段 */
    const SIGN_FIELD =  [
        'version', 'tranCode', 'merId', 'merOrderId',
        'submitTime', 'msgCiphertext', 'signType',
    ];

    /** @var string[] 验签字段 */
    const VERIFY_FIELD = [
        'version', 'tranCode', 'merOrderId', 'merId', 'charset',
        'signType', 'hnapayOrderId', 'resultCode', 'errorCode',
    ];

    private $version;
    private $tranCode;
    private $merId;
    private $merOrderId;
    private $submitTime;
    private $msgCiphertext;
    private $signType;
    private $signValue;
    private $merAttach;
    private $charset;

    private $payCancelInfoModel;

    public function __construct()
    {
        $this->version = self::VERSION;
        $this->tranCode = self::TRANCODE;
        $this->signType = self::SIGNTYPE_RSA;
        $this->charset = self::CHARSET_UTF8;
        $this->submitTime = date('YmdHis', time());
        $this->merAttach = '';

        $this->payCancelInfoModel = new PayCancelInfoModel();
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

        $this->payCancelInfoModel->copy($data);

        $this->msgCiphertext = $this->payCancelInfoModel->getMsgCipherText($this->publicKey);

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
        return RsaUtil::verifySignForBase64($responseData['signValue'], $this->publicKey,
            Util::getStringData(self::VERIFY_FIELD, $responseData));
    }
}
