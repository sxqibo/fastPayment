<?php

require_once '../../vendor/autoload.php';

use Sxqibo\FastPayment\WeChatPayV2\Transfer;

$data = [
    'partner_trade_no' => time(),
    'openid'           => 'o38gps3vNdCqaggFfrBRCRikwlWY',
    'check_name'       => 'FORCE_CHECK',
    'amount'           => '100',
    'desc'             => '企业付款操作说明信息',
    'spbill_create_ip' => '127.0.0.1',
];

$options = [
    'appid'   => '', // 微信绑定APPID，需配置
    'mch_id'  => '', // 微信商户编号，需要配置
    'mch_key' => '', // 微信商户密钥，需要配置
    'ssl_key' => '', //
    'ssl_cer' => '', //
];

try {
    $service = new Transfer($options);
    // 1.提现到零钱
    // $result  = $service->create($data);

    // 2.查询结果
    // $result = $service->query('X21312321');
} catch (\Exception $e) {
    print_r($e->getMessage() . PHP_EOL);
    exit;
}


