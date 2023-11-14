<?php

namespace Sxqibo\FastPayment\NewPay;

use ReflectionClass;

final class RefundInfoModel
{
    const IS_NOT_FIELD = [
        ['orgMerOrderId', '原商户支付订单号 不能为空'],
        ['orgSubmitTime', '原订单支付下单请求时间 不能为空'],
        ['orderAmt', '原订单金额 不能为空'],
        ['refundOrderAmt', '退款金额 不能为空'],
        ['notifyUrl', '商户异步通知地址 不能为空'],
    ];

    private $orgMerOrderId;
    private $orgSubmitTime;
    private $orderAmt;
    private $refundOrderAmt;
    private $notifyUrl;

    public function __construct()
    {
        $this->notifyUrl = 'http://xxx.xxx.com/xxx';
    }

    public function __get($name)
    {
        return $this->$name;
    }

    public function __set($name, $value)
    {
        return $this->$name = $value;
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

    public function verify()
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
