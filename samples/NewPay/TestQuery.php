<?php

use Sxqibo\FastPayment\NewPay\QueryModel;
use Sxqibo\FastPayment\NewPay\QueryService;

require_once '../../vendor/autoload.php';

/**
 * 主要测试 QueryService 和 QueryModel 两个类
 */
class TestQuery
{
    public function query($merId, $merOrderId, $submitTime)
    {
        $data = [
            'merId' => $merId,
            'merOrderId' => $merOrderId,
            'submitTime' => $submitTime,
        ];

        $privateKey = '';
        $publicKey = '';

        $queryModel = new QueryModel();
        $queryModel->setPrivateKey($privateKey);
        $queryModel->setPublicKey($publicKey);
        $queryModel->copy($data);

        $queryService = new QueryService();
        $result = $queryService->query($queryModel);

        var_dump($result);
    }
}

function test1()
{
    $merId = '';
    $orgMerOrderId = '';
    $orgSubmitTime = '';

    $test = new TestQuery();
    $test->query($merId, $orgMerOrderId, $orgSubmitTime);
}

test1();
