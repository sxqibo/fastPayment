<?php

use Sxqibo\FastPayment\NewPay\SinglePayQueryModel;
use Sxqibo\FastPayment\NewPay\SinglePayQueryService;

require_once '../../vendor/autoload.php';

class TestSinglePayQuery
{
    /**
     * 测试付款到银行的查询
     */
    public function singlePayQuery($merId, $orderId, $payeeName, $payeeAccount, $tranAmt, $submitTime)
    {
        $data = [
            'merId' => $merId,
            'merOrderId' => $orderId,
            'submitTime' => $submitTime,
        ];

        $singlePayQueryModel = new SinglePayQueryModel();

        $privateKey = '';
        $publicKey = '';

        $singlePayQueryModel->setPrivateKey($privateKey);
        $singlePayQueryModel->setPublicKey($publicKey);

        $singlePayQueryModel->copy($data);

        $singlePayQueryService = new SinglePayQueryService();
        $result = $singlePayQueryService->query($singlePayQueryModel);

        var_dump($result);
    }
}

function test1()
{
    $newPayTest = new TestSinglePayQuery();
    $merId = '';
    $orderId = '';
    $payeeName = '';
    $payeeAccount = '';
    $tranAmt = 1;
    $submitTime = '';
    $newPayTest->singlePayQuery($merId, $orderId, $payeeName, $payeeAccount, $tranAmt, $submitTime);
}

test1();
