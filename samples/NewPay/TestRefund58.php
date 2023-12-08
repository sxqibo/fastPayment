<?php

require_once '../../vendor/autoload.php';

class TestRefund
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
    }
}

function test1()
{
    $test = new TestRefund();

    $merId   = $test->getConfig()['service_corp']['merch_id']; // 新生支付平台提供给商户的唯一ID
    $orderId = substr(md5(rand()), 20); // 退款订单号
    echo '............' . $orderId . PHP_EOL;

    // 原订单信息
    $orgMerOrderId = $test->getConfig()['refund_info']['org_mer_order_id']; // 原商户支付订单号，见查询参数：orderID
    $orgSubmitTime = $test->getConfig()['refund_info']['org_submit_time']; // 原订单支付下单请求时间，见查询参数：acquiringTime
    $orderAmt      = $test->getConfig()['refund_info']['order_amt']; // 原订单金额，见查询参数：orderAmount

    // 退款信息
    $refundOrderAmt = $test->getConfig()['refund_info']['refund_order_amt']; // 退款金额， 见查询参数：

    $test->refund($merId, $orderId, $orgMerOrderId, $orgSubmitTime, $orderAmt, $refundOrderAmt);
}

test1();
