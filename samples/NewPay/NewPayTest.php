<?php

require_once '../../vendor/autoload.php';

use Sxqibo\FastPayment\NewPay\NewPayCode;
use Sxqibo\FastPayment\NewPay\QueryOrderScanPayService;
use Sxqibo\FastPayment\NewPay\ScanPayModel;
use Sxqibo\FastPayment\NewPay\ScanPayQueryModel;
use Sxqibo\FastPayment\NewPay\ScanPayService;
use Sxqibo\FastPayment\NewPay\SinglePayInfoModel;
use Sxqibo\FastPayment\NewPay\SinglePayModel;
use Sxqibo\FastPayment\NewPay\SinglePayQueryModel;
use Sxqibo\FastPayment\NewPay\SinglePayQueryService;
use Sxqibo\FastPayment\NewPay\SinglePayService;

class NewPayTest
{
    /**
     * 测试扫码支付的调起 - 微信
     *
     * @return void
     */
    public function testScanPay($merId, $orderId, $weChatMchId, $amount)
    {
        $scanPayService = new ScanPayService();
        $merOrderNum = $orderId;

        $data = [
            // 商户ID
            'merId' => $merId,
            // 订单ID
            'merOrderNum' => $merOrderNum,
            // 支付金额（单位：分）
            'tranAmt' => $amount,
            // 支付方式（微信：ORGCODE_WECHATPAY 阿里：ORGCODE_ALIPAY）
            'orgCode' => ScanPayModel::ORGCODE_WECHATPAY,
            // 进件号
            'weChatMchId' => $weChatMchId,
        ];

        // 收款公私钥
        $privateKey = '';
        $publicKey = '';

        $scanPayModel = new ScanPayModel();
        $scanPayModel->setPrivateKey($privateKey);
        $scanPayModel->setPublicKey($publicKey);

        $scanPayModel->copy($data);

        $result = $scanPayService->scanPay($scanPayModel);
        print '返回的数据:' . json_encode($result, JSON_UNESCAPED_UNICODE) . PHP_EOL;

        $newPayCode = new NewPayCode();
        print $newPayCode->getResultCode($result['resultCode']) . PHP_EOL;
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
        $scanPayQueryModel->copy($data);

        // 收款私钥
        $privateKey = '';
        $scanPayQueryModel->setPrivateKey($privateKey);

        $queryOrderScanPayService = new QueryOrderScanPayService();
        var_dump($queryOrderScanPayService->query($scanPayQueryModel));
    }

    /**
     * 测试错误码
     *
     * @return void
     */
    public function testGetErrorCode()
    {
        $newPay = new NewPayCode();
        print $newPay->getErrorCode('A0001483');

        print $newPay->getErrorCode('111');
    }

    /**
     * 测试返回码
     *
     * @return void
     */
    public function testGetResultCode()
    {
        $newPay = new NewPayCode();
        print $newPay->getResultCode('4444');

        print $newPay->getResultCode('111');
    }

    public function testSinglePay($merId, $orderId, $payeeName, $payeeAccount, $tranAmt)
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

        $singlePayModel->copy($data);

        // 收款的公私钥
        $privateKey = '';
        $publicKey = '';

        $singlePayModel->setPrivateKey($privateKey);
        $singlePayModel->setPublicKey($publicKey);
        $result = $singlePayService->singlePay($singlePayModel);

        var_dump($result);
    }

    /**
     * 测试付款到银行的查询
     */
    public function testSinglePayQuery($merId, $orderId, $payeeName, $payeeAccount, $tranAmt, $submitTime)
    {
        $data = [
            'merId' => $merId,
            'merOrderId' => $orderId,
            'submitTime' => $submitTime,
        ];

        $singlePayQueryModel = new SinglePayQueryModel();
        $singlePayQueryModel->copy($data);

        // 付款的公私钥
        $privateKey = '';
        $publicKey = '';

        $singlePayQueryModel->setPrivateKey($privateKey);
        $singlePayQueryModel->setPublicKey($publicKey);

        $singlePayQueryService = new SinglePayQueryService();
        $result = $singlePayQueryService->query($singlePayQueryModel);

        var_dump($result);
    }

    public function testRefund(string $merId, string $orderId, string $orgMerOrderId, string $orgSubmitTime, string $orderAmt, string $refundOrderAmt)
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

        $refundModel->copy($data);

        // 新收款公私钥
        $privateKey = '';
        $publicKey = '';

        $refundModel->setPrivateKey($privateKey);
        $refundModel->setPublicKey($publicKey);

        $result = $refundService->refund($refundModel);

        var_dump($result);
    }
}

/**
 * 扫码付款
 *
 * @return void
 */
function test1()
{
    $merId = '';
    $orderId = substr(md5(rand()), 20);
    $newPayTest = new NewPayTest();
    $weChatMchId = '';
    $amount = 2;
    $newPayTest->testScanPay($merId, $orderId, $weChatMchId, $amount);

    print PHP_EOL . '----------' . PHP_EOL;
    $newPayTest->scanPayQuery($merId, $orderId);
}

/**
 * 代付款到银行
 *
 * @return void
 */
function test2()
{
    $newPayTest = new NewPayTest();
    $merId = '';
//    $merId = '';
//    $orderId = 'e56fed8c52ad';
    $orderId = substr(md5(rand()), 20);
    $payeeName = '';
    $payeeAccount = '';
    $tranAmt = 0.01;
    print PHP_EOL . '----------' . PHP_EOL;
    $newPayTest->testSinglePay($merId, $orderId, $payeeName, $payeeAccount, $tranAmt);
//    exit;
    $submitTime = '20231012';
    print PHP_EOL . '----------' . PHP_EOL;
    $newPayTest->testSinglePayQuery($merId, $orderId, $payeeName, $payeeAccount, $tranAmt, $submitTime);
}

/**
 * 退款
 *
 * @return void
 */
function test3()
{
    $merId = '';
    $orderId = substr(md5(rand()), 20);
    echo '............' . $orderId;
    $orgMerOrderId = '2c72dfeb1fd0';
    $orgSubmitTime = '20231113141442';
    $orderAmt = '0.01';
    $refundOrderAmt = '0.01';
    $newPayTest = new NewPayTest();
    $newPayTest->testRefund($merId, $orderId, $orgMerOrderId, $orgSubmitTime, $orderAmt, $refundOrderAmt);
}

//test1();
//test2();
test3();
