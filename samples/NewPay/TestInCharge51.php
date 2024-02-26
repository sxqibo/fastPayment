<?php
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

require_once '../../vendor/autoload.php';

class TestInCharge
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
     * 测试公众号支付
     */
    public function mpPay(string $orderId)
    {
        $data = [
            // 1、服务商和进件信息
            'merId'        => $this->config['service_corp']['merch_id'], // 服务商ID
            'merchantId'   => $this->config['merchant_corp']['wechat_mch_id'], // 进件成功进件号

            // 2、订单信息
            'merOrderId'   => $orderId,
            'tranAmt'      => $this->config['mp_info']['pay_amount'],
            'goodsInfo'    => $this->config['mp_info']['orderSubject'],
            'orderSubject' => $this->config['mp_info']['goodsInfo'],
            'orgCode'      => 'WECHATPAY', // 微信支付

            // 3、公众号信息
            'appId'  => $this->config['wechat_app_info']['appId'], // 微信公众号appid
            'openId' => $this->config['wechat_app_info']['openId'], //openid
        ];



        $inChargeModel   = new \Sxqibo\FastPayment\NewPay\InchargeModel();
        $inChargeService = new \Sxqibo\FastPayment\NewPay\InChargeService();

        $privateKey = $this->config['service_corp']['refund_private_key'];
        $publicKey  = $this->config['service_corp']['refund_public_key'];

        $inChargeModel->setPrivateKey($privateKey);
        $inChargeModel->setPublicKey($publicKey);

        $inChargeModel->copy($data);

        $result = $inChargeService->inCharge($inChargeModel);

        var_dump($result);
        $this->logger->info("公众号支付相关信息：" . json_encode($result, 256));
        // 返回示例,仅作为参考
        // {"charset":"1","hnapayOrderId":"2024022687381757","resultCode":"0000","errorCode":"","version":"2.0","signValue":"gryPtLAi6g\/MEOJzEmGePHdErEMcTyqvLV1LEf8k3NZF+s5MOoetVu7B2t\/KEGD0ZtvbINDW25KmomMuyo2riGt0E7Dj5izl8mBcTDfv8sRaqRs9b9sgMPGHEpWGFFd8R1+3uB\/9\/kA2v9a7velRvvS1RhxeUnDfnwdvp5nWILM=","errorMsg":"","signType":"1","merId":"11000008561","tranCode":"ITA10","merAttach":"","payInfo":{"timeStamp":"1708941728","package":"prepay_id=wx261802083523825ef0dae8bec9770f0000","paySign":"23Qv3HK2YuM2qGdqvYwjQWkn00i674vcp\/iGz6Ue8FYiyXdMe4gO31DEPZpT1zifROulbglvvZjSWX53NauPvrRJuT3HgmZ\/sQCnVOfwD8BkxDPHbKlk9i+fFz0FXvUMl5hsjhB2uM0pzU\/QgvAmL2d7BKdFCuT\/6mI8Z1krgQr7lDukDcAvdHlRHfFVxh5cHEQZt3TNCEwQQvrmwhqrMsAhRwu3MHhiEh2\/x\/qrGkJUQvNIbdW7DBnSmgUPKHIsKI5vTHwlRujWL0gSfAR5tMy\/OzTjXdaMA6bjLc+gfxMbVM9v8pOR1t1rVWo2avp1A8tbgmRYOqmLITyG9dJVNg==","orderId":"2402261802081418745","appId":"wxb27504edb7e17eb5","signType":"RSA","nonceStr":"598183e4537d4906ab285e2caa2ccd7d"},"merOrderId":"1c9486f20ef3b66ba62a"}%

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
 * 公众号支付相关
 */
$test = new TestInCharge($logger);

$orderId = substr(md5(time()), 0, 20); // 微信公众号支付订单号
$logger->info("微信公众号支付订单号为：" . $orderId);

$test->mpPay($orderId);

$logger->info("===以上为以上为微信公众号支付 相关日志============");
print "微信公众号支付 相关信息请查看日志！";