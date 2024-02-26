<?php

return [

    /**
     * 服务商信息
     * 公司名称： A公司
     *
     * （一）扫码及查询
     * 5.2 微信&支付宝扫码（C扫B）- 测试OK
     *     【 密钥：1-1 付款私钥 和 1-2 付款公钥 】
     *     【 示例：TestScanPay52.php 】
     * 5.6 查询接口-扫码API - 测试OK
     *     【 密钥：1-1 付款私钥 】
     *     【 示例：TestQueryScanPay56.php 】
     *
     * （二）付款及查询
     * 5.4 付款到银行（其实就代付 - 测试OK
     *     【 密钥：2-1付款私钥 和 2-2付款公钥 】
     *     【 示例：TestSinglePay54.php 】
     * 5.7 查询接口-代付 - 测试OK
     *     【 密钥：2-1代付私钥 和 2-2代付公钥 】
     *     【 示例：TestSinglePayQuery57.php 】
     *
     *
     * （三）代付及查询
     * 5.8 退款接口  - 测试OK
     *     【 密钥：3-1退款私钥 和 3-2退款公钥 】
     *     【 示例：TestRefund58.php 】
     * 5.5 查询接口（其实是查询退款接口）- 测试OK
     *     【 密钥：3-1退款私钥 和 3-2退款公钥 】
     *     【 示例：TestQuery55.php 】
     */
    'service_corp'    => [
        /**
         * 服务商ID
         */
        'merch_id'             => '', // 服务商-"海南坤域"-商户ID

        /**
         * 1-1 收款私钥
         */
        'payment_private_key'  => '',

        /**
         * 1-2 收款公钥
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
    'merchant_corp'   => [
        'wechat_mch_id' => '', // 微信进件号 - XX有限公司
    ],

    /**
     * 订单信息
     *
     * 场景：
     * 第一步： 张三 通过扫码支付了 1 元， 订单号为：123456
     * 我生成的订单号是：4c17daa00f8c
     */
    'order_info'      => [
        /**
         * 交易金额
         */
        'scan_pay_amount' => '100', // 扫码交易金额
        /**
         * 扫码支付信息（用于查询支付）
         */
        'scan_pay_order'  => '4c17daa00f8c',  // 支付订单号（我生成的订单号，不是新生返回的订单号）
    ],

    /**
     * 收款信息
     *
     * 场景：
     * 第二步：平台收到后， 转给商户联系人 “李四” 1 元，卡号为 900123456
     * 我生成的转账订单号是： 14279c06e7f1
     */
    'transfer_info'   => [
        // 用户信息
        'user_name'        => '杨红伟', // 收款人
        'user_card_number' => '6227000267070109093', // 收款人账号
        // 金额信息
        'transfer_amount'  => 1, // 收款金额
        // 订单信息（查询需要）
        'order_id'         => '14279c06e7f1', // 我生成的转账的订单号
        'submit_time'      => '20231208', // 我的转账提交时间
    ],

    /**
     * 退款信息
     *
     * 场景：
     * 第三步：张三 退部分款项，第一次退了 0.2 元， 退款单号为：234567，时间为：20231114113822
     */
    'refund_info'     => [
        /**
         * 退款操作
         */
        // 支付时的信息
        'org_mer_order_id' => '4c17daa00f8c', // 原商户支付订单号，见查询参数：orderID
        'org_submit_time'  => '20231208163614', // 支付的时间
        'order_amt'        => '1', // 原订单金额，见查询参数：orderAmount
        // 退款金额
        'refund_order_amt' => '0.2', // 退款金额

        /**
         * 退款信息（用于查询退款）
         */
        'refund_order'     => '2c72dfeb1fd0',     // 退款订单号
        'refund_time'      => '20231113',   // 退款提交时间
    ],

    /**
     * 公众号支付信息
     */
    'mp_info'         => [
        'pay_amount' => '1', // 交易金额
        'orderSubject'  => 'test-title',  // 标题
        'goodsInfo'     => 'test-info',  // 描述
    ],


    /**
     * 公众号信息
     */
    'wechat_app_info' => [
        'appId'  => '', // 微信公众号appid，目前这是“海南坤裕科技有限公司”下的一个公众号
        'openId' => '',// 关注公众号的某一个人
    ],

];