<?php

namespace Sxqibo\FastPayment\WePay;

use Sxqibo\FastPayment\Common\Client;
use GuzzleHttp\Exception\RequestException;
use Exception;
use GuzzleHttp\HandlerStack;
use WechatPay\GuzzleMiddleware\WechatPayMiddleware;
use WechatPay\GuzzleMiddleware\Util\PemUtil;

class BaseService
{
    protected $uri;
    protected $header;
    protected $appid;
    protected $merchantId;
    protected $client;

    /**
     * 初始化信息
     *
     * BaseService constructor.
     * @param $merchantId
     * @param $merchantSerialNumber
     * @param $merchantPrivateKey
     * @param $wechatpayCertificate
     */
    public function __construct($merchantId, $merchantSerialNumber, $merchantPrivateKey, $wechatpayCertificate)
    {
        try {
            $this->appid = $merchantId;
            // 商户API证书序列号
            $merchantPrivateKey = PemUtil::loadPrivateKey($merchantPrivateKey); // 商户私钥
            // 微信支付平台配置
            $wechatpayCertificate = PemUtil::loadCertificate($wechatpayCertificate); // 微信支付平台证书

            // 构造一个WechatPayMiddleware
            $wechatpayMiddleware = WechatPayMiddleware::builder()
                ->withMerchant($merchantId, $merchantSerialNumber, $merchantPrivateKey) // 传入商户相关配置
                ->withWechatPay([$wechatpayCertificate]) // 可传入多个微信支付平台证书，参数类型为array
                ->build();

            $this->uri     = 'https://api.mch.weixin.qq.com/v3';
            $this->headers = [
                'Content-Type' => 'application/json',
                'Accept'       => 'application/json'
            ];

            // 传入$wechatpayMiddleware中间件
            $this->client = new Client($wechatpayMiddleware);
        } catch (RequestException $e) {
            // todo 优化
            echo $e->getMessage() . "\n";
            if ($e->hasResponse()) {
                echo $e->getResponse()->getStatusCode() . ' ' . $e->getResponse()->getReasonPhrase() . "\n";
                echo $e->getResponse()->getBody();
            }
            return;
            // throw new Exception('初始化实例异常');
        }
    }

    // todo
    public function handleResult($result)
    {
        return [];
    }
}

