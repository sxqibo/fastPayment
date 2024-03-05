<?php

namespace Sxqibo\FastPayment\NewPay;

/**
 * 5.3 支付宝H5
 *
 * https://www.yuque.com/chenyanfei-sjuaz/uhng8q/efqwi8
 */
final class H5Model extends BaseModel
{
    const REQUEST_URL = 'https://gateway.hnapay.com/multipay/h5.do';

    /** @var string 版本 */
    const VERSION = '2.0';

    /** @var string 交易代码 */
    const TRAN_CODE = 'MUP11';

    /** @var string 付款方式 */
    const PAY_TYPE = 'HnaALL';

    const IS_NOT_FIELD = [
        ['merId', '商户ID 不能为空'],
        ['merOrderId', '商户订单号 不能为空'],
    ];

    /** @var string[] 签名字段 */
    const SIGN_FIELD =  [
        'version', 'tranCode', 'merId', 'merOrderId',
        'submitTime', 'signType', 'charset', 'msgCiphertext',
    ];

    /** @var string[] 验签字段 */
    const VERIFY_FIELD = [
        'version', 'tranCode', 'merOrderId', 'merId',
        'charset', 'signType', 'resultCode', 'hnapayOrderId',
    ];

    private $version;
    private $tranCode;

    private $payType;
    private $merId;
    private $merOrderId;
    private $submitTime;
    private $msgCiphertext;
    private $signType;
    private $signValue;
    private $merAttach;
    private $charset;

    private $h5InfoModel;

    public function __construct()
    {
        $this->version = self::VERSION;
        $this->tranCode = self::TRAN_CODE;
        $this->payType = self::PAY_TYPE;
        $this->signType = self::SIGNTYPE_RSA;
        $this->charset = self::CHARSET_UTF8;
        $this->submitTime = date('YmdHis', time());
        $this->merAttach = '';
        $this->h5InfoModel = new H5InfoModel();
    }

    /**
     * 数据赋值
     *
     * @param $data
     * @return void
     * @throws \Exception
     */
    public function copy($data)
    {
        $this->merId = $data['merId'];
        $this->merOrderId = $data['merOrderId'];

        unset($data['merId']);
        unset($data['merOrderId']);

        $this->h5InfoModel->copy($data);

        $this->msgCiphertext = $this->h5InfoModel->getMsgCipherText($this->publicKey);

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

    public function verifySign($responseData)
    {
        return RsaUtil::verifySignForBase64($responseData['signValue'], $this->publicKey,
            Util::getStringData(self::VERIFY_FIELD, $responseData));
    }
}
