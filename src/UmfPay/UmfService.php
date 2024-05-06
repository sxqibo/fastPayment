<?php

namespace Sxqibo\FastPayment\UmfPay;

use Sxqibo\FastPayment\Common\HttpUtil;


/**
 *
 */
class UmfService
{
    private $payRequestUrl = 'http://pay.soopay.net/spay/pay/payservice.do';
    private $charset = 'UTF-8';
    private $signType = 'RSA';
    private $resFormat = 'HTML';
    private $version = '4.0';
    private $merId;
    private $platformPublicKey;
    private $merchantPrivateKey;

    /**
     * 构造函数
     * @param $paramMerId
     * @param $paramPlatformPublicKey
     * @param $paramMerchantPrivateKey
     * @throws \Exception
     */
    public function __construct($paramMerId, $paramPlatformPublicKey, $paramMerchantPrivateKey)
    {
        $this->merId = $paramMerId;
        if (!$paramPlatformPublicKey) {
            throw new \Exception('没有平台公钥');
        }
        if (!$paramMerchantPrivateKey) {
            throw new \Exception('没有商户私钥');
        }
        $this->platformPublicKey  = $paramPlatformPublicKey;
        $this->merchantPrivateKey = $paramMerchantPrivateKey;
    }

    /**
     * 发起API请求
     * @param $requestParams
     * @return array|string
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Exception
     */
    public function submit($requestParams)
    {
        $defaultParams = [
            'charset'    => $this->charset,
            'sign_type'  => $this->signType,
            'res_format' => $this->resFormat,
            'version'    => $this->version,
            'amt_type'   => 'RMB',
            'mer_id'     => $this->merId,
        ];

        $params         = array_merge($defaultParams, $requestParams);
        $params         = $this->doEncrypt($params);
        $params['sign'] = $this->generateSign($params);

        $response = HttpUtil::post($params, $this->payRequestUrl);
        if (!$response) {
            throw new \Exception('请求接口失败或网络连接异常');
        }
        //对账直接返回
        if ($params["service"] == "download_settle_file") {
            return $response;
        }
        return $this->parseHtmlStr($response);
    }

    /**
     * 获取支付跳转链接
     * @param $params
     * @return string
     * @throws \Exception
     */
    public function getPayUrl($requestParams): string
    {
        $defaultParams = [
            'charset'    => $this->charset,
            'sign_type'  => $this->signType,
            'res_format' => $this->resFormat,
            'version'    => $this->version,
            'amt_type'   => 'RMB',
            'mer_id'     => $this->merId,
        ];

        $params         = array_merge($defaultParams, $requestParams);
        $params         = $this->doEncrypt($params);
        $params['sign'] = $this->generateSign($params);

        return $this->payRequestUrl . '?' . http_build_query($params);
    }

    /**
     * 解析HTML字符串
     * @param $htmlStr
     * @return array
     * @throws \Exception
     */
    private function parseHtmlStr($htmlStr): array
    {
        preg_match('/<META\s+name="MobilePayPlatform"\s+content="([\w\W]*?)"/si', $htmlStr, $matches);
        $content = $matches[1];
        if (!$content) {
            throw new \Exception('平台返回html解析失败');
        }
        $params     = [];
        $paramPairs = explode('&', $content);
        foreach ($paramPairs as $str) {
            $arr             = explode('=', $str);
            $params[$arr[0]] = $arr[1];
        }
        //if(!$this->verifySign($params)){
        //	throw new \Exception('平台响应数据验证签名失败');
        //}
        return $params;
    }

    /**
     * 响应字符串
     * @param $params
     * @return false|string
     * @throws \Exception
     */
    public function responseUmfStr($requestParams)
    {
        $defaultParams = [
            'sign_type' => $this->signType,
            'version'   => $this->version,
            'mer_id'    => $this->merId,
            'ret_code'  => '0000',
            'ret_msg'   => 'success'
        ];

        $params         = array_merge($defaultParams, $requestParams);
        $params['sign'] = $this->generateSign($params);
        $str            = '';
        foreach ($params as $key => $value) {
            $str .= $key . '=' . $value . '&';
        }
        return substr($str, 0, -1);
    }

    /**
     * 获取待签名字符串
     * @param $param
     * @return false|string
     */
    private function getSignContent($param)
    {
        ksort($param);
        $signStr = '';

        foreach ($param as $k => $v) {
            if ($k != "sign" && $k != "sign_type" && $v != '') {
                $signStr .= $k . '=' . $v . '&';
            }
        }
        return substr($signStr, 0, -1);
    }

    /**
     * 请求参数签名
     * @param $param
     * @return string
     * @throws \Exception
     */
    private function generateSign($param): string
    {
        return $this->rsaPrivateSign($this->getSignContent($param));
    }

    /**
     * 验签方法
     * @throws \Exception
     */
    public function verifySign($param)
    {
        if (empty($param['sign'])) return false;
        return $this->rsaPublicVerify($this->getSignContent($param), $param['sign']);
    }

    /**
     * 敏感字段加密
     * @param $param
     * @return mixed
     * @throws \Exception
     */
    private function doEncrypt($param)
    {
        $chkKeys = array(
            "card_id",
            "valid_date",
            "cvv2",
            "pass_wd",
            "identity_code",
            "card_holder",
            "recv_account",
            "recv_user_name",
            "identity_holder",
            "identityCode",
            "cardHolder",
            "mer_cust_name",
            "account_name",
            "bank_account",
            "endDate",
        );
        foreach ($chkKeys as $key) {
            if (isset($param[$key])) {
                $encrypted = $this->rsaPublicEncrypt($param[$key]);
                if (!$encrypted) {
                    throw new \Exception('加密失败，无法获取平台公钥');
                }
                $param[$key] = $encrypted;
            }
        }
        return $param;
    }

    /**
     * 商户私钥签名
     * @param $data
     * @return string
     * @throws \Exception
     */
    private function rsaPrivateSign($data): string
    {
        $pKeyId = openssl_get_privatekey($this->merchantPrivateKey);
        if (!$pKeyId) {
            throw new \Exception('签名失败，商户私钥不正确');
        }
        openssl_sign($data, $signature, $pKeyId);
        $signature = base64_encode($signature);
        return $signature;
    }

    /**
     * 平台公钥验签
     * @param $data
     * @param $signature
     * @return false|int
     * @throws \Exception
     */
    private function rsaPublicVerify($data, $signature)
    {
        $pubKeyId = openssl_get_publickey($this->platformPublicKey);
        if (!$pubKeyId) {
            throw new \Exception('验签失败，平台公钥不正确');
        }
        return openssl_verify($data, base64_decode($signature), $pubKeyId);
    }

    /**
     * 平台公钥加密
     * @param $data
     * @return string
     * @throws \Exception
     */
    private function rsaPublicEncrypt($data): string
    {
        $pubKeyId = openssl_get_publickey($this->platformPublicKey);
        if (!$pubKeyId) {
            throw new \Exception('加密失败，平台公钥不正确');
        }
        openssl_public_encrypt($data, $encrypted, $pubKeyId);
        $encrypted = base64_encode($encrypted);
        return $encrypted;
    }

    /**
     * 商户私钥解密
     * @param $data
     * @return string
     * @throws \Exception
     */
    private function rsaPrivateDecrypt($data): string
    {
        $pKeyId = openssl_get_privatekey($this->merchantPrivateKey);
        if (!$pKeyId) {
            throw new \Exception('解密失败，商户私钥不正确');
        }
        openssl_private_decrypt(base64_decode($data), $decrypted, $pKeyId);
        return $decrypted;
    }
}