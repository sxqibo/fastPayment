<?php
require_once '../vendor/autoload.php';

use Sxqibo\FastPayment;

$params = [
    'cus_id'      => '',
    'app_id'      => '',
    'public_key'  => '',
    'private_key' => '',
];

$service = new FastPayment\UnionPay($params);

$data = [
    'amount'   => 1000, // 交易金额-单位为分-否-15
    'order_no' => 'T' . time(), // 商户交易单号-商户的交易订单号-否-32
    'pay_type' => 'W02', // 支付宝APP支付

    'title'      => '测试商品11', // 订单标题-订单商品名称，为空则以商户名作为商品名称-是-100-最大100个字节(50个中文字符)-
    'remark'     => '备注11', // 备注-备注信息-是-160-最大160个字节(80个中文字符)禁止出现+，空格，/，?，%，#，&，=这几类特殊符号
    'valid_time' => '5', // 有效时间-订单有效时间，以分为单位，不填默认为5分钟-是-2
    'acct'       => 'oe1Tu4gfv40yNdxs3h9RrsFDxLbw', // 支付平台用户标识-JS支付时使用：微信支付-用户的微信openid、支付宝支付-用户user_id、微信小程序-用户小程序的openid、云闪付JS-用户userId-是-32
    'notify_url' => 'http://api.newthink.cc/12321', // 交易结果通知地址-接收交易结果的异步通知回调地址，通知url必须为直接可访问的url，不能携带参数。-是-256
    'front_url'  => 'http::/www.baidu.com', // 支付完成跳转-必须为https协议地址，且不允许带参数-是-128-只支持payType=U02云闪付JS支付、payType=W02微信JS支付
];

$result = $service->pay($data);
var_dump($result);

