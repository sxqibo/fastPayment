<?php

namespace Sxqibo\FastPayment\NewPay;

final class RsaUtil
{
    /**
     * 生成签名
     *  发送数据时的签名
     *  付款到银行和付款到银行查询使用
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
     *  扫码付款C扫B和扫码支付查询接口使用
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

    /**
     * 对返回值验签
     *  返回结果的验签
     *  付款到银行和付款到银行查询使用
     *
     * @param $data
     * @param $publicKey
     * @return bool
     * @throws Exception
     */
    public static function verifySignForBase64($signValue, $publicKey, $signParam): bool
    {
        // 验签
        $res = openssl_get_publickey($publicKey);

        return (bool)openssl_verify($signParam, base64_decode($signValue), $res);
    }

    /**
     * 接受返回数据的验签
     *  返回结果的验签
     *  扫码付款C扫B使用
     *
     * @param $data
     * @param $publicKey
     * @return bool
     * @throws \Exception
     */
    public static function verifySignForHex2Bin($signMsg, $publicKey, $signParam): bool
    {
        $res = openssl_get_publickey($publicKey);

        return (bool)openssl_verify($signParam, hex2bin($signMsg), $res);
    }
}
