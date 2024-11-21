<?php
/**
 * 微信消费者投诉接口
 */
require_once '../../vendor/autoload.php';

use Sxqibo\FastPayment\WeChatPayV3\Complaints;

$options = [
    // 微信绑定APPID
    'appid'        => '',

    // 微信商户编号
    'mch_id'       => '',

    // 微信商户密钥
    'mch_v3_key'   => '',

    // 商户API私钥
    'cert_private' => '',

    // 商户API公钥
    'cert_public'  => '',
];

try {
    $service = new Complaints($options);
    // 测试查询投诉单列表
    $result = $service->getBillList();
    echo '>>> 手机号' . PHP_EOL;
    foreach ($result['data']['data'] as $r) {
        echo $service->getDecrypt($r['payer_phone'], $options['cert_private']) . PHP_EOL;
    }
    dd($result, '测试查询投诉单列表');


//    // 测试查询投诉单详情
//    $complainId = ''; // 请填具体内容
//    if ($complainId) {
//        $result = $service->getBillDetail($complainId);
//        dd($result, '测试查询投诉单详情');
//    }
//
//    // 测试查询投诉单协商历史
//    $complainId = '';
//    if ($complainId) {
//        $result = $service->getBillNegotiationHistory($complainId);
//        dd($result, '测试查询投诉单协商历史');
//    }
//
//    $complainId = ''; // 请填具体内容
//    if ($complainId) {
//        $result = $service->handleResponse($complainId,
//            [
//                'complainted_mchid' => $options['mch_id'],
//                'response_content'  => '测试投诉回复，……',
//            ]);
//        dd($result, '回复用户');
//    }
//
//    $complainId = ''; // 请填具体内容
//    if ($complainId) {
//        $result = $service->handleComplete($complainId,
//            [
//                'complainted_mchid' => $options['mch_id'],
//            ]);
//        dd($result, '反馈处理完成');
//    }
//
//
//    $complainId = ''; // 请填具体内容，功能写完了，但遇到不了真实场景，待验证
//    if ($complainId) {
//        $result = $service->handleUpdateRefundProgress($complainId,
//            [
//                'action'            => 'APPROVE',
//                'launch_refund_day' => 0
//            ]);
//        dd($result, '更新退款审批结果');
//    }


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


