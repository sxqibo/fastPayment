<?php
require_once '../../vendor/autoload.php';

use Sxqibo\FastPayment\UmfPay\UmfService;

/**
 * 扫码支付
 * @doc https://www.yuque.com/umpayer/tv9uf6/dyx054r5g0yz626r
 * @param $appId
 * @param $pub
 * @param $private
 * @param $type
 * @return false|string
 * @throws \GuzzleHttp\Exception\GuzzleException
 * @throws Exception
 */
function qrcode($config)
{

    // 基本信息
    $appId   = $config['basic']['appid'];
    $pub     = $config['basic']['pub'];
    $private = $config['basic']['private'];

    // 扫码信息
    $scanInfo                  = $config['scan_info'];

    // 接口业务参数列表
    $scanInfo['scancode_type'] = 'WECHAT'; // 扫码类型：微信

    // 支付
    $result = (new UmfService($appId, $pub, $private))->submit($scanInfo);

    // 结果
    if (isset($result['ret_code']) && $result['ret_code'] == '0000') {
        return base64_decode($result['bank_payurl']);
    } elseif (isset($result['ret_code'])) {
        throw new Exception('[' . $result['ret_code'] . ']' . $result['ret_msg']);
    } else {
        throw new Exception('返回数据解析失败');
    }
}

$config = include 'config.php';
try {
    $result = qrcode($config);
} catch (\GuzzleHttp\Exception\GuzzleException $e) {
    throw new \Exception('微信支付失败1！' . $e->getMessage());
} catch (Exception $e) {
    throw new \Exception('微信支付失败2！' . $e->getMessage());
}

print_r($result);