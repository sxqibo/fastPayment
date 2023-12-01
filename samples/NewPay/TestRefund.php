<?php

require_once '../../vendor/autoload.php';

class TestRefund
{
    /**
     * 测试退款
     */
    public function refund(string $merId, string $orderId, string $orgMerOrderId, string $orgSubmitTime, string $orderAmt, string $refundOrderAmt)
    {
        $data = [
            'merId' => $merId,
            'merOrderId' => $orderId,

            'orgMerOrderId' => $orgMerOrderId,
            'orgSubmitTime' => $orgSubmitTime,
            'orderAmt' => $orderAmt,
            'refundOrderAmt' => $refundOrderAmt,
        ];

        $refundModel = new \Sxqibo\FastPayment\NewPay\RefundModel();
        $refundService = new \Sxqibo\FastPayment\NewPay\RefundService();

        $privateKey = '';
        $publicKey = '';

        $refundModel->setPrivateKey($privateKey);
        $refundModel->setPublicKey($publicKey);

        $refundModel->copy($data);

        $result = $refundService->refund($refundModel);

        var_dump($result);
    }
}

function test1()
{
    $merId = ''; // 新生支付平台提供给商户的唯一ID
    $orderId = substr(md5(rand()), 20); // 退款订单号
    echo '............' . $orderId . PHP_EOL;
    $orgMerOrderId = ''; // 原商户支付订单号，见查询参数：orderID
    $orgSubmitTime = ''; // 原订单支付下单请求时间，见查询参数：acquiringTime
    $orderAmt = ''; // 原订单金额，见查询参数：orderAmount
    $refundOrderAmt = ''; // 退款金额， 见查询参数：
    $newPayTest = new TestRefund();
    $newPayTest->refund($merId, $orderId, $orgMerOrderId, $orgSubmitTime, $orderAmt, $refundOrderAmt);
}

test1();
