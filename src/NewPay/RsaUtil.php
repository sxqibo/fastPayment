<?php

namespace Sxqibo\FastPayment\NewPay;

final class RsaUtil
{
    /**
     * 生成签名
     *  发送数据时的签名
     *
     * @param $request
     * @param $privateKey
     * @return string
     * @throws \Exception
     */
    public static function buildSignForBase64($request, $privateKey): string
    {
        // 付款公私钥/商户私钥（付款）.pem
        // 计算签名
        $res = openssl_get_privatekey($privateKey);

        openssl_sign($request, $signature, $res, OPENSSL_ALGO_SHA1);

        return base64_encode($signature);
    }


    /**
     * 生成签名
     *  发送数据时的签名
     *
     * @param $request
     * @param $privateKey
     * @return string
     * @throws \Exception
     */
    public static function buildSignForBin2Hex($request, $privateKey): string
    {
        $res = openssl_get_privatekey($privateKey);
        openssl_sign($request, $signature, $res, OPENSSL_ALGO_SHA1);

        return bin2hex($signature);
    }
}
