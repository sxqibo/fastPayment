<?php

require_once '../../vendor/autoload.php';

use Sxqibo\FastPayment\WeChatPayV3\ProfitSharing;

// 1. 请求分账API
$data = [
    'transaction_id'   => '4208450740201411110007820472', // 微信支付订单号
    'order_no'         => 'P20150806125346', // 商户系统内部的分账单号，在商户系统内部唯一，同一分账单号多次请求等同一次。只能是数字、大小写字母_-|*@
    'unfreeze_unsplit' => true,
    'receivers'        => [
        [
            'type'        => 'MERCHANT_ID',
            'account'     => '86693852',
            'name'        => 'hu89ohu89ohu89o',
            'amount'      => 888,
            'description' => '分给商户A',
        ],
    ],
];

$options = [
    'appid'        => '', // 微信绑定APPID，需配置
    'mch_id'       => '', // 微信商户编号，需要配置
    'mch_v3_key'   => '', // 微信商户密钥，需要配置
    'cert_private' => '', // 商户API私钥
    'cert_public'  => '', // 商户API公钥
];

try {
    $service = new ProfitSharing($options);
    $result  = $service->createOrder($data);
    var_dump($result);
} catch (\Exception $e) {
    print_r($e->getMessage() . PHP_EOL);
    exit;
}

try {
    // 2. 查询分账结果API
    // $result  = $service->getOrdersDetail('', '');

    // 3. 请求分账回退API
    // $data   = [
    //     'order_no'     => '', // 商户分账单号
    //     'return_no'    => '', // 商户回退单号
    //     'return_mchid' => '', // 回退商户号
    //     'amount'       => 10, // 回退金额
    //     'description'  => '用户退款' // 回退描述
    // ];
    //
    // $result = $service->returnOrders($data);

    // 4. 查询分账回退结果API
    // $outReturnNo = '';
    // $outOrderNo  = '';
    // $result      = $service->getReturnOrdersInfo($outReturnNo, $outOrderNo);

    // 5. 解冻剩余资金API
    // $transactionId = '';
    // $outOrderNo    = '';
    // $description   = '解冻全部剩余资金';
    // $result        = $service->unfreeze($transactionId, $outOrderNo, $description);

    // 6. 查询剩余待分金额API
    // $transactionId = '';
    // $result        = $service->getTransactionsAmount($transactionId);

    // 7. 添加分账接收方API
    // $data = [
    //     'type'            => '',
    //     'account'         => '',
    //     'name'            => '',
    //     'relation_type'   => '',
    //     'custom_relation' => '',
    // ];
    // $result = $service->addReceivers($data);

    // 8. 删除分账接收方API
    // $type    = '';
    // $account = '';
    // $result  = $service->deleteReceivers($type, $account);
    // dd($result);

} catch (\Exception $e) {
    print_r($e->getMessage() . PHP_EOL);
    exit;
}


