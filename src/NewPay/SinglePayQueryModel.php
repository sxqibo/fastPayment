<?php

namespace Sxqibo\FastPayment\NewPay;

use Exception;

/**
 * 5.7 查询接口-代付
 *
 * https://www.yuque.com/chenyanfei-sjuaz/uhng8q/bfcc86
 */
final class SinglePayQueryModel extends BaseModel
{
    const REQUEST_URL = 'https://gateway.hnapay.com/website/singlePayQuery.do';

    /** @var string 版本号 */
    const VERSION = '2.0';

    /** @var string 交易代码 */
    const TRANCODE = 'SGP02';

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

    public function __construct()
    {
        $this->version = self::VERSION;
        $this->tranCode = self::TRANCODE;
        $this->signType = self::SIGNTYPE_RSA;
        $this->charset = self::CHARSET_UTF8;
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
