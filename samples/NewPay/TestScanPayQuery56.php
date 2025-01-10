<?php

use Sxqibo\FastPayment\NewPay\NewPayCode;
use Sxqibo\FastPayment\NewPay\QueryOrderScanPayService;
use Sxqibo\FastPayment\NewPay\ScanPayQueryModel;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

require_once '../../vendor/autoload.php';

/**
 * 主要测试 QueryOrderScanPayService 和 ScanPayQueryModel 两个类
 */
class TestScanPayQuery56
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
     * 扫码支付查询
     *
     * @param $merId
     * @param $orderId
     * @return void
     */
    public function scanPayQuery($merId, $orderId)
    {
        $data = [
            'serialID'  => md5(rand()),
            'mode'      => ScanPayQueryModel::MODE_SINGLE,
            'type'      => ScanPayQueryModel::TYPE_PAY,
            'orderID'   => $orderId,
            //            'orderID' => '',
            //            'beginTime' => '20230922110000',
            //            'endTime' => '20230922111059',
            'beginTime' => date('YmdH') . '0000',
            'endTime'   => date('YmdH') . '5959',
            //            'remark' => '',
            'partnerID' => $merId,
        ];

        $scanPayQueryModel = new ScanPayQueryModel();
        $privateKey        = $this->config['service_corp']['payment_private_key'];  // 付款私钥
        $scanPayQueryModel->setPrivateKey($privateKey);
        $scanPayQueryModel->copy($data);

        $queryOrderScanPayService = new QueryOrderScanPayService();
        $result                    = $queryOrderScanPayService->query($scanPayQueryModel);
        var_dump($result);
        $this->logger->info("扫码支付查询结果：". json_encode($result, 256));
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
 * 扫码查询相关
 */
$test = new TestScanPayQuery56($logger);

$merId = $test->getConfig()['service_corp']['merch_id']; // 服务商-商户ID

$orderId = $test->getConfig()['scan_info']['scan_pay_order']; // 扫码时的订单号

$test->scanPayQuery($merId, $orderId);

$logger->info("===以上为扫码 查询相关日志============");
print "扫码查询 相关信息请查看日志！";