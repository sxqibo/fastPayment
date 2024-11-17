<?php
require_once '../../vendor/autoload.php';

use Sxqibo\FastPayment\EPay\EpayServices;

$config = include 'config.php';

$param = [
    // 基本信息
    'pid'          => $config['basic']['pid'],   // 商户ID

    // 2. 发起支付（获取链接）- 信息
    'type'         => $config['order_info']['type'],            // 支付方式
    'out_trade_no' => $config['order_info']['out_trade_no'],    // 订单号
    'return_url'   => $config['order_info']['notify_url'],      // 异步回调地址
    'notify_url'   => $config['order_info']['notify_url'],      // 跳转通知地址
    'name'         => $config['order_info']['name'],            // 商品名称
    'money'        => $config['order_info']['money'],           // 金额

    // 3. mapi额外的参数
    'clientip'     => $config['mapi_info']['clientip'],         // 用户IP地址
    'device'       => $config['mapi_info']['device'],           // 用户IP地址
];

$epay      = new EpayServices($config['basic']);
try {
    $result = $epay->mapi($param); // 3. mapi发起支付（API接口）
} catch (Exception $e) {
    throw new \Exception('支付失败！' . $e->getMessage());
}
print_r($result);