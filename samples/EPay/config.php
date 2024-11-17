<?php
return [
    // 基本信息
    'basic'       => [
        'apiurl' => '', // 支付接口地址
        'pid'    => '',  // 商户ID
        'key'    => '',   // 商户密钥
    ],

    // 1 和 2） 发起支付（页面跳转）-信息
    'order_info'  => [
        'type'         => 'alipay', // 支付方式（可传入 alipay ,wxpay,qqpay,bank,jdpay）
        'notify_url'   => 'http://127.0.0.1/SDK/notify_url.php', // 异步地址， 需http://格式的完整路径，不能加?id=123这类自定义参数
        'return_url'   => 'http://127.0.0.1/SDK/return_url.php', // 同步地址，需http://格式的完整路径，不能加?id=123这类自定义参数
        'out_trade_no' => time(), // 商户网站订单系统中唯一订单号，必填
        'name'         => '测试商品',
        'money'        => 100,
    ],

    // 3 mapi的额外信息，基本信息同上边1的内容
    'mapi_info'   => [
        'clientip' => '127.0.0.1', // 用户IP地址
        'device'   => 'pc' // 设备类型,列表： pc, mobile, qq, wechat, alipay, jump

    ],

    // 6. 订单查询
    'query_info'  => [
        'order_no' => '12312312', // 支付时的订单号
    ],

    // 7. 订单退款
    'refund_info' => [
        'trade_no' => '12312312', // 易支付订单号
        'money'    => 100, // 退款金额
    ],


];