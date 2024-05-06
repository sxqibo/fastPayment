<?php
require_once '../../vendor/autoload.php';

use Sxqibo\FastPayment\UmfPay\UmfService;

/**
 * 云闪付扫码支付
 * @param $appId
 * @param $pub
 * @param $private
 * @param $type
 * @return false|string
 * @throws \GuzzleHttp\Exception\GuzzleException
 * @throws Exception
 */
function qrcode($config)
{
    // 基本信息
    $appId   = $config['basic']['appid'];
    $pub     = $config['basic']['pub'];
    $private = $config['basic']['private'];

    // 扫码信息
    $scanInfo                  = $config['scan_info'];
    $scanInfo['scancode_type'] = 'UNION'; //

    $result = (new UmfService($appId, $pub, $private))->submit($scanInfo);

    if (isset($result['ret_code']) && $result['ret_code'] == '0000') {
        return base64_decode($result['bank_payurl']);
    } elseif (isset($result['ret_code'])) {
        throw new Exception('[' . $result['ret_code'] . ']' . $result['ret_msg']);
    } else {
        throw new Exception('返回数据解析失败');
    }
}

$config = include 'config.php';
try {
    $result = qrcode($config);
} catch (\GuzzleHttp\Exception\GuzzleException $e) {
    throw new \Exception('云闪付-支付失败1！' . $e->getMessage());
} catch (Exception $e) {
    throw new \Exception('云闪付-支付失败2！' . $e->getMessage());
}

print_r($result);