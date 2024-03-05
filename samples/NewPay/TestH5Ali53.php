<?php
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

require_once '../../vendor/autoload.php';

class TestH5Ali53
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
     * 测试支付宝支付
     */
    public function h5Pay(string $orderId)
    {
        $data = [
            // 1、服务商和进件信息
            'merId'        => $this->config['service_corp']['merch_id'], // 服务商ID
            'merchantId'   => $this->config['merchant_corp']['alipay_mch_id'], // 进件成功进件号

            // 2、订单信息
            'merOrderId'   => $orderId,
            'tranAmt'      => $this->config['h5_info']['pay_amount'],
            'orderSubject' => $this->config['h5_info']['orderSubject'],
            'payType'      => 'HnaALL', // 必填：HnaALL 为在H5 收银台页面选择支付方式(默认)

        ];

        $inChargeModel   = new \Sxqibo\FastPayment\NewPay\H5Model();
        $inChargeService = new \Sxqibo\FastPayment\NewPay\H5Service();

        $privateKey = $this->config['service_corp']['new_private_key'];
        $publicKey  = $this->config['service_corp']['new_public_key'];

        $inChargeModel->setPrivateKey($privateKey);
        $inChargeModel->setPublicKey($publicKey);

        $inChargeModel->copy($data);

        $result = $inChargeService->h5($inChargeModel);

        echo $result;
        $this->logger->info("支付宝H5支付相关信息：" . $result);

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
 * 支付宝H5支付相关
 */
$test = new TestH5Ali53($logger);

$orderId = substr(md5(time()), 0, 20); // 微信公众号支付订单号
$logger->info("支付宝H5支付订单号为：" . $orderId);

$test->h5Pay($orderId);

$logger->info("===以上为支付宝H5支付 相关日志============");
print "支付宝H5支付 相关信息请查看日志！";