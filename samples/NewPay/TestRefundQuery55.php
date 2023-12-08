<?php

use Sxqibo\FastPayment\NewPay\QueryModel;
use Sxqibo\FastPayment\NewPay\QueryService;

require_once '../../vendor/autoload.php';

require 'config.php';

/**
 * 主要测试 QueryService 和 QueryModel 两个类
 */
class TestRefundQuery55
{
    private $config;

    public function __construct()
    {
        $this->config = include 'config.php';
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
    }
}

function test1()
{
    $test = new TestRefundQuery55();

    $merId = $test->getConfig()['service_corp']['merch_id']; // 服务商-商户ID

    // 退款信息
    $orgMerOrderId = $test->getConfig()['order_info']['refund_order']; // 订单号（退款订单号）
    $orgSubmitTime = $test->getConfig()['order_info']['refund_time'];; // 退款提交时间

    $test->query($merId, $orgMerOrderId, $orgSubmitTime);
}

test1();
