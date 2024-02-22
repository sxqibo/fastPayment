<?php

use Sxqibo\FastPayment\NewPay\SinglePayInfoModel;
use Sxqibo\FastPayment\NewPay\SinglePayModel;
use Sxqibo\FastPayment\NewPay\SinglePayService;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

require_once '../../vendor/autoload.php';

class TestTransfer54
{
    private $config;
    private $logger;

    public function __construct($logger)
    {
        $this->config = include 'config.php';
        $this->logger = $logger;
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
            'tranAmt'      => (string)$tranAmt,

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
        $this->logger->info("转账结果：". json_encode($result, 256));
    }
}

/**
 * 日志相关
 */
// 创建一个日志记录器对象
$logger = new Logger('newPayLogger');

// 创建一个日志处理器对象，将日志记录到日志文件中
$handler = new StreamHandler('./logfile.log');
$logger->pushHandler($handler);


/**
 * 代付款到银行相关
 */
$test = new TestTransfer54($logger);

$merId   = $test->getConfig()['service_corp']['merch_id']; // 服务商-商户ID
$orderId = substr(md5(rand()), 20);

$logger->info("我的转账订单号是：" . $orderId);
$logger->info("我的转账时间是：" . date('Ymd', time()));

$payeeName    = $test->getConfig()['transfer_info']['user_name'];           // 收款方姓名
$payeeAccount = $test->getConfig()['transfer_info']['user_card_number'];    // 收款方账户
$tranAmt      = $test->getConfig()['transfer_info']['transfer_amount'];     // 支付金额

$test->singlePay($merId, $orderId, $payeeName, $payeeAccount, $tranAmt);

$logger->info("===以上为 代付款到银行 相关日志============");
print "代付款到银行 相关信息请查看日志！";