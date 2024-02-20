<?php


namespace Sxqibo\FastPayment\NewPay;

/**
 * 5.9 微信&支付宝扫码（B扫C）
 *
 * https://www.yuque.com/chenyanfei-sjuaz/uhng8q/zokydupg793yle6v#bGx4G
 */
final class Scp03InfoModel extends BaseModel
{
    const IS_NOT_FIELD = [
        ['subMchId', '子商户报备编号 不能为空'],
        ['terminalId', '设备编号 不能为空'],
        ['terminalType', '设备类型 不能为空'],
        ['terminalAddress', '终端布放地址 不能为空'],
    ];

    private $subMchId;
    private $terminalId;
    private $terminalType;
    private $serialNum;
    private $terminalAddress;

    public function __construct()
    {
        $this->serialNum = '';
    }

    public function getModelData(): array
    {
        return parent::getData(__CLASS__, $this);
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

    public function getMsgCipherText($publicKey)
    {
        $msgText = $this->getModelData();

        $msgCiphertext = json_encode($msgText, JSON_UNESCAPED_UNICODE);

        return $this->publicEncrypt($msgCiphertext, $publicKey);
    }

    private function publicEncrypt($input, $pk)
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
}
