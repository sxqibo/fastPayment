<?php

namespace Sxqibo\FastPayment\NewPay;

use ReflectionClass;

abstract class BaseModel
{
    /** 签名类型 */
    /** RSA */
    const SIGNTYPE_RSA = '1';

    /** 编码方式 */
    /** UTF-8 */
    const CHARSET_UTF8 = '1';

    protected $publicKey;
    protected $privateKey;

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

    public function getData($clazz, $obj): array
    {
        $data = [];

        $reflectionClass = new ReflectionClass($clazz);
        $reflectionProperties = $reflectionClass->getProperties();

        foreach ($reflectionProperties as $property) {
            $propertyName = $property->getName();
            $property->setAccessible(true);
            $data[$propertyName] = $property->getValue($obj);
        }

        unset($data['privateKey']);
        unset($data['publicKey']);
        unset($data['config']);

        return $data;
    }
}
