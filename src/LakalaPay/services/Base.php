<?php
// +----------------------------------------------------------------------
// | NewThink [ Think More,Think Better! ]
// +----------------------------------------------------------------------
// | Copyright (c) 2016~2030 http://www.sxqibo.com All rights reserved.
// +----------------------------------------------------------------------
// | 版权所有：山西岐伯信息科技有限公司
// +----------------------------------------------------------------------
// | Author:  hongwei  Date:2024/8/28 Time:4:52 PM
// +----------------------------------------------------------------------

namespace Sxqibo\FastPayment\LakalaPay\services;

use GuzzleHttp\Client;
use Sxqibo\FastPayment\LakalaPay\utils\Str;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;

abstract class Base
{
    // 签名算法
    const SIGNATURE_ALGO = 'LKLAPI-SHA256withRSA';

    /**
     * 接口版本
     * @var string
     */
    protected string $apiVersion = '1.0';

    /**
     * 配置参数
     * @var array
     */
    protected array $options = [
        // appid
        'appid'       => '',
        // 商户证书序列号
        'serial_no'   => '',
        // 商户号
        'merchant_no' => '',
        // 证书私钥内容
        'private_key' => '',
        // 验签证书内容
        'certificate' => '',
        // 是否测试环境
        'test_env'    => false,
    ];

    /**
     * 生产环境接口基础Url
     * @var string
     */
    // protected string $baseUrl = 'https://s2.lakala.com';
    protected string $baseUrl = 'https://jsmch.xyvcard.com/prod-api/lklpay'; // 拉卡拉技术对接人员说用这个，不知道为什么和官方文档中的不一致

    /**
     * 测试环境接口基础Url
     * @var string
     */
    protected string $baseUrlTest = 'https://test.wsmsd.cn/sit';

    /**
     * 构造方法
     * @access public
     * @param array $options 配置参数
     * @return void
     */
    public function __construct(array $options)
    {
        // 合并配置
        $this->options = array_merge($this->options, $options);

        // 获取证书私钥内容
        if (isset($this->options['private_key']) && preg_match('/\.pem$/s', $this->options['private_key']) && file_exists($this->options['private_key'])) {
            $this->options['private_key'] = file_get_contents($this->options['private_key']);
        }
        // 获取异步通知验签证书内容
        if (isset($this->options['certificate']) && preg_match('/\.cer$/s', $this->options['certificate']) && file_exists($this->options['certificate'])) {
            $this->options['certificate'] = file_get_contents($this->options['certificate']);
        }
    }

    /**
     * 获取接口地址
     * @access protected
     * @return string
     */
    protected function getBaseUrl(): string
    {
        // 如果是测试环境
        if (isset($this->options['test_env']) && !empty($this->options['test_env'])) {
            return $this->baseUrlTest;
        }
        // 生产环境
        return $this->baseUrl;
    }

    /**
     * API请求：生成签名
     * @access protected
     * @param string $body
     * @return string
     */
    protected function getAuthorization(string $body = ''): string
    {
        // 生成12位随机字符串
        $nonceStr = Str::random(12);
        // 请求时间戳
        $timestamp = time();
        // 获取配置中的APPID
        $appid = $this->options['appid'];
        // 获取配置中的SERIAL_NO
        $serialNo = $this->options['serial_no'];
        // 构造签名报文
        $message = $appid . "\n" . $serialNo . "\n" . $timestamp . "\n" . $nonceStr . "\n" . $body . "\n";
        // 获取私钥
        $key = openssl_get_privatekey($this->options['private_key']);
        // 签名
        openssl_sign($message, $signature, $key, OPENSSL_ALGO_SHA256);

        // 拼接并返回鉴权信息
        return static::SIGNATURE_ALGO . ' ' . Str::serializeAuthData([
                'appid'     => $appid,
                'serial_no' => $serialNo,
                'timestamp' => $timestamp,
                'nonce_str' => $nonceStr,
                'signature' => base64_encode($signature),
            ]);
    }

    /**
     * API响应：校验签名
     * @access protected
     * @param array  $headers
     * @param string $body
     * @return bool
     */
    public function signatureVerification(array $headers, string $body = ''): bool
    {
        // 收集签名参数
        $signData = [
            $headers['Lklapi-Appid'][0],
            $headers['Lklapi-Serial'][0],
            $headers['Lklapi-Timestamp'][0],
            $headers['Lklapi-Nonce'][0],
            $body
        ];
        // 构造签名报文
        $message = implode("\n", $signData) . "\n";
        // 获取公钥
        $key = openssl_get_publickey($this->options['certificate']);
        // 获取校验结果
        $flag = openssl_verify($message, base64_decode($headers['Lklapi-Signature'][0]), $key, OPENSSL_ALGO_SHA256);
        // 正确
        if ($flag) {
            return true;
        }
        return false;
    }

    /**
     * 获取通知数据
     * @access public
     * @param string $authorization
     * @param string $body
     * @return array
     */
    public function getNotifyData(string $authorization, string $body = ''): array
    {
        // 校验签名
        if (true !== $this->notifySignatureVerification($authorization, $body)) {
            return [null, new \Exception('签名校验未通过')];
        }
        // 获取异步通知内容
        $notifyData = json_decode($body, true);
        // 如果没有订单ID
        if (!is_array($notifyData) || !isset($notifyData['out_order_no'])) {
            return [null, new \Exception('支付通知数据错误')];
        }
        // 返回
        return [$notifyData, null];
    }

    /**
     * 异步通知：校验签名
     * @access protected
     * @param string $authorization
     * @param string $body
     * @return bool
     */
    public function notifySignatureVerification(string $authorization, string $body = ''): bool
    {
        // 过滤算法标识
        $authorization = trim(str_replace(static::SIGNATURE_ALGO, '', $authorization));
        // 反序列化获取鉴权字段
        $authData = Str::unserializeAuthData($authorization);
        // 构造签名报文
        $message = $authData['timestamp'] . "\n" . $authData['nonce_str'] . "\n" . $body . "\n";
        // 获取公钥
        $key = openssl_get_publickey($this->options['certificate']);
        // 获取校验结果
        $flag = openssl_verify($message, base64_decode($authData['signature']), $key, OPENSSL_ALGO_SHA256);
        // 正确
        if ($flag) {
            return true;
        }
        return false;
    }

    /**
     * 发送POST请求
     * @access protected
     * @param string       $path 请求接口
     * @param array|string $body 请求参数
     * @return array
     * @throws GuzzleException
     */
    protected function sendPostRequest(string $path, array|string $body = []): array
    {
        // 如果是数组
        if (is_array($body)) {
            $body = json_encode($body, JSON_UNESCAPED_UNICODE);
        }

        // 获取鉴权信息
        $authorization = $this->getAuthorization($body);

        // 构造请求头
        $headers = [
            'Authorization' => $authorization,
            'Content-Type'  => 'application/json'
        ];

        $response     = (new Client())->request('POST', $this->getBaseUrl() . $path, [
            'headers' => $headers,
            'body'    => $body
        ]);
        $responseCode = $response->getStatusCode();
        $responseBody = $response->getBody();
        if ($responseCode == 200) {
            $responseData = json_decode($responseBody, true);
            if ($responseData['code'] <> '000000') {
                throw new \LogicException("错误信息：{$responseData['msg']}，错误码：{$responseData['code']}");
            }

            // 验签
            /* 验签关闭，原因：拉卡拉API测试过程中发现，有时候不返回验签需要的header参数
            $responseHeaders = $response->getHeaders();
            if (!$this->signatureVerification($responseHeaders, $responseBody)) {
                throw new \InvalidArgumentException("验签失败");
            }
            */

            return $responseData['resp_data'];

        } else {

            throw new RequestException($responseBody, $responseCode);
        }
    }
}
