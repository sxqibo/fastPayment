<?php

use Sxqibo\FastPayment\NewPay\SinglePayInfoModel;
use Sxqibo\FastPayment\NewPay\SinglePayModel;
use Sxqibo\FastPayment\NewPay\SinglePayService;

require_once '../../vendor/autoload.php';

class TestTransfer54
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

    public function singlePay($merId, $orderId, $payeeName, $payeeAccount, $tranAmt)
    {
        $data = [
            'merId'        => $merId,
            'merOrderId'   => $orderId,
            'payeeName'    => $payeeName,
            'payeeAccount' => $payeeAccount,
            'tranAmt'      => $tranAmt,

            'payType'   => SinglePayInfoModel::PAYTYPE_BANK,
            'auditFlag' => SinglePayInfoModel::AUDITFLAG_NO,
            'payeeType' => SinglePayInfoModel::PAYEETYPE_PERSON,
        ];

        $singlePayService = new SinglePayService();
        $singlePayModel   = new SinglePayModel();


        // 收款的公私钥
        $privateKey = $this->config['service_corp']['transfer_private_key'];
        $publicKey  = $this->config['service_corp']['transfer_public_key'];


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
    $test = new TestTransfer54();

    $merId   = $test->getConfig()['service_corp']['merch_id']; // 服务商-商户ID
    $orderId = substr(md5(rand()), 20);

    print "我的转账订单号是：" . $orderId;
    print PHP_EOL . '----------' . PHP_EOL;
    print "我的转账时间是：" . date('Ymd', time());
    print PHP_EOL . '----------' . PHP_EOL;


    $payeeName    = $test->getConfig()['transfer_info']['user_name'];           // 收款方姓名
    $payeeAccount = $test->getConfig()['transfer_info']['user_card_number'];    // 收款方账户
    $tranAmt      = $test->getConfig()['transfer_info']['transfer_amount'];     // 支付金额

    print PHP_EOL . '----------' . PHP_EOL;
    $test->singlePay($merId, $orderId, $payeeName, $payeeAccount, $tranAmt);
}

test1();
