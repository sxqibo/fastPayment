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

// 1. 交易撤销 - 当天交易用撤销
$refundNo     = 'R123123';
$refundAmount = 1000; // 分
$result       = $service->cancel($refundNo, $refundAmount, ['old_order_no' => 'T1623394747']);

// 2. 交易退款 - 当天交易请用撤销,非当天交易才用此退货接口
$refundNo     = 'R123123';
$refundAmount = 1000; // 分
$result       = $service->refund($refundNo, $refundAmount, ['old_order_no' => 'T1623394747']);

// 3. 交易查询
$orderNo = '1234';
$result  = $service->query($orderNo);

// 4. 根据授权码(付款码)获取用户ID
$authCode = '01'; // 01-微信付款码 02-银联userAuth
$authType = '136048058474886014';
$result   = $service->getAuthCodeToUserId($authCode, $authType);

// 5. 微信人脸授权码获取
$storeId   = ''; // 门店编号-由商户定义， 各门店唯一
$storeName = '';  // 门店名称 - 有商户定义-否
$rawData   = ''; // //  初始化数据。由微信人脸SDK的接口返回。//获取方式参见微信官方刷脸支付接口：//[获取数据 getWxpayfaceRawdata](#获取数据 getWxpayfaceRawdata)
$result    = $service->getWxFacePayInfo($storeId, $storeName, $rawData);

// 6. 关闭订单
$orderNo = '';
$result  = $service->close($orderNo);

// 7.获取交易类型列表
$result = $service->getTrxCodeList();

// 8. 获取交易方式列表
$result = $service->getPayTypeList();

