<?php
require_once '../../vendor/autoload.php';

use Sxqibo\FastPayment\EPay\EpayServices;

$config = include 'config.php';

$tradeNo = $config['refund_info']['trade_no']; // 易支付订单号
$money   = $config['refund_info']['order_no']; // 退款金额

$epay   = new EpayServices($config['basic']);

try {
    $result = $epay->refund($tradeNo, $money);
} catch (Exception $e) {
    throw new \Exception('支付失败！' . $e->getMessage());
}

print_r($result);