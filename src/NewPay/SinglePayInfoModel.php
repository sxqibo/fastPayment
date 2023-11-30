<?php

namespace Sxqibo\FastPayment\NewPay;

/**
 * 5.4 付款到银行
 *
 * 文档地址：https://www.yuque.com/chenyanfei-sjuaz/uhng8q/ccdtg7
 */
final class SinglePayInfoModel extends BaseModel
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

    private $config = [
        'notifyUrl' => 'http://xxx.xxx.com/xxx',
        'paymentTerminalInfo' => '01|10001',
        'deviceInfo' => '192.168.0.1||||||',
    ];

    public function __construct(array $config = [])
    {
        if (!empty($config)) {
            $this->config = $config;
        }

        $this->notifyUrl = $this->config['notifyUrl'];
        $this->paymentTerminalInfo = $this->config['paymentTerminalInfo'];
        $this->deviceInfo = $this->config['deviceInfo'];
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

    public function getMsgCipherText($publicKey)
    {
        $msgText = $this->getModelData();

        // 付款公司钥/网关公钥(付款).pem
        $msgCiphertext = json_encode($msgText, JSON_UNESCAPED_UNICODE);

        return $this->publicEncrypt($msgCiphertext, $publicKey);
    }

    /**
     * 付款detail的加密
     *
     * @param $input
     * @param $pk
     * @return false|string
     */
    public function publicEncrypt($input, $pk)
    {
        $split = str_split($input, 117);

        $crypto = '';

        foreach ($split as $chunk) {
            $isOkey = openssl_public_encrypt($chunk, $output, $pk, OPENSSL_PKCS1_PADDING);
            if (!$isOkey) {
                return false;
            }
            $crypto .= $output;
        }

        return base64_encode($crypto);
    }

    public function getModelData(): array
    {
        return parent::getData(__CLASS__, $this);
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
