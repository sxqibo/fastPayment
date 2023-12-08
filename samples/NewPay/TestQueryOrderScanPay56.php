<?php

use Sxqibo\FastPayment\NewPay\NewPayCode;
use Sxqibo\FastPayment\NewPay\QueryOrderScanPayService;
use Sxqibo\FastPayment\NewPay\ScanPayQueryModel;

require_once '../../vendor/autoload.php';

/**
 * 主要测试 QueryOrderScanPayService 和 ScanPayQueryModel 两个类
 */
class TestQueryOrderScanPay
{
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
        // 收款私钥
        $privateKey = '';
        $scanPayQueryModel->setPrivateKey($privateKey);
        $scanPayQueryModel->copy($data);

        $queryOrderScanPayService = new QueryOrderScanPayService();
        $query = $queryOrderScanPayService->query($scanPayQueryModel);
        var_dump($query);
    }
}

function test1()
{
    $merId = '11000008001';
    $orderId = '2023112959734748';
    $testQueryOrderScanPay = new TestQueryOrderScanPay();
    $testQueryOrderScanPay->scanPayQuery($merId, $orderId);
}

test1();
