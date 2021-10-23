<?php

require_once '../../vendor/autoload.php';

use Sxqibo\FastPayment\WeChatPayV2\Transfer;

$data = [
    'partner_trade_no' => time(),
    "enc_bank_no"      => '', //收款方银行卡号RSA加密
    "enc_true_name"    => '', //收款方用户名RSA加密
    "bank_code"        => 1022, //收款银行编号
    'amount'           => '100',
    'desc'             => '企业付款操作说明信息',
];

$options = [
    'mch_id'  => '', // 微信商户编号，需要配置
    'mch_key' => '', // 微信商户密钥，需要配置
    'ssl_key' => '', //
    'ssl_cer' => '', //
    'pub_cer' => '', //
];

try {
    $service = new Transfer($options);
//    // 1.提现到银行卡
     $result  = $service->createBank($data);
//
//    // 2.查询结果
//     $result = $service->queryBank('X21312321');
} catch (\Exception $e) {
    print_r($e->getMessage() . PHP_EOL);
    exit;
}


