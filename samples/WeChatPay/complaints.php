<?php
/**
 * 微信消费者投诉接口
 */
require_once '../../vendor/autoload.php';

use Sxqibo\FastPayment\WeChatPay\Complaints;

$options = [
    // 参数一：商户号
    'mch_id' => '712841083',

    // 参数二：商户API私钥
    'cert_private' => '-----BEGIN PRIVATE KEY-----
MIIEvgIBADANBgkqhkiG9w0BAQEFAASCBKgwggSkAgEAAoIBAQC/FBExdJgEPkkU
g+3lmj9AX70xErwL9m/SyJ2S5E4II3UYuxa7QHz7DcQT4ryt101U7xaYHZLPT6go
lDgbHVcnt0oynszpHYbBO+h4PB4iWeOOYm/s6q0Uy34MJuZdTCWo6MlHJqAGajIh
aUzmcLbKIvmE1qttqE8y3YKeob9CrJ7FALyENxCemwMsnSzekCq4q5MG8UP5QKLh
3WCMzoZyNm/otcr4muCA8UcY6t0wu9hwyk6+mWzpeEGuhbBjjL4Yk+QEJhs8iXNC
Cq3Ql0v1YgT5khrxP7eTdDuWgJFe6Y6UTpCv7+dlL7/ipfFDjxnXNqKq3gaA2D5u
cqij1yzHAgMBAAECggEAdBqvb8uW600lfs/DaDZXpLgH75+gn+w4em3oQW90csGI
z2QvJczDpJDyydqGJ2Oh27ADnJ6rrEiMt0uI5ADqCnn3HdccT7HfRd8vHI/7B4hz
Gvt5Yw4d6XrUtnGUnLA+WepJE3DG3977Yw3m6kcm6qBh2XPPaqxQo2mxis5htxK7
ZiQm9z1+KLeqhf0ENqgM0193DyJ3radOqCR/KrJ4Qq1U03MSmLC+sNsP1E/VzvCV
t4qWBIFno3doHb54pmu6Nfv8VZhvi3DzvhSDYDjyFJRPeKp1BXfYUnWQBa5+kYjw
/xntsW4sbagxcxwS2Hk4qIFfaeUTNpUhk+LHNIu4QQKBgQDszM2mjIPCVqkGwm9b
wyLkatjVqNsk1nehSw71XLlo3KIlYIUob38iS2mY2QsDLg6rRYYsmRcTYfmZdv4e
6tQ0MtaVvHVel5S94KTrEGn6pU3ZklHvBevNONUrNFbYdhzuklQZi1v5CzAAbrl3
zgWB/KEfmS8jAPmmB8S4LXx2UQKBgQDOkjtb1mxJURnIsUGxWhdaapiE/BrIl9tM
zq0mD0yGJQei65pE/O4uVuVkepUmgYKnRe4YgmtLulacC+1OCuerTWSgpWOI66rZ
XHVsoNrS5BAUo8hr5bDV/Hf84kgkD6t5yG+31OcNyd2a1we7ZnfoQHOYXYZUc8tc
If5xtRdzlwKBgQCaJNM1uEBIsCrFKKpenE7JS7gslQdaGnWzO+3X0G0tEnpGRGdJ
pBKpG1f41Egz4LZRzScDPwBjcKKOwIO5UnmiJPnEbPImChwb4fDYx02FiDd+Cp5l
LoCJjZZN0ns52uEId55hJnNPUXYEwg7fKvAw5mdn70pcydS4vFPU5F8hkQKBgQCI
hYqncnoUpn3k7oldHg6LGiH82eUVp48vHvS+T6Qij/yRBybo60S66YEnvAAw960S
whvOpPsmjFtLPHK0fm6H/1k+9q9mwWIjz4Bnr5OPh1y9V1VRQfdyJS5jumU1OAn7
LaXwF8wwh7Zm7DBXASzEGTyMeVsbG4BOPsU7/xQltwKBgDTsBwjRDJ53JmVKZs8I
ADFczyoIe8mYHw3GXnXuT6FSQWQYT5bJpswwCGESZDjwANEzvg7OUQfkmrtiKiy3
d7Q1oNarV/Zg6jb8OFyBoaLj9zg1P59rFtEDiKyE6QyhAwbmoaYCLjPjPb65uzVJ
WJSuxyukXPd7NouCovwhu1h3
-----END PRIVATE KEY-----',

    // 参数三：「商户API证书」的「证书序列号」
    'merchant_certificate_serial' => '7B46CC301AFCEE1E16BFF42F812E257B70A57B05',

    // 参数四：微信支付平台公钥
    'platform_public_key' =>  '-----BEGIN PUBLIC KEY-----
MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEA2tCVByzVh7ypG6ZpboS7
eg0uMPeN1xN4Z6/+k6EuRI6IOPeAnoYdfJHRNVXnKY7YB+J/tTHne9fAa3IxpBoS
Omqi+7++dQKmBJcACPlaD2UTApKoUjup6KVh+tEQTWJ3g6tzl15px80QtMZK1JGc
9jM2j8SolCg7/qa7TQUzYdLQjn5jf4tzDVTLhs82o4H01A+vzwMoJZYXf8Xnk/CB
R0rakfTnk7YFuhz+dDZV2ru0VjcM67dQoWG2LYWCWVfe3YKZDyEVkbfjAmyoCESa
vbh3IRGyV5QlNvY9iq9oUNSEXNlRwwCEQ2s/krsGhlgHqHHTAC7nZaBxiqQtPntr
RQIDAQAB
-----END PUBLIC KEY-----',

    // 参数五：平台公钥ID
    'public_key_id' => 'PUB_KEY_ID_0118000073852024111500337900000304',
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
    $complainId = '200000020241119210228040055'; // 请填具体内容
    $result = $service->getBillDetail($complainId);
    dd($result, '测试查询投诉单详情');

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


