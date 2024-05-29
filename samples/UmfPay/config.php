<?php

return [
    // 基本信息
    'basic'     => [
        'appid'   => '',
        'pub'     => '',
    ],

    // 扫码信息，说明：没有值的千万不能传值，要不报验签失败
    'scan_info' => [
        // 一：接口协议参数列表
        'service'       => 'active_scancode_order_new', // 接口名称（统一下单）@doc: https://www.yuque.com/umpayer/tv9uf6/dyx054r5g0yz626r
        // 二： 接口业务参数列表
        'notify_url'    => 'https://www.sxqibo.com/notify.php',          // 服务器异步通知页面路径
        'goods_inf'     => '商品信息',   // 商品描述信息
        'order_id'      => time(),      // 商户唯一订单号
        'mer_date'      => date("Ymd"),     // 原商户订单日期
        'amount'        => 100,         // 付款金额，如果是人民币，则以分为单位
        'user_ip'       => '127.0.0.1', // 用户IP地址，这个必须是真实IP，否则会报错“交易存在风险，订单支付失败，错误码xxx”
        'mer_flag'      => 'KMER',
        'app_id'        => 'wx88127142b07ec0e0',
        //'scancode_type' => 'UNION', // 这个在业务中必传
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