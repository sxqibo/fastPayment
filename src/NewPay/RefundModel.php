<?php

namespace Sxqibo\FastPayment\NewPay;

use ReflectionClass;

final class RefundModel
{
    const REQUEST_URL = 'https://gateway.hnapay.com/exp/refund.do';

    /** @var string 版本 */
    const VERSION = '2.0';

    /** @var string 交易代码 */
    const TRANCODE = 'EXP09';

    /** @var string 签名类型 */
    /** @var string RSA */
    const SIGNTYPE_RSA = '1';

    /** @var string 编码方式 */
    /** @var string UTF-8 */
    const CHARSET = '1';

    const IS_NOT_FIELD = [
        ['merId', '商户ID 不能为空'],
        ['merOrderId', '商户订单号 不能为空'],
    ];

    /** @var string[] 签名字段 */
    const SIGN_FIELD =  ['version', 'tranCode',
        'merId', 'merOrderId', 'submitTime', 'msgCiphertext',
    ];

    /** @var string[] 验签字段 */
    const VERIFY_FIELD =  ['version', 'tranCode',
        'merOrderId', 'merId',
        'charset', 'signType',
        'resultCode', 'errorCode', 'hnapayOrderId',
        'orgMerOrderId', 'refundAmt', 'orderStatus'];

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

    private $refundInfoModel;

    private $publicKey;
    private $privateKey;

    public function __construct()
    {
        $this->version = self::VERSION;
        $this->tranCode = self::TRANCODE;
        $this->signType = self::SIGNTYPE_RSA;
        $this->charset = self::CHARSET;
        $this->submitTime = date('YmdHis', time());
        $this->merAttach = '';

        $this->refundInfoModel = new RefundInfoModel();
    }

    public function setPublicKey(string $publicKey)
    {
        $this->publicKey = KeyUtils::makePublicKey($publicKey);
    }

    public function setPrivateKey(string $privateKey)
    {
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
        $this->merId = $data['merId'];
        $this->merOrderId = $data['merOrderId'];

        unset($data['merId']);
        unset($data['merOrderId']);

        $this->refundInfoModel->copy($data);
    }

    /**
     * 属性转数组
     *
     * @return array
     */
    public function getData(): array
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

    public function getRefundInfo()
    {
        return $this->refundInfoModel->getData();
    }

    public function getSignData()
    {
        return Util::getStringData(self::SIGN_FIELD, $this->getData());
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
}
