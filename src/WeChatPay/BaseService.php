<?php

namespace Sxqibo\FastPayment\WeChatPay;

use GuzzleHttp\Exception\RequestException;
use Exception;
use InvalidArgumentException;
use WeChatPay\Builder;
use WeChatPay\Crypto\Rsa;

class BaseService
{
    protected $client;

    protected $headers = [
        'Content-Type' => 'application/json',
        'Accept'       => 'application/json'
    ];

    /**
     * 配置参数
     * @var array
     */
    protected $config = [
        'appid'                       => '', // 微信-APPID
        'mch_id'                      => '', // 微信-商户编号
        'cert_private'                => '', // 商户-私钥内容
        'merchant_certificate_serial' => '', // 微信-商户API证书
        'platform_public_key'         => '', // 微信-支付平台公钥
        'public_key_id'               => '', // 微信-平台公钥ID
    ];

    /**
     * 初始化信息
     * @param array $options
     * @throws Exception
     */
    public function __construct(array $options = [])
    {
        try {
            $this->validateConfig($options);

            $this->config['appid']                       = isset($options['appid']) ? $options['appid'] : '';
            $this->config['mch_id']                      = $options['mch_id'];
            $this->config['cert_private']                = $this->loadPrivateKey($options['cert_private']);
            $this->config['merchant_certificate_serial'] = $options['merchant_certificate_serial'];
            $this->config['platform_public_key']         = $options['platform_public_key'];
            $this->config['public_key_id']               = $options['public_key_id'];

            $this->client = $this->createClient();

        } catch (RequestException $e) {
            throw new Exception('请求异常: ' . $e->getMessage());
        } catch (InvalidArgumentException $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * 验证配置参数
     * @param array $options
     * @throws InvalidArgumentException
     */
    protected function validateConfig(array $options)
    {
        if (empty($options['mch_id'])) {
            throw new InvalidArgumentException("缺少配置项 -- [mch_id]");
        }
        if (empty($options['cert_private'])) {
            throw new InvalidArgumentException("缺少配置项 -- [cert_private]");
        }
        if (empty($options['merchant_certificate_serial'])) {
            throw new InvalidArgumentException("缺少配置项 -- [merchant_certificate_serial]");
        }
        if (empty($options['platform_public_key'])) {
            throw new InvalidArgumentException("缺少配置项 -- [platform_public_key]");
        }
        if (empty($options['public_key_id'])) {
            throw new InvalidArgumentException("缺少配置项 -- [public_key_id]");
        }
    }

    /**
     * 加载私钥
     * @param string $certPrivate
     * @return string
     * @throws InvalidArgumentException
     */
    protected function loadPrivateKey(string $certPrivate): string
    {
        if (stripos($certPrivate, '-----BEGIN PRIVATE KEY-----') === false) {
            if (file_exists($certPrivate)) {
                return file_get_contents($certPrivate);
            } else {
                throw new InvalidArgumentException("文件不存在 -- [cert_private]");
            }
        }
        return $certPrivate;
    }

    /**
     * 创建 APIv3 客户端实例
     * @return mixed
     * @throws Exception
     */
    protected function createClient()
    {
        // 从本地文件中加载「商户API私钥」，「商户API私钥」会用来生成请求的签名
        $merchantPrivateKeyInstance = Rsa::from($this->config['cert_private'], Rsa::KEY_TYPE_PRIVATE);

        // 从本地文件中加载「微信支付平台证书」或者「微信支付平台公钥」，用来验证微信支付应答的签名
        $platformPublicKeyInstance = Rsa::from($this->config['platform_public_key'], Rsa::KEY_TYPE_PUBLIC);

        // 构造一个 APIv3 客户端实例
        return Builder::factory([
            'mchid'      => $this->config['mch_id'], // 商户号
            'serial'     => $this->config['merchant_certificate_serial'], // 「商户API证书」的「证书序列号」
            'privateKey' => $merchantPrivateKeyInstance,
            'certs'      => [
                $this->config['public_key_id'] => $platformPublicKeyInstance,
            ],
        ]);
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
            return [
                'code'      => -1,
                'code_text' => $result['code'],
                'message'   => $result['message'],
                'data'      => []
            ];
        }

        return [
            'code'    => 0,
            'message' => '成功',
            'data'    => $result
        ];
    }

    /**
     * 数据解密
     *  目前用在手机号解密上
     * @param $str string 密文
     * @param $certPrivate string 私钥
     *
     * @return string 明文
     */
    public function getDecrypt($str, $certPrivate)
    {
        $decrypted = '';
        openssl_private_decrypt(base64_decode($str), $decrypted, $certPrivate, OPENSSL_PKCS1_OAEP_PADDING);
        return $decrypted;
    }
}
