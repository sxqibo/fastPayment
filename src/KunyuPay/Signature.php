<?php
namespace Sxqibo\FastPayment\KunyuPay;

class Signature
{
    private \OpenSSLAsymmetricKey $privateKey;
    private \OpenSSLAsymmetricKey $publicKey;

    public function __construct()
    {
    }

    public function setPrivateKey(string $privateKey): void
    {
        $this->privateKey = openssl_pkey_get_private($privateKey);
    }

    public function setPublicKey(string $publicKey): void
    {
        $this->publicKey = openssl_pkey_get_public($publicKey);
    }

    /**
     * 生成签名
     * @param array $params
     * @return string
     * @throws \Exception
     */
    public function generateSignature(array $params): string
    {
        if (empty($params))
            throw new \Exception('签名参数为空');

        if (empty($this->privateKey))
            throw new \Exception('私钥为空');

        // 将签名参数按键名进行升序排序
        ksort($params);

        // 将请求参数转换为字符串
        $data = http_build_query($params);

        // 使用私钥进行签名
        $result = openssl_sign($data, $signature, $this->privateKey);
        if ($result === false) {
            $errMsg = openssl_error_string();
            throw new \Exception($errMsg ?: '签名生成失败，原因未知');
        }

        // 将签名结果进行Base64编码，并返回
        return base64_encode($signature);
    }

    /**
     * 验证签名
     * @param array  $params
     * @param string $clientSignature
     * @return bool
     * @throws \Exception
     */
    public function verifySignature(array $params, string $clientSignature): bool
    {
        if (empty($params))
            throw new \Exception('签名参数为空');

        if (empty($this->publicKey))
            throw new \Exception('公钥为空');

        // 将签名参数按键名进行升序排序
        ksort($params);

        // 将请求参数转换为字符串
        $data = http_build_query($params);

        // 对客户端传递的签名进行Base64解码
        $clientSignature = base64_decode($clientSignature);

        // 使用公钥进行签名验证
        $result = openssl_verify($data, $clientSignature, $this->publicKey);

        return $result == 1;
    }
}
