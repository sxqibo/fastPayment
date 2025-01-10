<?php

namespace Sxqibo\FastPayment\NewPay;

/**
 * 5.11 商户账户余额查询接口
 *
 * @doc https://www.yuque.com/chenyanfei-sjuaz/uhng8q/uo3eggdth1m3w4f6
 */
final class BalanceModel extends BaseModel
{
    const REQUEST_URL = 'https://gateway.hnapay.com/merchant/acct/queryBalance.do';

    /** @var string 版本 */
    const VERSION = '2.0';

    /** @var string 交易代码 */
    const TRANCODE = 'QB01';

    const IS_NOT_FIELD = [
        ['merId', '商户ID 不能为空'],
    ];

    /** @var string[] 签名字段 */
    const SIGN_FIELD = [
        'version', 'tranCode', 'merId', 'acctType', 'charset', 'signType',
    ];

    /** @var string[] 验签字段 */
    const VERIFY_FIELD = [
        'version', 'tranCode', 'merId',
        'acctType', 'charset', 'signType',
        'resultCode', 'errorCode', 'avaBalance',
    ];

    private $version;  // 版本号
    private $tranCode;  // 交易代码
    private $merId;  // 商户ID
    private $acctType; // 账户类型
    private $remark;  // 备注
    private $signType; // 签名类型
    private $signValue; // 签名密文串
    private $charset; // 编码方式

    public function __construct()
    {
        $this->version  = self::VERSION;
        $this->tranCode = self::TRANCODE;
        $this->acctType = '';
        $this->remark   = '';
        $this->signType = self::SIGNTYPE_RSA;
        $this->charset  = self::CHARSET_UTF8;
    }

    /**
     * 数据赋值
     *
     * @param $data
     * @return void
     */
    public function copy($data)
    {
        $this->merId      = $data['merId'];

        unset($data['merId']);

        try {
            $this->signValue = RsaUtil::buildSignForBase64($this->getSignData(), $this->privateKey);
        } catch (\Exception $e) {
            throw $e->getCode();
        }
    }

    /**
     * 获取数据
     * @return array
     */
    public function getModelData(): array
    {
        return parent::getData(__CLASS__, $this);
    }

    /**
     * 获取签名数据
     * @return string
     */
    public function getSignData(): string
    {
        try {
            return Util::getStringData(self::SIGN_FIELD, $this->getModelData());
        } catch (\Exception $e) {
            throw $e->getCode();
        }
    }

    /**
     * 验证数据
     * @return string
     */
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

    /**
     * 验证签名
     * @param $responseData
     * @return bool
     */
    public function verifySign($responseData): bool
    {
        try {
            return RsaUtil::verifySignForBase64(
                $responseData['signValue'],
                $this->publicKey,
                Util::getStringData(self::VERIFY_FIELD, $responseData)
            );
        } catch (\Exception $e) {
            throw $e->getCode();
        }
    }
}
