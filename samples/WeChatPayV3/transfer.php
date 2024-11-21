<?php
require_once '../../vendor/autoload.php';

use Sxqibo\FastPayment\WeChatPayV3\Transfer;

// 1. 发起批量转账API
$data = [
    'batch_no'             => '',
    'batch_name'           => '',
    'batch_remark'         => '',
    'total_amount'         => '', // 10元
    'total_num'            => '',
    'transfer_detail_list' => [
        [
            'detail_no'       => '',
            'transfer_amount' => '',
            'transfer_remark' => '',
            'openid'          => '',
            'user_name'       => '',
        ],
    ],
];

$options = [
    'appid'        => '', // 微信绑定APPID
    'mch_id'       => '', // 微信商户编号
    'mch_v3_key'   => '', // 微信商户密钥
    'cert_private' => '', // 商户API私钥
    'cert_public'  => '', // 商户API公钥
];

try {
    $service = new Transfer($options);
    $result  = $service->batches($data);
    var_dump($result);
} catch (\Exception $e) {
    print_r($e->getMessage() . PHP_EOL);
    exit;
}

// 2. 商家批次单号查询批次单API
// $outBatchNo = '';
// $outDetailNo = '';
// var_dump  = $service->getBatchesByOutBatchNo($outBatchNo);
// dd($result);

// 3. 商家明细单号查询明细单API
// $result = $service->getBatchesDetailByOutDetailNo($outBatchNo,$outDetailNo);
// var_dump($result);



