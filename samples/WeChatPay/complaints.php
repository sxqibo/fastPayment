<?php
/**
 * 微信消费者投诉接口
 */
require_once '../../vendor/autoload.php';

use Sxqibo\FastPayment\WeChatPay\Complaints;

$options = [
    // 微信绑定APPID
    'appid'        => '',

    // 微信商户编号
    'mch_id'       => '',

    // 微信商户密钥
    'mch_v3_key'   => '',

    // 商户API私钥
    'cert_private' => '
',

    // 商户API公钥
    'cert_public'  => '',
];

try {
    $service = new Complaints($options);
    // 测试查询投诉单列表
    $result = $service->getBillList();
    dd($result, '测试查询投诉单列表');

    // 测试查询投诉单详情
    $complainId = '';
    if ($complainId) {
        $result = $service->getBillDetail($complainId);
        dd($result, '测试查询投诉单详情');
    }

    // 测试查询投诉单协商历史
    $complainId = '';
    if ($complainId) {
        $result = $service->getBillNegotiationHistory($complainId);
        dd($result, '测试查询投诉单协商历史');
    }

} catch (\Exception $e) {
    dd($e->getMessage(), '错误信息');
}


function dd($data, $title = '打印测试')
{
    print_r('>>' . $title);
    print_r(PHP_EOL);
    print_r($data);
    print_r(PHP_EOL);
    print_r(str_repeat('----', 20));
    print_r(PHP_EOL);
}


