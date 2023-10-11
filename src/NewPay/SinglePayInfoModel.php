<?php

namespace Sxqibo\FastPayment\NewPay;

use ReflectionClass;

final class SinglePayInfoModel
{
    /** @var string 付款类型 */
    /** @var string 付款到银行 */
    const PAYTYPE_BANK = '1';
    /** @var string 付款到账户 */
    const PAYTYPE_ACCCOUNT = '2';

    /** @var string 是否需要复核 */
    /** @var string 不需要 */
    const AUDITFLAG_NO = '0';
    /** @var string 需要 */
    const AUDITFLAG_YES = '1';

    /** @var string 收款方类型 */
    /** @var string 个人 */
    const PAYEETYPE_PERSON = '1';
    /** @var string 企业 */
    const PAYEETYPE_COMPANY = '2';

    const IS_NOT_FIELD = [
        ['tranAmt', '支付金额 不能为空'],
        ['payType', '付款类型 不能为空'],
        ['payeeName', '收款方姓名 不能为空'],
        ['payeeAccount', '收款方账户 不能为空'],
        ['payeeType', '收款方类型 不能为空'],
    ];

    private $tranAmt = '';
    private $payType = '';
    private $auditFlag = '';
    private $payeeName = '';
    private $payeeAccount = '';
    private $note = '';
    private $remark = '';
    private $bankCode = '';
    private $payeeType = '';
    private $notifyUrl = '';
    private $paymentTerminalInfo = '';
    private $deviceInfo = '';

    public function __construct()
    {
        $this->notifyUrl = 'http://xxx.xxx.com/xxx';
        $this->paymentTerminalInfo = '01|10001';
        $this->deviceInfo = '192.168.0.1||||||';
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

        if (empty($this->auditFlag) && $this->auditFlag != '0') {
            return '是否需要复核 不能为空';
        }

        if ($this->payType == self::PAYTYPE_BANK
            && $this->payeeType == self::PAYEETYPE_COMPANY) {
            if (empty($this->bankCode)) {
                return '银行简码 不能为空';
            }
        }

        return '';
    }
}
