<?php

use Sxqibo\FastPayment\NewPay\SinglePayQueryModel;
use Sxqibo\FastPayment\NewPay\SinglePayQueryService;

require_once '../../vendor/autoload.php';

class TestTransferQuery57
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

    /**
     * 测试付款到银行的查询
     */
    public function singlePayQuery($merId, $orderId, $payeeName, $payeeAccount, $tranAmt, $submitTime)
    {
        $data = [
            'merId'      => $merId,
            'merOrderId' => $orderId,
            'submitTime' => $submitTime,
        ];

        $singlePayQueryModel = new SinglePayQueryModel();

        $privateKey = $this->config['service_corp']['transfer_private_key'];
        $publicKey  = $this->config['service_corp']['transfer_public_key'];

        $singlePayQueryModel->setPrivateKey($privateKey);
        $singlePayQueryModel->setPublicKey($publicKey);

        $singlePayQueryModel->copy($data);

        $singlePayQueryService = new SinglePayQueryService();
        $result                = $singlePayQueryService->query($singlePayQueryModel);

        var_dump($result);
    }
}

function test1()
{
    $test = new TestTransferQuery57();
    // 服务商信息
    $merId = $test->getConfig()['service_corp']['merch_id']; // 服务商-商户ID

    // 用户信息
    $payeeName    = $test->getConfig()['transfer_info']['user_name'];
    $payeeAccount = $test->getConfig()['transfer_info']['user_card_number'];

    // 金额信息
    $tranAmt      = $test->getConfig()['transfer_info']['transfer_amount'];

    // 订单信息
    $orderId    = $test->getConfig()['transfer_info']['order_id'];
    $submitTime = $test->getConfig()['transfer_info']['submit_time'];

    $test->singlePayQuery($merId, $orderId, $payeeName, $payeeAccount, $tranAmt, $submitTime);
}

test1();
