<?php

namespace Sxqibo\FastPayment\Common;


class Utility
{
    /**
     * 处理URL参数
     *
     * @param $array
     * @return string
     */
    public static function toUrlParams($array)
    {
        $buff = "";
        foreach ($array as $k => $v) {
            if ($v != "" && !is_array($v)) {
                $buff .= $k . "=" . $v . "&";
            }
        }

        $buff = trim($buff, "&");
        return $buff;
    }

    /**
     * 验证通联支付签名
     *
     * @param array $params
     * @param $publicKey
     */
    public static function validUnionPaySign(array $params, $publicKey)
    {
        $sign = $params['sign'];
        unset($params['sign']);
        ksort($params);
        $bufSignSrc = static::ToUrlParams($params);
        $publicKey  = chunk_split($publicKey, 64, "\n");
        $key        = "-----BEGIN PUBLIC KEY-----\n$publicKey-----END PUBLIC KEY-----\n";

        $result = openssl_verify($bufSignSrc, base64_decode($sign), $key);

        return $result;
    }

    /**
     * 获取微信支付加密签名
     *
     * @param $str
     * @param $publicKeyPath string 平台证书路径
     * @return string
     */
    public static function getWePayEncrypt($str, $certpublic)
    {
        //$str是待加密字符串
        if (stripos($certpublic, '-----BEGIN CERTIFICATE-----') === false) {
            if (file_exists($certpublic)) {
                $publicKey = file_get_contents($certpublic);
            } else {
                throw new \Exception("File Non-Existent -- [cert_private]");
            }
        } else {
            $publicKey = $certpublic;
        }

        $encrypted = '';
        if (openssl_public_encrypt($str, $encrypted, $publicKey, OPENSSL_PKCS1_OAEP_PADDING)) {
            //base64编码
            $sign = base64_encode($encrypted);
        } else {
            throw new \Exception('encrypt failed');
        }
        return $sign;
    }
}
