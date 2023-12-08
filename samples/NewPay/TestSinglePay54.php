<?php

use Sxqibo\FastPayment\NewPay\SinglePayInfoModel;
use Sxqibo\FastPayment\NewPay\SinglePayModel;
use Sxqibo\FastPayment\NewPay\SinglePayService;

require_once '../../vendor/autoload.php';

class TestSinglePay
{
    public function singlePay($merId, $orderId, $payeeName, $payeeAccount, $tranAmt)
    {
        $data = [
            'merId' => $merId,
            'merOrderId' => $orderId,
            'payeeName' => $payeeName,
            'payeeAccount' => $payeeAccount,
            'tranAmt' => $tranAmt,

            'payType' => SinglePayInfoModel::PAYTYPE_BANK,
            'auditFlag' => SinglePayInfoModel::AUDITFLAG_NO,
            'payeeType' => SinglePayInfoModel::PAYEETYPE_PERSON,
        ];

        $singlePayService = new SinglePayService();
        $singlePayModel = new SinglePayModel();


        // 收款的公私钥
        $privateKey = '';
        $publicKey = '';


        $singlePayModel->setPrivateKey($privateKey);
        $singlePayModel->setPublicKey($publicKey);
        $singlePayModel->copy($data);
        $result = $singlePayService->singlePay($singlePayModel);

        var_dump($result);
    }
}

/**
 * 代付款到银行
 *
 * @return void
 */
function test1()
{
    $newPayTest = new TestSinglePay();
    $merId = '';
    $orderId = substr(md5(rand()), 20);
    $payeeName = '';
    $payeeAccount = '';
    $tranAmt = 1;
    print PHP_EOL . '----------' . PHP_EOL;
    $newPayTest->singlePay($merId, $orderId, $payeeName, $payeeAccount, $tranAmt);
}

test1();
