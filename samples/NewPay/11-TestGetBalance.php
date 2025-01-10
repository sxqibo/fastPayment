<?php

use Monolog\Logger;
use Monolog\Handler\StreamHandler;

require_once '../../vendor/autoload.php';

class TestBalance
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

    /**
     * 测试退款
     */
    public function balance(string $merId)
    {
        $data = [
            'merId' => $merId,
        ];

        $balanceModel   = new \Sxqibo\FastPayment\NewPay\BalanceModel();
        $balanceService = new \Sxqibo\FastPayment\NewPay\BalanceService();

        $privateKey = $this->config['service_corp']['new_private_key'];
        $publicKey  = $this->config['service_corp']['new_public_key'];

        $balanceModel->setPrivateKey($privateKey);
        $balanceModel->setPublicKey($publicKey);

        $balanceModel->copy($data);

        $result = $balanceService->queryBalance($balanceModel);

        var_dump($result);
        $this->logger->info("查询商户余额：" . json_encode($result, 256));
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
 * 退款相关
 */
$test = new TestBalance($logger);

$merId = $test->getConfig()['service_corp']['merch_id']; // 新生支付平台提供给商户的唯一ID
$test->balance($merId);

$logger->info("===以上为查询商户账户余额相关日志============");
print "余额 相关信息请查看日志！";