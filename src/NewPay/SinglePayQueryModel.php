<?php

namespace Sxqibo\FastPayment\NewPay;

use ReflectionClass;

final class SinglePayQueryModel
{
    const REQUEST_URL = 'https://gateway.hnapay.com/website/singlePayQuery.do';

    /** @var string 版本号 */
    const VERSION = '2.0';

    /** @var string 交易代码 */
    const TRANCODE = 'SGP02';

    /** @var string 签名类型 */
    /** @var string RSA */
    const SIGNTYPE_RSA = '1';

    /** @var string 编码方式 */
    /** @var string UTF-8 */
    const CHARSET = '1';

    private $version = '';
    private $tranCode = '';
    private $merId = '';
    private $merOrderId = '';
    private $submitTime = '';
    private $signType = '';
    private $signValue = '';
    private $merAttach = '';
    private $charset = '';

    const SIGN_FIELD = ['version',
        'tranCode', 'merId', 'merOrderId',
        'submitTime'];

    const VERIFY_FIELD = [
        'version', 'tranCode', 'merOrderId',
        'merId', 'charset', 'signType',
        'resultCode', 'errorCode', 'hnapayOrderId',
        'tranAmt', 'orderStatus'
    ];

    const IS_NOT_FIELD = [
        ['merId', '商户ID 不能为空'],
        ['merOrderId', '原商户订单号 不能为空'],
        ['submitTime', '原商户订单请求时间 不能为空'],
    ];

    private $privateKey;
    private $publicKey;

    public function __construct()
    {
        $this->version = self::VERSION;
        $this->tranCode = self::TRANCODE;
        $this->signType = self::SIGNTYPE_RSA;
        $this->charset = self::CHARSET;
    }

    public function setPublicKey(string $publicKey)
    {
        // 付款公司钥/网关公钥(付款).pem
        $this->publicKey = KeyUtils::makePublicKey($publicKey);
    }

    public function setPrivateKey(string $privateKey)
    {
        // 付款公私钥/商户私钥（付款）.pem
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

    public function verify(): string
    {
        foreach (self::IS_NOT_FIELD as $field) {
            $name = $field[0];
            if (empty($this->$name)) {
                return $field[1];
            }
        }

        return '';
    }
}
