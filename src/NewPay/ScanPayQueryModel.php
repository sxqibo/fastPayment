<?php

namespace Sxqibo\FastPayment\NewPay;

use ReflectionClass;

/**
 *
 */
final class ScanPayQueryModel
{
    const REQUEST_URL = 'https://gateway.hnapay.com/website/queryOrderResult.htm';

    /** @var string 版本 */
    const VERSION = "2.8";

    /** @var string 查询模式 */
    /** @var string 单笔 */
    const MODE_SINGLE = '1';
    /** @var string 批量 */
    const MODE_MULTI = '2';

    /** @var string 查询类型 */
    /** @var string 支付订单 */
    const TYPE_PAY = '1';
    /** @var string 退款订单 */
    const TYPE_REFUND = '2';

    /** @var string 编码方式 */
    /** @var string UTF-8 */
    const CHARSET = '1';

    /** @var string 签名类型 */
    /** @var string RSA */
    const SIGNTYPE_RSA = '1';

    /** @var string[] queryDetail的字段 */
    private $queryDetail = [
        'orderID', 'orderAmount', 'payAmount', 'acquiringTime',
        'completeTime', 'orderNo', 'stateCode', 'respCode',
        'respMsg', 'targetOrderId', 'vasType', 'vasOrderId',
        'vasFeeAmt', 'realBankOrderId', 'userId', 'buyerLogonId'
    ];

    /** @var string[] 签名字段 */
    const SIGN_FIELD =  [
        'version', 'serialID', 'mode', 'type', 'orderID', 'beginTime',
        'endTime', 'partnerID', 'remark', 'charset', 'signType'
    ];

    private $version = '';
    private $serialID = '';
    private $mode = '';
    private $type = '';
    private $orderID = '';
    private $beginTime = '';
    private $endTime = '';
    private $partnerID = '';
    private $remark = '';
    private $charset = '';
    private $signType = '';
    private $signMsg = '';

    private $privateKey;

    public function __construct()
    {
        $this->version = self::VERSION;
        $this->charset = self::CHARSET;
        $this->signType = self::SIGNTYPE_RSA;
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

    /**
     * 获取需要签名的数据
     *
     * @return string
     */
    public function getSignData()
    {
        $fields = [];

        foreach (self::SIGN_FIELD as $field) {
            $fields[$field] = $this->$field;
        }
        // var_dump($fields);
        return http_build_query($fields);
    }

    public function verify()
    {
        if (empty($this->serialID)) {
            return '请求序列号不能为空';
        }

        if (empty($this->mode)) {
            return '查询模式不能为空';
        }

        if ($this->mode != self::MODE_SINGLE
            && $this->mode != self::MODE_MULTI) {
            return '查询模式的取值不正确';
        }

        if ($this->mode == self::MODE_SINGLE && empty($this->orderID)) {
            return '单笔查询必须传入 orderID';
        }

        if ($this->mode == self::MODE_MULTI) {
            $this->orderID = '';
        }

        if (empty($this->type)) {
            return '查询类型不能为空';
        }

        if ($this->type != self::TYPE_PAY
            && $this->type != self::TYPE_REFUND) {
            return '询类型的取值不正确';
        }

        if (empty($this->partnerID)) {
            return '商户ID不能为空';
        }

        return '';
    }
}
