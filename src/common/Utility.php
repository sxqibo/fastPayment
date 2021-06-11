<?php

namespace Sxqibo\FastPayment\common;


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
}
