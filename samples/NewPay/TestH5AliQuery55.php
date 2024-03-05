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
class TestH5AliQuery55
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

        $privateKey = $this->config['service_corp']['new_private_key'];
        $publicKey  = $this->config['service_corp']['new_public_key'];

        $queryModel = new QueryModel();
        $queryModel->setPrivateKey($privateKey);
        $queryModel->setPublicKey($publicKey);
        $queryModel->copy($data);

        $queryService = new QueryService();
        $result       = $queryService->query($queryModel);

        var_dump($result);
        $this->logger->info("支付宝H5查询结果：". json_encode($result, 256));
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
 * H5查询相关
 */
$test = new TestH5AliQuery55($logger);

$merId = $test->getConfig()['service_corp']['merch_id']; // 服务商-商户ID

// H5信息
$orgMerOrderId = $test->getConfig()['h5_info']['h5_order']; // 订单号（支付宝H5订单号）
$logger->info("订单号（支付宝H5）：" . $orgMerOrderId);
$orgSubmitTime = $test->getConfig()['h5_info']['h5_time'];; // 支付宝H5提交时间
$logger->info("支付宝H5提交时间：" . $orgSubmitTime);

$test->query($merId, $orgMerOrderId, $orgSubmitTime);

$logger->info("===以上为支付宝H5查询相关日志============");
print "支付宝H5查询 相关信息请查看日志！";