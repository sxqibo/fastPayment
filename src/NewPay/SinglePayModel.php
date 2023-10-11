<?php

namespace Sxqibo\FastPayment\NewPay;

use ReflectionClass;

final class SinglePayModel
{
    const REQUEST_URL = 'https://gateway.hnapay.com/website/singlePay.do';

    /** @var string 版本号 */
    const VERSION = '2.1';

    /** @var string 交易代码  */
    const TRANCODE = 'SGP01';

    /** @var string 编码方式 */
    /** @var string UTF-8 */
    const CHARSET = '1';

    /** @var string 签名类型 */
    /** @var string RSA */
    const SIGNTYPE_RSA = '1';

    /** @var string[] 签名字段 */
    const SIGN_FIELD =  ['version', 'tranCode',
        'merId', 'merOrderId', 'submitTime', 'msgCiphertext',
        'signType'];

    /** @var string[] 验签字段 */
    const VERIFY_FIELD =  ['version', 'tranCode',
        'merOrderId', 'merId',
        'charset', 'signType',
        'resultCode', 'hnapayOrderId'];

    /** @var \string[][] 判断不能为空的字段 */
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

    private $privateKey;
    private $publicKey;

    private $singlePayInfoMdoel;

    public function __construct()
    {
        $this->version = self::VERSION;
        $this->tranCode = self::TRANCODE;
        $this->signType = self::SIGNTYPE_RSA;
        $this->charset = self::CHARSET;
        $this->submitTime = date('YmdHis', time());

        $this->singlePayInfoMdoel = new SinglePayInfoModel();
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
        $this->merId = $data['merId'];
        $this->merOrderId = $data['merOrderId'];

        unset($data['merId']);
        unset($data['merOrderId']);

        $this->singlePayInfoMdoel->copy($data);
    }

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

    public function getPayInfo(): array
    {
        return $this->singlePayInfoMdoel->getData();
    }

    public function getSignData(): string
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
