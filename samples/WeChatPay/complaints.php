<?php
/**
 * 微信消费者投诉接口
 */
require_once '../../vendor/autoload.php';

use Sxqibo\FastPayment\WeChatPay\Complaints;

$options = [
    // 参数一：商户号
    'mch_id' => '',

    // 参数二：商户API私钥
    'cert_private' => '',

    // 参数三：「商户API证书」的「证书序列号」
    'merchant_certificate_serial' => '',

    // 参数四：微信支付平台公钥
    'platform_public_key' =>  '',

    // 参数五：平台公钥ID
    'public_key_id' => '',
];

try {
    $service = new Complaints($options);
    // 测试查询投诉单列表
//    $result = $service->getBillList();
//    echo '>>> 手机号' . PHP_EOL;
//    foreach ($result['data'] as $r) {
//        echo $service->getDecrypt($r['payer_phone'], $options['merchantPrivateKeyContent']) . PHP_EOL;
//    }
//    dd($result, '测试查询投诉单列表');


    // 测试查询投诉单详情
//    $complainId = '200000020241119210228040055'; // 请填具体内容
//    $result = $service->getBillDetail($complainId);
//    dd($result, '测试查询投诉单详情');

    // 测试查询投诉单协商历史
//    $complainId = '200000020241119210228040055';
//    $result     = $service->getBillNegotiationHistory($complainId);
//    dd($result, '测试查询投诉单协商历史');

//    $complainId = '200000020241119210228040055'; // 请填具体内容
//    $result     = $service->handleResponse($complainId,
//        [
//            'complainted_mchid' => $options['mchid'],
//            'response_content'  => '测试投诉回复，……',
//        ]
//    );
//    dd($result, '回复用户');


//    $complainId = ''; // 请填具体内容
//    if ($complainId) {
//        $result = $service->handleComplete($complainId,
//            [
//                'complainted_mchid' => $options['mch_id'],
//            ]);
//        dd($result, '反馈处理完成');
//    }

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


