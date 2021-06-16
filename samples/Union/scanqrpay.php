<?php

require_once '../../vendor/autoload.php';

use Sxqibo\FastPayment;

$params = [
    'cus_id'      => '',
    'app_id'      => '',
    'public_key'  => '',
    'private_key' => '',
];

$service = new FastPayment\UnionPay($params);

$data = [
    'amount'         => 2000, // 交易金额-单位为分-否-15
    'order_no'       => 'T' . time(), // 商户交易单号-商户的交易订单号-否-32
    'auth_code'      => 'wxp://f2f0Cf0h0KUa048VLANefcfH5HeRihHaK3H5', // 支付授权码-如微信,支付宝,银联的付款二维码 - 否-32
    'title'          => '测试111', // 订单标题-订单商品名称，为空则以商户名作为商品名称-是-100-最大100个字节(50个中文字符)-
    'remark'         => '备注', // 备注-备注信息-是-160-最大160个字节(80个中文字符)禁止出现+，空格，/，?，%，#，&，=这几类特殊符号
    'goods_tag'      => 'youhui', // 订单支付标识-订单优惠标记，用于区分订单是否可以享受优惠，字段内容在微信后台配置券时进行设置，说明详见代金券或立减优惠-是-32-只对微信支付有效W01交易方式不支持
    'benefit_detail' => '优惠信息', // 优惠信息-Benefitdetail的json字符串,注意是String-是-不限制-仅支持微信单品优惠、W01交易方式不支持、支付宝智慧门店/支付宝单品优惠
];

$result = $service->scanqrpay($data);
var_dump($result);

