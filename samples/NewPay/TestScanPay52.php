<?php

use Sxqibo\FastPayment\NewPay\NewPayCode;
use Sxqibo\FastPayment\NewPay\ScanPayModel;
use Sxqibo\FastPayment\NewPay\ScanPayService;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

require_once '../../vendor/autoload.php';

/**
 * 主要测试 ScanPayModel 和 ScanPayService 两个类
 */
class TestScanPay
{
    private $config;
    private $logger;

    public function __construct($logger)
    {
        $this->config = include 'config.php';
        $this->logger = $logger;
    }

    public function getConfig()
    {
        return $this->config;
    }

    public function scanPay($merId, $orderId, $weChatMchId)
    {
        $scanPayService = new ScanPayService();
        $merOrderNum    = $orderId;

        $data = [
            'merId'       => $merId,        // 商户ID
            'merOrderNum' => $merOrderNum,  // 订单ID
            'tranAmt'     => $this->config['order_info']['scan_pay_amount'],   // 支付金额（单位：分）
            'orgCode'     => ScanPayModel::ORGCODE_WECHATPAY,   // 支付方式（微信：ORGCODE_WECHATPAY 阿里：ORGCODE_ALIPAY）
            'weChatMchId' => $weChatMchId,  // 进件号
        ];

        $privateKey = $this->config['service_corp']['payment_private_key'];
        $publicKey  = $this->config['service_corp']['payment_public_key'];

        $scanPayModel = new ScanPayModel();
        $scanPayModel->setPrivateKey($privateKey);
        $scanPayModel->setPublicKey($publicKey);

        $scanPayModel->copy($data);

        $result = $scanPayService->scanPay($scanPayModel);
        $this->logger->info('返回的数据:' . json_encode($result, JSON_UNESCAPED_UNICODE));


        $newPayCode = new NewPayCode();
        $this->logger->info('状态码：'. $newPayCode->getResultCode($result['resultCode']));

        return $result;
    }
}

/**
 * 日志相关
 */
// 创建一个日志记录器对象
$logger = new Logger('newPayLogger');

// 创建一个日志处理器对象，将日志记录到日志文件中
$handler = new StreamHandler('./logfile.log');
$logger->pushHandler($handler);

/**
 * 支付相关
 */
$test = new TestScanPay($logger);
$merId   = $test->getConfig()['service_corp']['merch_id'];  // 服务商-商户ID
$orderId = substr(md5(rand()), 20); // 订单号
$logger->info("我生成的订单号是：" . $orderId);
$weChatMchId = $test->getConfig()['merchant_corp']['wechat_mch_id']; // 微信进件号

$test->scanPay($merId, $orderId, $weChatMchId);

$logger->info("===以上为扫码支付 相关日志============");
print "扫码支付，相关信息请查看日志！";