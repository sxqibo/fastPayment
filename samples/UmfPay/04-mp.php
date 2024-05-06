<?php
require_once '../../vendor/autoload.php';

use Sxqibo\FastPayment\UmfPay\UmfService;

/**
 * 微信公众号支付
 * @param $appId
 * @param $pub
 * @param $private
 * @param $type
 * @return array
 * @throws \GuzzleHttp\Exception\GuzzleException
 * @throws Exception
 */
function wxjspay($config)
{
    // 基本信息
    $appId   = $config['basic']['appid'];
    $pub     = $config['basic']['pub'];
    $private = $config['basic']['private'];

    // 公众号信息
    $mpInfo                  = $config['mp_info'];

    // 支付
    $url = (new UmfService($appId, $pub, $private))->getpayurl($mpInfo);

    // 结果
    return ['type' => 'jump', 'url' => $url];
}


$config = include 'config.php';
try {
    $result = wxjspay($config);
} catch (\GuzzleHttp\Exception\GuzzleException $e) {
    throw new \Exception('微信公众号-支付失败1！' . $e->getMessage());
} catch (Exception $e) {
    throw new \Exception('微信公众号-支付失败2！' . $e->getMessage());
}

print_r($result);