<?php

namespace Sxqibo\FastPayment\NewPay;

/**
 * 5.4 付款到银行
 *
 * 文档地址：https://www.yuque.com/chenyanfei-sjuaz/uhng8q/ccdtg7
 */
final class SinglePayModel extends BaseModel
{
    const REQUEST_URL = 'https://gateway.hnapay.com/website/singlePay.do';

    /** @var string 版本号 */
    const VERSION = '2.1';

    /** @var string 交易代码  */
    const TRANCODE = 'SGP01';

    /** @var string[] 签名字段 */
    const SIGN_FIELD =  ['version', 'tranCode',
        'merId', 'merOrderId', 'submitTime', 'msgCiphertext',
        'signType'];

    /** @var string[] 验签字段 */
    const VERIFY_FIELD =  ['version', 'tranCode',
        'merOrderId', 'merId',
        'charset', 'signType',
        'resultCode', 'hnapayOrderId'];

    /** @var string[][] 判断不能为空的字段 */
    const IS_NOT_FIELD = [
        ['merId', '商户ID 不能为空'],
        ['merOrderId', '商户订单号 不能为空']
    ];

    private $version = '';
    private $tranCode = '';
    private $merId = '';
    private $merOrderId = '';
    private $submitTime = '';
    private $msgCiphertext = '';
    private $signType = '';
    private $signValue = '';
    private $merAttach = '';
    private $charset = '';

    private $singlePayInfoMdoel;

    public function __construct()
    {
        $this->version = self::VERSION;
        $this->tranCode = self::TRANCODE;
        $this->signType = self::SIGNTYPE_RSA;
        $this->charset = self::CHARSET_UTF8;
        $this->submitTime = date('YmdHis', time());

        $this->singlePayInfoMdoel = new SinglePayInfoModel();
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

        $this->singlePayInfoMdoel->copy($data);

        $this->msgCiphertext = $this->singlePayInfoMdoel->getMsgCipherText($this->publicKey);

        // 付款公私钥/商户私钥（付款）.pem
        // 计算签名
        $this->signValue = RsaUtil::buildSignForBase64(
            $this->getSignData(), $this->privateKey);
    }


    public function getModelData(): array
    {
        return parent::getData(__CLASS__, $this);
    }

    public function getPayInfo(): array
    {
        return $this->singlePayInfoMdoel->getModelData();
    }

    public function getSignData(): string
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

        return $this->singlePayInfoMdoel->verify();
    }

    public function verifySign($responseData): bool
    {
        return RsaUtil::verifySignForBase64($responseData['signValue'],
            $this->publicKey,
            Util::getStringData(self::VERIFY_FIELD, $responseData));
    }
}
