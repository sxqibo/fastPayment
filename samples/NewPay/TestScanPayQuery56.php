<?php

use Sxqibo\FastPayment\NewPay\NewPayCode;
use Sxqibo\FastPayment\NewPay\QueryOrderScanPayService;
use Sxqibo\FastPayment\NewPay\ScanPayQueryModel;

require_once '../../vendor/autoload.php';

/**
 * 主要测试 QueryOrderScanPayService 和 ScanPayQueryModel 两个类
 */
class TestScanPayQuery56
{
    private $config;

    public function __construct() {
        $this->config = include 'config.php';
    }

    public function getConfig() {
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
            'serialID' => md5(rand()),
            'mode' => ScanPayQueryModel::MODE_SINGLE,
            'type' => ScanPayQueryModel::TYPE_PAY,
            'orderID' => $orderId,
            //            'orderID' => '',
            //            'beginTime' => '20230922110000',
            //            'endTime' => '20230922111059',
            'beginTime' => date('YmdH') . '0000',
            'endTime' => date('YmdH') . '5959',
            //            'remark' => '',
            'partnerID' => $merId,
        ];

        $scanPayQueryModel = new ScanPayQueryModel();
        $privateKey = $this->config['service_corp']['payment_private_key'];  // 付款私钥
        $scanPayQueryModel->setPrivateKey($privateKey);
        $scanPayQueryModel->copy($data);

        $queryOrderScanPayService = new QueryOrderScanPayService();
        $query = $queryOrderScanPayService->query($scanPayQueryModel);
        var_dump($query);
    }
}

function test1()
{
    $test = new TestScanPayQuery56();

    $merId = $test->getConfig()['service_corp']['merch_id']; // 服务商-商户ID

    $orderId = $test->getConfig()['order_info']['scan_pay_order']; // 扫码时的订单号

    $test->scanPayQuery($merId, $orderId);
}

test1();
