<?php

namespace Sxqibo\FastPayment\WeChatPayV2;

use Sxqibo\FastPayment\Common\Client;
use InvalidArgumentException;
use Sxqibo\FastPayment\Common\Utility;
use WeChat\Exceptions\InvalidResponseException;
use WeChat\Exceptions\LocalCacheException;

class BaseService
{
    protected $base = 'https://api.mch.weixin.qq.com';

    protected $headers = [
        'Content-Type' => 'text/xml; charset=UTF-8'
    ];

    /**
     * 商户配置
     * @var DataArray
     */
    protected $config;

    /**
     * 当前请求数据
     * @var DataArray
     */
    protected $params;

    /**
     * 请求客户端
     * @var Client
     */
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
    public function __construct(array $options = [])
    {
        if (empty($options['appid'])) {
            throw new InvalidArgumentException("Missing Config -- [appid]");
        }
        if (empty($options['mch_id'])) {
            throw new InvalidArgumentException("Missing Config -- [mch_id]");
        }
        if (empty($options['mch_key'])) {
            throw new InvalidArgumentException("Missing Config -- [mch_key]");
        }

        $this->config = new DataArray($options);
        // 商户基础参数
        $this->params = new DataArray([
            'appid'     => $this->config->get('appid'),
            'mch_id'    => $this->config->get('mch_id'),
            'nonce_str' => Utility::createNoncestr(),
        ]);

        // 商户参数支持
        if ($this->config->get('sub_appid')) {
            $this->params->set('sub_appid', $this->config->get('sub_appid'));
        }
        if ($this->config->get('sub_mch_id')) {
            $this->params->set('sub_mch_id', $this->config->get('sub_mch_id'));
        }

        $this->client = new Client();
    }

    /**
     * 以Post请求接口
     * @param string $url 请求
     * @param array $data 接口参数
     * @param bool $isCert 是否需要使用双向证书
     * @param string $signType 数据签名类型 MD5|SHA256
     * @param bool $needSignType 是否需要传签名类型参数
     * @return array
     * @throws InvalidResponseException
     * @throws \WeChat\Exceptions\LocalCacheException
     */
    protected function callPostApi($url, array $data, $isCert = false, $signType = 'HMAC-SHA256')
    {
        $option = [];
        if ($isCert) {
            $option['ssl_cer'] = $this->config->get('ssl_cer');
            $option['ssl_key'] = $this->config->get('ssl_key');

            if (empty($option['ssl_cer']) || !file_exists($option['ssl_cer'])) {
                throw new InvalidArgumentException("Missing Config -- ssl_cer", '0');
            }
            if (empty($option['ssl_key']) || !file_exists($option['ssl_key'])) {
                throw new InvalidArgumentException("Missing Config -- ssl_key", '0');
            }
        }

        $params         = $this->params->merge($data);
        $params['sign'] = $this->getPaySign($params, $signType);

        $result = $this->post($url, Utility::arr2xml($params), $option);
        $result = Utility::xml2arr($result);

        return $result;
    }

    /**
     * 以post访问模拟访问
     * @param string $url 访问URL
     * @param array $data POST数据
     * @param array $options
     * @return boolean|string
     * @throws LocalCacheException
     */
    public function post($url, $data = [], $options = [])
    {
        $options['data'] = $data;

        return $this->doRequest('post', $url, $options);
    }

    /**
     * CURL模拟网络请求
     * @param string $method 请求方法
     * @param string $url 请求方法
     * @param array $options 请求参数[headers,data,ssl_cer,ssl_key]
     * @return boolean|string
     * @throws LocalCacheException
     */
    public function doRequest($method, $url, $options = [])
    {
        $curl = curl_init();
        // GET参数设置
        if (!empty($options['query'])) {
            $url .= (stripos($url, '?') !== false ? '&' : '?') . http_build_query($options['query']);
        }
        // CURL头信息设置
        if (!empty($options['headers'])) {
            curl_setopt($curl, CURLOPT_HTTPHEADER, $options['headers']);
        }
        // POST数据设置
        if (strtolower($method) === 'post') {
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $options['data']);
        }

        // 证书文件设置
        if (!empty($options['ssl_cer']))
            if (file_exists($options['ssl_cer'])) {
                curl_setopt($curl, CURLOPT_SSLCERTTYPE, 'PEM');
                curl_setopt($curl, CURLOPT_SSLCERT, $options['ssl_cer']);
            } else throw new \WeChat\Exceptions\InvalidArgumentException("Certificate files that do not exist. --- [ssl_cer]");
        // 证书文件设置
        if (!empty($options['ssl_key']))
            if (file_exists($options['ssl_key'])) {
                curl_setopt($curl, CURLOPT_SSLKEYTYPE, 'PEM');
                curl_setopt($curl, CURLOPT_SSLKEY, $options['ssl_key']);
            } else throw new InvalidArgumentException("Certificate files that do not exist. --- [ssl_key]");
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_TIMEOUT, 60);
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        list($content) = [curl_exec($curl), curl_close($curl)];

        return $content;
    }


    /**
     * 生成支付签名
     * @param array $data 参与签名的数据
     * @param string $signType 参与签名的类型
     * @param string $buff 参与签名字符串前缀
     * @return string
     */
    public function getPaySign(array $data, $signType = 'MD5', $buff = '')
    {
        ksort($data);
        if (isset($data['sign']))
            unset($data['sign']);
        foreach ($data as $k => $v)
            $buff .= "{$k}={$v}&";
        $buff .= ("key=" . $this->config->get('mch_key'));
        if (strtoupper($signType) === 'MD5') {
            return strtoupper(md5($buff));
        }
        return strtoupper(hash_hmac('SHA256', $buff, $this->config->get('mch_key')));
    }

    /**
     * 处理返回结果
     *
     * @param $result
     * @return array
     */
    public function handleResult($result)
    {
        if ($result['return_code'] == 'SUCCESS' && $result['result_code'] == 'SUCCESS') {

            return ['code' => 0, 'message' => '成功', 'data' => $result];
        } else {
            return [
                'code'    => -1, 'err_code' => $result['err_code'],
                'message' => $result['err_code_des'] ?? '',
                'data'    => $result
            ];
        }
    }
}

