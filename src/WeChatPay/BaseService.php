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
        'appid'                                  => '', // 微信-APPID
        'mchid'                                  => '', // 微信-商户编号
        'merchantPrivateKeyContent'              => '', // 商户-私钥内容
        'merchantCertificateSerial'              => '', // 微信-商户API证书
        'platformPublicKeyContent'               => '', // 微信-支付平台公钥
        'platformCertificateSerialOrPublicKeyId' => '', // 微信-平台公钥ID
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

            $this->config['appid']                                  = isset($options['appid']) ? $options['appid'] : '';
            $this->config['mchid']                                  = $options['mchid'];
            $this->config['merchantPrivateKeyContent']              = $this->loadPrivateKey($options['merchantPrivateKeyContent']);
            $this->config['merchantCertificateSerial']              = $options['merchantCertificateSerial'];
            $this->config['platformPublicKeyContent']               = $options['platformPublicKeyContent'];
            $this->config['platformCertificateSerialOrPublicKeyId'] = $options['platformCertificateSerialOrPublicKeyId'];

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
        if (empty($options['mchid'])) {
            throw new InvalidArgumentException("缺少配置项 -- [mchid]");
        }
        if (empty($options['merchantPrivateKeyContent'])) {
            throw new InvalidArgumentException("缺少配置项 -- [merchantPrivateKeyContent]");
        }
        if (empty($options['merchantCertificateSerial'])) {
            throw new InvalidArgumentException("缺少配置项 -- [merchantCertificateSerial]");
        }
        if (empty($options['platformPublicKeyContent'])) {
            throw new InvalidArgumentException("缺少配置项 -- [platformPublicKeyContent]");
        }
        if (empty($options['platformCertificateSerialOrPublicKeyId'])) {
            throw new InvalidArgumentException("缺少配置项 -- [platformCertificateSerialOrPublicKeyId]");
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
                throw new InvalidArgumentException("文件不存在 -- [merchantPrivateKeyContent]");
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
        $merchantPrivateKeyInstance = Rsa::from($this->config['merchantPrivateKeyContent'], Rsa::KEY_TYPE_PRIVATE);

        // 从本地文件中加载「微信支付平台证书」或者「微信支付平台公钥」，用来验证微信支付应答的签名
        $platformPublicKeyInstance = Rsa::from($this->config['platformPublicKeyContent'], Rsa::KEY_TYPE_PUBLIC);

        // 构造一个 APIv3 客户端实例
        return Builder::factory([
            'mchid'      => $this->config['mchid'], // 商户号
            'serial'     => $this->config['merchantCertificateSerial'], // 「商户API证书」的「证书序列号」
            'privateKey' => $merchantPrivateKeyInstance,
            'certs'      => [
                $this->config['platformCertificateSerialOrPublicKeyId'] => $platformPublicKeyInstance,
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
