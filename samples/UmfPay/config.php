<?php

return [
    // 基本信息
    'basic'     => [
        'appId'   => '',
        'pub'     => '',
        'private' => '',
    ],

    // 扫码信息
    'scan_info' => [
        'service'       => 'active_scancode_order_new',
        'notify_url'    => '',
        'goods_inf'     => '商品信息',
        'order_id'      => time(),
        'mer_date'      => date("Ymd"),
        'amount'        => 100,
        'user_ip'       => '127.0.0.1',
        'scancode_type' => 'UNION', // 支付宝支付
        'mer_flag'      => 'KMER',
        'consumer_id'   => '',
    ],

    // 公众号支付信息
    'mp_info' => [
        'service'          => 'publicnumber_and_verticalcode',
        'notify_url'       => '',
        'ret_url'          => '',
        'goods_inf'        => '商品名称',
        'order_id'         => time(),
        'mer_date'         => date("Ymd"),
        'amount'           => 100,
        'user_ip'          => '127.0.0.1',
        'is_public_number' => 'Y',
    ],

    // 退款信息
    'refund_order' => [
        'service'       => 'mer_refund',
        'refund_no'     => date("ymdHis") . rand(1111, 9999),
        'order_id'      => '2024050611111',
        'mer_date'      => '20240506',
        'org_amount'    => 100,
        'refund_amount' => 100,
    ],
];