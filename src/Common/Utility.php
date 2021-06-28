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
     * 获取加密签名
     *
     * @param $str
     * @param $publicKeyPath string 平台证书路径
     * @return string
     */
    public static function getEncryptData($str, $certpublic)
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

    /**
     * 获取解密数据
     *
     * @param $encryptData
     * @param $certpublic
     * @return string
     * @throws \Exception
     */
    public static function getDecryptData($encryptData, $certpublic)
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

        $decryptData = '';
        if (openssl_public_decrypt($encryptData, $decryptData, $publicKey)) {
            return $decryptData;
        } else {
            throw new \Exception('decrypt failed');
        }
    }

    /**
     * 产生随机字符串
     * @param int $length 指定字符长度
     * @param string $str 字符串前缀
     * @return string
     */
    public static function createNoncestr($length = 32, $str = "")
    {
        $chars = "abcdefghijklmnopqrstuvwxyz0123456789";
        for ($i = 0; $i < $length; $i++) {
            $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
        }
        return $str;
    }

    /**
     * 数组转XML内容
     * @param array $data
     * @return string
     */
    public static function arr2xml($data)
    {
        return "<xml>" . self::_arr2xml($data) . "</xml>";
    }

    /**
     * XML内容生成
     * @param array $data 数据
     * @param string $content
     * @return string
     */
    private static function _arr2xml($data, $content = '')
    {
        foreach ($data as $key => $val) {
            is_numeric($key) && $key = 'item';
            $content .= "<{$key}>";
            if (is_array($val) || is_object($val)) {
                $content .= self::_arr2xml($val);
            } elseif (is_string($val)) {
                $content .= '<![CDATA[' . preg_replace("/[\\x00-\\x08\\x0b-\\x0c\\x0e-\\x1f]/", '', $val) . ']]>';
            } else {
                $content .= $val;
            }
            $content .= "</{$key}>";
        }
        return $content;
    }

    /**
     * 解析XML内容到数组
     * @param string $xml
     * @return array
     */
    public static function xml2arr($xml)
    {
        $entity = libxml_disable_entity_loader(true);
        $data = (array)simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA);
        libxml_disable_entity_loader($entity);
        return json_decode(json_encode($data), true);
    }

}
