<?php

use Sxqibo\FastPayment\NewPay\NewPayCode;
use Sxqibo\FastPayment\NewPay\ScanPayModel;
use Sxqibo\FastPayment\NewPay\ScanPayService;

require_once '../../vendor/autoload.php';

/**
 * 主要测试 ScanPayModel 和 ScanPayService 两个类
 */
class TestScanPay
{
    public function scanPay($merId, $orderId, $weChatMchId)
    {
        $scanPayService = new ScanPayService();
        $merOrderNum = $orderId;

        $data = [
            // 商户ID
            'merId' => $merId,
            // 订单ID
            'merOrderNum' => $merOrderNum,
            // 支付金额（单位：分）
            'tranAmt' => 1,
            // 支付方式（微信：ORGCODE_WECHATPAY 阿里：ORGCODE_ALIPAY）
            'orgCode' => ScanPayModel::ORGCODE_WECHATPAY,
            // 进件号
            'weChatMchId' => $weChatMchId,
        ];

        $privateKey = '';
        $publicKey = '';

        $scanPayModel = new ScanPayModel();
        $scanPayModel->setPrivateKey($privateKey);
        $scanPayModel->setPublicKey($publicKey);

        $scanPayModel->copy($data);

        $result = $scanPayService->scanPay($scanPayModel);
        print '返回的数据:' . json_encode($result, JSON_UNESCAPED_UNICODE) . PHP_EOL;

        $newPayCode = new NewPayCode();
        print $newPayCode->getResultCode($result['resultCode']) . PHP_EOL;
    }
}

function test1()
{
    $merId = '';
    $orderId = substr(md5(rand()), 20);
    $newPayTest = new TestScanPay();
    $weChatMchId = '';
    $newPayTest->scanPay($merId, $orderId, $weChatMchId);
}

test1();
