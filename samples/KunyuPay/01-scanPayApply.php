<?php
require_once '../../vendor/autoload.php';

use Sxqibo\FastPayment\KunyuPay\KunyuService;

/**
 * 扫码支付（下单获取二维码）
 * @param $config
 * @return false|string
 * @throws Exception
 */
function qrcode($config)
{

    // 基本信息
    $merId   = $config['basic']['mer_id'];
    $pub     = $config['basic']['pub'];
    $private = $config['basic']['private'];

    // 扫码信息
    $scanInfo = $config['scan_info'];

    return (new KunyuService($merId, $private, $pub))->scanPayApply($scanInfo);
}

$config = include 'config.php';
try {
    $result = qrcode($config);
} catch (\GuzzleHttp\Exception\GuzzleException $e) {
    throw new \Exception('微信支付失败1！' . $e->getMessage());
} catch (Exception $e) {
    throw new \Exception('微信支付失败2！' . $e->getMessage());
}

print_r($result);