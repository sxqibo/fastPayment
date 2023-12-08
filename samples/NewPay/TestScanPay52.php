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
    private $config;

    public function __construct() {
        $this->config = include 'config.php';
    }

    public function getConfig() {
        return $this->config;
    }

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

        $privateKey = $this->config['service_corp']['payment_private_key'];
        $publicKey = $this->config['service_corp']['payment_public_key'];

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
    $test = new TestScanPay();

    $merId = $test->getConfig()['service_corp']['merch_id'];  // 服务商-商户ID
    $orderId = substr(md5(rand()), 20); // 订单号
    $weChatMchId = $test->getConfig()['merchant_corp']['wechat_mch_id']; // 微信进件号

    $test->scanPay($merId, $orderId, $weChatMchId);
}

test1();
