<?php

use Sxqibo\FastPayment\NewPay\QueryModel;
use Sxqibo\FastPayment\NewPay\QueryService;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

require_once '../../vendor/autoload.php';

require 'config.php';

/**
 * 主要测试 QueryService 和 QueryModel 两个类
 */
class TestRefundQuery55
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

    public function query($merId, $merOrderId, $submitTime)
    {
        $data = [
            'merId'      => $merId,
            'merOrderId' => $merOrderId,
            'submitTime' => $submitTime,
        ];

        $privateKey = $this->config['service_corp']['refund_private_key'];
        $publicKey  = $this->config['service_corp']['refund_public_key'];

        $queryModel = new QueryModel();
        $queryModel->setPrivateKey($privateKey);
        $queryModel->setPublicKey($publicKey);
        $queryModel->copy($data);

        $queryService = new QueryService();
        $result       = $queryService->query($queryModel);

        var_dump($result);
        $this->logger->info("退款查询结果：". json_encode($result, 256));
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
 * 退款查询相关
 */
$test = new TestRefundQuery55($logger);

$merId = $test->getConfig()['service_corp']['merch_id']; // 服务商-商户ID

// 退款信息
$orgMerOrderId = $test->getConfig()['refund_info']['refund_order']; // 订单号（退款订单号）
$logger->info("订单号（退款订单号）：" . $orgMerOrderId);
$orgSubmitTime = $test->getConfig()['refund_info']['refund_time'];; // 退款提交时间
$logger->info("退款提交时间：" . $orgSubmitTime);

$test->query($merId, $orgMerOrderId, $orgSubmitTime);

$logger->info("===以上为退款查询相关日志============");
print "退款查询 相关信息请查看日志！";