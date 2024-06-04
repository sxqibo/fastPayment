<?php

return [
    // 基本信息
    'basic'         => [
        // 商户信息
        'mer_id'  => '',

        // 私钥
        'private' => '',

        // 公钥
        'pub'     => '',
    ],

    // 1. 扫码操作-信息
    'scan_info'     => [
        'order_id'     => 'TS' . date('YmsHis'),
        'order_amount' => 10,
        'apply_time'   => date('Y-m-d H:i:s'),
        'goods_name'   => '测试商品001',
        'goods_detail' => '测试商品详情001',
        'notify_url'   => '',
        'addition'     => 'scene=goods_detail_page',
    ],

    // 2. 扫码查询-信息
    'query_info'    => [
        'mer_id'     => '100018',
        'order_id'   => 'TS20240430105630',
        'apply_time' => date('Y-m-d H:i:s'),
    ],

    // 3. 退款操作-信息
    'refund_info'   => [
        'refund_order_id' => 'TR' . date('YmsHis'),
        'pay_order_id'    => 'TS20240430105630',
        'refund_amount'   => 1,
        'apply_time'      => date('Y-m-d H:i:s'),
        'notify_url'      => '',
        'addition'        => 'test',
    ],

    // 4. 退款查询-信息
    'refund_query'  => [
        'order_id'   => 'TR20240407110407',
        'apply_time' => date('Y-m-d H:i:s'),
    ],

    // 5. 公众号操作-信息
    'mp_info'       => [
        'order_id'      => 'TS' . date('YmsHis'),
        'order_amount'  => 1,
        'apply_time'    => date('Y-m-d H:i:s'),
        'goods_info'    => '测试商品001-subm',
        'order_subject' => '测试订单标题001-subm',
        'notify_url'    => '',
        'open_id'       => 'oCtK96XkgN2PkLqIyApgguKKBhL8',
    ],

    // 6. 公众号扫码操作-信息
    'mp_scan_info'  => [
        'order_id'      => 'TS' . date('YmsHis'),
        'order_amount'  => 1,
        'apply_time'    => date('Y-m-d H:i:s'),
        'goods_info'    => '测试商品001-subm',
        'order_subject' => '测试订单标题001-subm',
        'notify_url'    => '',
    ],

    // 7. 公众号查询-信息
    'mp_scan_query' => [
        'order_id'   => 'TS20240526164426',
        'apply_time' => date('Y-m-d H:i:s'),
    ],
];