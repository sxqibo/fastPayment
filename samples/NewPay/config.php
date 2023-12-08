<?php

return [

    /**
     * 服务商信息
     * 公司名称： A公司
     *
     * 5.2 微信&支付宝扫码（C扫B）- 测试OK
     *     【 密钥：1-1 付款私钥 和 1-2 付款公钥 】
     * 5.4 付款到银行（其实就代付 - 测试OK
     *     【 密钥：2-1付款私钥 和 2-2付款公钥 】
     * 5.5 查询接口（其实是查询退款接口）- 测试OK
     *     【 密钥：3-1退款私钥 和 3-2退款公钥 】
     * 5.6 查询接口-扫码API - 测试OK
     *     【 密钥：1-1 付款私钥 】
     * 5.7 查询接口-代付 - 测试OK
     *     【 密钥：2-1代付私钥 和 2-2代付公钥 】
     * 5.8 退款接口  - 测试OK
     *     【 密钥：3-1退款私钥 和 3-2退款公钥 】
     */
    'service_corp'  => [
        /**
         * 服务商ID
         */
        'merch_id'             => '', // 服务商-"优卡科技"-商户ID

        /**
         * 1-1 付款私钥
         */
        'payment_private_key'  => '',

        /**
         * 1-2 付款公钥
         */
        'payment_public_key'   => '',


        /**
         * 2-1 代付私钥
         */
        'transfer_private_key' => '',

        /**
         * 2-2 代付公钥
         */
        'transfer_public_key'  => '',

        /**
         * 3-1 退款私钥
         */
        'refund_private_key'   => '',

        /**
         * 3-2 退款公钥
         */
        'refund_public_key'    => '',

    ],

    /**
     * 商户信息
     * 商户名称： A-1 公司
     */
    'merchant_corp' => [
        'wechat_mch_id' => '', // 微信进件号
    ],

    /**
     * 订单信息
     */
    'order_info'    => [
        /**
         * 扫码支付信息（用于查询支付）
         * 示例：
         * 张三扫码支付后，获取订单号是 123456
         * 这时查询扫码订单的信息，订单号就是 123456
         */
        'scan_pay_order'   => '',

        /**
         * 退款信息（用于查询退款）
         * 示例：
         * 张三退了一笔10元，订单号是 234567
         * 这时查询退款信息，订单号就是 234567， 时间就是退款的时间
         */
        'refund_order'     => '', // 退款订单号
        'refund_time'      => '', //退款提交时间

        /**
         * 退款操作
         * 示例：
         * 张三购买一笔10元，订单号是 234567，退款金额是 2元
         * 这时前三个参数就是支付的信息， 第4个参数就是退款的金额
         * 说明： 这里退款可以分批退款
         */
        // 支付时的信息
        'org_mer_order_id' => '', // 原商户支付订单号，见查询参数：orderID
        'org_submit_time'  => '', // 支付的时间
        'order_amt'        => '', //原订单金额，见查询参数：orderAmount
        // 退款金额
        'refund_order_amt' => '', // 退款金额
    ],

    /**
     * 收款信息
     */
    'transfer_info' => [
        // 用户信息
        'user_name'        => '', // 收款人
        'user_card_number' => '', // 收款人账号
        // 金额信息
        'transfer_amount'  => 1, // 收款金额，例如1
        // 订单信息
        'order_id'         => '', // 转账的订单号TODO
        'submit_time'      => '', // 转账提交时间TODO
    ]

];