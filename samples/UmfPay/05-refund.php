<?php
require_once '../../vendor/autoload.php';

use Sxqibo\FastPayment\UmfPay\UmfService;


function refund($config)
{
    // 基本信息
    $appId   = $config['basic']['appid'];
    $pub     = $config['basic']['pub'];
    $private = $config['basic']['private'];

    // 退款信息
    $refundOrder = $config['refund_order'];

    // 退款
    try {
        $result = (new UmfService($appId, $pub, $private))->submit($refundOrder);
    } catch (Exception $ex) {
        return ['code' => -1, 'msg' => $ex->getMessage()];
    }

    // 返回
    if (isset($result['ret_code']) && $result['ret_code'] == '0000') {
        return ['code' => 0, 'trade_no' => $result['order_id'], 'refund_fee' => $result['refund_amt']];
    } elseif (isset($result['ret_code'])) {
        return ['code' => -1, 'msg' => '[' . $result['ret_code'] . ']' . $result['ret_msg']];
    } else {
        return ['code' => -1, 'msg' => '未知错误'];
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