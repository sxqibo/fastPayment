<?php
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

require_once '../../vendor/autoload.php';

class TestRefund
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

    /**
     * 测试退款
     */
    public function refund(string $merId, string $orderId, string $orgMerOrderId, string $orgSubmitTime, string $orderAmt, string $refundOrderAmt)
    {
        $data = [
            'merId'          => $merId,
            'merOrderId'     => $orderId,
            'orgMerOrderId'  => $orgMerOrderId,
            'orgSubmitTime'  => $orgSubmitTime,
            'orderAmt'       => $orderAmt,
            'refundOrderAmt' => $refundOrderAmt,
        ];

        $refundModel   = new \Sxqibo\FastPayment\NewPay\RefundModel();
        $refundService = new \Sxqibo\FastPayment\NewPay\RefundService();

        $privateKey = $this->config['service_corp']['refund_private_key'];
        $publicKey  = $this->config['service_corp']['refund_public_key'];

        $refundModel->setPrivateKey($privateKey);
        $refundModel->setPublicKey($publicKey);

        $refundModel->copy($data);

        $result = $refundService->refund($refundModel);

        var_dump($result);
        $this->logger->info("退款相关信息：" . json_encode($result, 256));
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
 * 退款相关
 */
$test = new TestRefund($logger);

$merId   = $test->getConfig()['service_corp']['merch_id']; // 新生支付平台提供给商户的唯一ID
$orderId = substr(md5(rand()), 20); // 退款订单号
$logger->info("退款订单号为：" . $orderId);

// 原订单信息
$orgMerOrderId = $test->getConfig()['refund_info']['org_mer_order_id']; // 原商户支付订单号，见查询参数：orderID
$orgSubmitTime = $test->getConfig()['refund_info']['org_submit_time']; // 原订单支付下单请求时间，见查询参数：acquiringTime
$orderAmt      = $test->getConfig()['refund_info']['order_amt']; // 原订单金额，见查询参数：orderAmount

// 退款信息
$refundOrderAmt = $test->getConfig()['refund_info']['refund_order_amt']; // 退款金额， 见查询参数：

$test->refund($merId, $orderId, $orgMerOrderId, $orgSubmitTime, $orderAmt, $refundOrderAmt);

$logger->info("===以上为退款相关日志============");
print "退款 相关信息请查看日志！";