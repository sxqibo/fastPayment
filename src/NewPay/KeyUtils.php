<?php

namespace Sxqibo\FastPayment\NewPay;

final class KeyUtils
{
    /**
     * 私钥格式化
     *
     * @param $privateKey
     * @return string
     */
    public static function makePrivateKey($privateKey): string
    {
        return "-----BEGIN PRIVATE KEY-----\n"
            . wordwrap($privateKey, 64, "\n", true)
            . "\n-----END PRIVATE KEY-----";
    }

    /**
     * 公钥格式化
     *
     * @param $publicKey
     * @return string
     */
    public static function makePublicKey($publicKey): string
    {
        return "-----BEGIN PUBLIC KEY-----\n"
            . wordwrap($publicKey, 64, "\n", true)
            . "\n-----END PUBLIC KEY-----";
    }
}
