<?php

namespace Sxqibo\FastPayment\WeChatPay;

use GuzzleHttp\HandlerStack;
use Sxqibo\FastPayment\Common\Cache;
use Sxqibo\FastPayment\Common\Client;
use GuzzleHttp\Exception\RequestException;
use Exception;
use WechatPay\GuzzleMiddleware\Util\AesUtil;
use WechatPay\GuzzleMiddleware\WechatPayMiddleware;
use WechatPay\GuzzleMiddleware\Util\PemUtil;
use InvalidArgumentException;

class BaseService
{
    protected $base    = 'https://api.mch.weixin.qq.com/v3';
    protected $headers = [
        'Content-Type' => 'application/json',
        'Accept'       => 'application/json'
    ];

    /**
     * 配置参数
     * @var array
     */
    protected $config = [
        'appid'        => '', // 微信绑定APPID，需配置
        'mch_id'       => '', // 微信商户编号，需要配置
        'mch_v3_key'   => '', // 微信商户密钥，需要配置
        'cert_private' => '', // 商户私钥内容，需要配置
        'cert_public'  => '', // 商户公钥内容,需要配置

        'cert_serial_number' => '', // 商户API证书序列号,无需配置
        'platform_public'    => '', // 平台证书，无需配置
    ];

    /**
     * 初始化信息
     *
     * BaseService constructor.
     * @param $merchantId
     * @param $merchantSerialNumber
     * @param $merchantPrivateKey
     * @param $wechatpayCertificate
     */
    public function __construct(array $options = [])
    {
        try {
            if (empty($options['mch_id'])) {
                throw new InvalidArgumentException("Missing Config -- [mch_id]");
            }
            if (empty($options['mch_v3_key'])) {
                throw new InvalidArgumentException("Missing Config -- [mch_v3_key]");
            }
            if (empty($options['cert_private'])) {
                throw new InvalidArgumentException("Missing Config -- [cert_private]");
            }
            if (empty($options['cert_public'])) {
                throw new InvalidArgumentException("Missing Config -- [cert_public]");
            }

            if (stripos($options['cert_public'], '-----BEGIN CERTIFICATE-----') === false) {
                if (file_exists($options['cert_public'])) {
                    $options['cert_public'] = file_get_contents($options['cert_public']);
                } else {
                    throw new InvalidArgumentException("File Non-Existent -- [cert_public]");
                }
            }

            if (stripos($options['cert_private'], '-----BEGIN PRIVATE KEY-----') === false) {
                if (file_exists($options['cert_private'])) {
                    $options['cert_private'] = file_get_contents($options['cert_private']);
                } else {
                    throw new InvalidArgumentException("File Non-Existent -- [cert_private]");
                }
            }

            $this->config['appid']        = isset($options['appid']) ? $options['appid'] : '';
            $this->config['mch_id']       = $options['mch_id'];
            $this->config['mch_v3_key']   = $options['mch_v3_key'];
            $this->config['cert_private'] = $options['cert_private'];
            $this->config['cert_public']  = $options['cert_public'];
            // 商户API证书序列号
            $this->config['cert_serial_number'] = openssl_x509_parse($this->config['cert_public'])['serialNumberHex'];
            if (empty($this->config['cert_serial_number'])) {
                throw new InvalidArgumentException("Failed to parse certificate public key");
            }

            // 平台证书获取
            $merchantPrivateKey                = openssl_get_privatekey($options['cert_private']); // 商户私钥
            $wechatpayCertificate              = $this->getWechatpayCertificate($merchantPrivateKey);
            $this->config['platform_public']   = $wechatpayCertificate;
            $certificateSerialNo               = PemUtil::parseCertificateSerialNo($wechatpayCertificate);
            $this->headers['Wechatpay-Serial'] = $certificateSerialNo; // 平台证书证书序列号

            // 构造一个WechatPayMiddleware
            $wechatpayCertificate = \openssl_x509_read($wechatpayCertificate); //  微信支付平台证书
            $wechatpayMiddleware  = WechatPayMiddleware::builder()
                ->withMerchant($options['mch_id'], $this->config['cert_serial_number'], $merchantPrivateKey) // 传入商户相关配置
                ->withWechatPay([$wechatpayCertificate]) // 可传入多个微信支付平台证书，参数类型为array
                ->build();

            // 传入$wechatpayMiddleware中间件
            $this->client = new Client(['wechatMiddleware' => $wechatpayMiddleware]);

        } catch (RequestException $e) {
            throw new Exception('请求异常' . $e->getMessage());
        } catch (\InvalidArgumentException $e) {
            throw new Exception($e->getMessage());
        }
    }


    /**
     * 处理返回结果
     *
     * @param $result
     * @return array
     */
    public function handleResult($result)
    {
        if (isset($result['code'])) {
            return ['code' => -1, 'code_text' => $result['code'], 'message' => $result['message'], 'data' => []];
        }

        return ['code' => 0, 'message' => '成功', 'data' => $result];
    }

    /**
     * 获取微信平台证书
     *
     * @return false|string
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    protected function getWechatpayCertificate($merchantPrivateKey)
    {
        $serialNo = $this->config['cert_serial_number']; // 商户API证书序列号
        $file     = Cache::getCache($serialNo);
        if ($file) {
            return base64_decode($file);
        }

        $wechatpayMiddleware = WechatPayMiddleware::builder()
            ->withMerchant($this->config['mch_id'], $serialNo, $merchantPrivateKey)
            ->withValidator(new NoopValidator) // NOTE: 设置一个空的应答签名验证器，**不要**用在业务请求
            ->build();

        $stack = HandlerStack::create();
        $stack->push($wechatpayMiddleware, 'wechatpay');
        // 创建Guzzle HTTP Client时，将HandlerStack传入
        $client = new \GuzzleHttp\Client(['handler' => $stack]);

        $resp = $client->request('GET', 'https://api.mch.weixin.qq.com/v3/certificates', [ // 注意替换为实际URL
            'headers' => ['Accept' => 'application/json']
        ]);

        $content = $resp->getBody()->getContents();
        $content = json_decode($content, true)['data'][0];
        // dd($content);

        $util = new AesUtil($this->config['mch_v3_key']);

        $encryptCertificate = $content['encrypt_certificate'];
        $data               = $util->decryptToString($encryptCertificate['associated_data'],
            $encryptCertificate['nonce'], $encryptCertificate['ciphertext']);

        // 存入缓存
        Cache::setCache($serialNo, base64_encode($data), 36000); // 10小时有效期

        return $data;
    }

}

