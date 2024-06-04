<?php
require_once '../../vendor/autoload.php';

use Sxqibo\FastPayment\KunyuPay\KunyuService;

/**
 * 扫码支付单笔订单查询
 * @param $config
 * @return mixed|null
 */
function mp($config)
{
    // 基本信息
    $merId   = $config['basic']['mer_id'];
    $pub     = $config['basic']['pub'];
    $private = $config['basic']['private'];

    // 扫码信息
    $info = $config['mp_query'];

    return (new KunyuService($merId, $private, $pub))->mpPayQuery($info);
}


$config = include 'config.php';
try {
    $result = mp($config);
} catch (\GuzzleHttp\Exception\GuzzleException $e) {
    throw new \Exception('微信支付失败1！' . $e->getMessage());
} catch (Exception $e) {
    throw new \Exception('微信支付失败2！' . $e->getMessage());
}

print_r($result);