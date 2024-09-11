<?php
require_once '../../vendor/autoload.php';

use Sxqibo\FastPayment\EPay\EpayServices;

$config = include 'config.php';

$param = [
    // 基本信息
    "pid"          => $config['basic']['pid'],   // 商户ID

    // 1.发起支付（页面跳转）-信息
    "type"         => $config['order_info']['type'],            // 支付方式
    "notify_url"   => $config['order_info']['notify_url'],      // 同步回调
    "return_url"   => $config['order_info']['notify_url'],      // 异步回调
    "out_trade_no" => $config['order_info']['out_trade_no'],    // 订单号
    "name"         => $config['order_info']['name'],            // 商品名称
    "money"        => $config['order_info']['money'],           // 金额
];

$epay      = new EpayServices($config['basic']);
try {
    $result = $epay->submitPage($param); // 发起支付（页面跳转）
} catch (Exception $e) {
    throw new \Exception('支付失败！' . $e->getMessage());
}
print_r($result);