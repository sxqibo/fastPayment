<?php

namespace Sxqibo\FastPayment\NewPay;

use Exception;

/**
 * 类说明：对应新生支付文档 5.5
 * 文档地址：https://www.yuque.com/chenyanfei-sjuaz/uhng8q/gbxazy
 */
final class QueryModel extends BaseModel
{
    const REQUEST_URL = 'https://gateway.hnapay.com/exp/query.do';

    /** 版本 */
    const VERSION = '2.0';

    /** 交易代码 */
    const TRANCODE = 'EXP08';

    const IS_NOT_FIELD = [
        ['merId', '商户ID 不能为空'],
        ['merOrderId', '原商户订单号 不能为空'],
        ['submitTime', '原商户订单请求时间 不能为空'],
    ];

    /** 签名字段  */
    const SIGN_FIELD = ['version', 'tranCode',
        'merId', 'merOrderId', 'submitTime'
    ];

    /** 验签字段 */
    const VERIFY_FIELD = [
        'version', 'tranCode',
        'merOrderId', 'merId',
        'charset', 'signType',
        'resultCode', 'errorCode', 'hnapayOrderId',
        'tranAmt', 'refundAmt', 'orderStatus'
    ];

    /** 版本号 */
    private $version;
    /** 交易代码 */
    private $tranCode;
    /** 商户ID */
    private $merId;
    /** 原商户订单号 */
    private $merOrderId;
    /** 原商户订单请求时间 */
    private $submitTime;
    /** 签名类型 */
    private $signType;
    /** 签名密文串 */
    private $signValue;
    /** 附加数据 */
    private $merAttach;
    /** 编码方式 */
    private $charset;

    public function __construct()
    {
        $this->version = self::VERSION;
        $this->tranCode = self::TRANCODE;
        $this->signType = self::SIGNTYPE_RSA;
        $this->charset = self::CHARSET_UTF8;
        $this->merAttach = '';
    }

    /**
     * 数据赋值
     *
     * @param $data
     * @return void
     * @throws Exception
     */
    public function copy($data)
    {
        foreach ($data as $key => $value) {
            $this->$key = $value;
        }

        $err = $this->verify();
        if (!empty($err)) {
            throw new Exception($err);
        }

        $this->signValue = RsaUtil::buildSignForBase64($this->getSignData(), $this->privateKey);
    }

    public function getModelData(): array
    {
        return parent::getData(__CLASS__, $this);
    }

    /**
     * @throws Exception
     */
    public function getSignData(): string
    {
        return Util::getStringData(self::SIGN_FIELD, $this->getModelData());
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

    public function verifySign($responseData): bool
    {
        return RsaUtil::verifySignForBase64(
            $responseData['signValue'],
            $this->publicKey,
            Util::getStringData(self::VERIFY_FIELD, $responseData));
    }
}
