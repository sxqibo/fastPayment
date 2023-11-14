<?php

require_once '../../vendor/autoload.php';

use Sxqibo\FastPayment\NewPay\NewPayCode;
use Sxqibo\FastPayment\NewPay\QueryOrderScanPayService;
use Sxqibo\FastPayment\NewPay\ScanPayModel;
use Sxqibo\FastPayment\NewPay\ScanPayQueryModel;
use Sxqibo\FastPayment\NewPay\ScanPayService;
use Sxqibo\FastPayment\NewPay\SinglePayInfoModel;
use Sxqibo\FastPayment\NewPay\SinglePayModel;
use Sxqibo\FastPayment\NewPay\SinglePayQueryModel;
use Sxqibo\FastPayment\NewPay\SinglePayQueryService;
use Sxqibo\FastPayment\NewPay\SinglePayService;

class NewPayTest
{
    /**
     * 测试扫码支付的调起 - 微信
     *
     * @return void
     */
    public function testScanPay($merId, $orderId, $weChatMchId)
    {
        $scanPayService = new ScanPayService();
        $merOrderNum = $orderId;

        $data = [
            // 商户ID
            'merId' => $merId,
            // 订单ID
            'merOrderNum' => $merOrderNum,
            // 支付金额（单位：分）
            'tranAmt' => 1,
            // 支付方式（微信：ORGCODE_WECHATPAY 阿里：ORGCODE_ALIPAY）
            'orgCode' => ScanPayModel::ORGCODE_WECHATPAY,
            // 进件号
            'weChatMchId' => $weChatMchId,
        ];

        $privateKey = 'MIICdgIBADANBgkqhkiG9w0BAQEFAASCAmAwggJcAgEAAoGBAIba/jcGy1o1M5eg+8WSq8lMiBNgBpxCpAeI9B60Sli4KJjDRDNIUrJ2AjDtOJYubUeccVvGU7xYMeXi5YiGDvOVUSFq/344b6/RrRhjjVnBWDIckXb1BPl4Pq7v7q+5RSExEc3lWEyIuNc7gXQBh0UFs5W14dOZmX2V5577h2RtAgMBAAECgYBMJBvC3/AzN7SwmTFupVifKLnwDpjM44ePxZDoQS11GE27qwy30871elHUZCw+B2qmMzuB8OHYhwdtYAXvZpnLrw+5R+L0PYjGtGWQTmk7Cu4325FQ0Ppo84jG/oL40oZ72pTkq33Wa2neAww+sgasbMY1oAmabesN3RCZzjMsoQJBAPLix445YXZ1sRub3tXFAGK3nbu2SJEtfcG1W+oKaUFn9WCjaoyU8P5uoK5bNYm35Su9h2LSY7gA5arZV8gdeDUCQQCOIv5wYeDXY4Te7N0uGtx4rhQrN4nj4pg5Vjb5V497Kzbq7iXMYRN0wgsDxRBJPXME4psOVO3ADzeUSFL03XJZAkAVsvr/EtNJQQR8ofVLhdkd+KeH4KYlCjpk9u3qP9nddQswAgl/28KYCIwkZ5Ol5R79RGZ3BrLP+oyKMfassy1NAkA4Z2ye/khyUNzOdiKDhEdPYI1CZSTEGQydXDgulG+sygZeDilTxIYrBEHIui/vUIJPQvmTI2LBn4hHwLei0inpAkEA21DAmRGy7LcERtXC7g93wzjUAuHbRF00WfOD1ZlQMqFWMEJZKZBbvxkGatq2saQiYZ6lC+Ig7onmCYT6ZZwl6w==';
        $publicKey = 'MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQCf21zJo95Uf9KKPLxagqzaT+L0fvsKuLG5cW5rzzHKwgfe8TkU2/ZnI2T0DowRvz7w98kfKBKxu0q/VV8QV21Ui/AxOXdfrbQEQ/QVSXtF8NtCpaXqcSOdNQF9dDNpx/VrDpaarv6xp/4nfbeAlf+t6IdUkfo8Rz0Ne5fihpsSRwIDAQAB';

        $scanPayModel = new ScanPayModel();
        $scanPayModel->setPrivateKey($privateKey);
        $scanPayModel->setPublicKey($publicKey);

        $scanPayModel->copy($data);

        $result = $scanPayService->scanPay($scanPayModel);
        print '返回的数据:' . json_encode($result, JSON_UNESCAPED_UNICODE) . PHP_EOL;

        $newPayCode = new NewPayCode();
        print $newPayCode->getResultCode($result['resultCode']) . PHP_EOL;
    }

    /**
     * 扫码支付查询
     *
     * @param $merId
     * @param $orderId
     * @return void
     */
    public function scanPayQuery($merId, $orderId)
    {
        $data = [
            'serialID' => md5(rand()),
            'mode' => ScanPayQueryModel::MODE_SINGLE,
            'type' => ScanPayQueryModel::TYPE_PAY,
            'orderID' => $orderId,
//            'orderID' => '',
//            'beginTime' => '20230922110000',
//            'endTime' => '20230922111059',
            'beginTime' => date('YmdH') . '0000',
            'endTime' => date('YmdH') . '5959',
//            'remark' => '',
            'partnerID' => $merId,
        ];

        $scanPayQueryModel = new ScanPayQueryModel();
        $scanPayQueryModel->copy($data);

        // 收款公私钥/商户私钥（收款）.pem
        $privateKey = 'MIICdgIBADANBgkqhkiG9w0BAQEFAASCAmAwggJcAgEAAoGBAIba/jcGy1o1M5eg+8WSq8lMiBNgBpxCpAeI9B60Sli4KJjDRDNIUrJ2AjDtOJYubUeccVvGU7xYMeXi5YiGDvOVUSFq/344b6/RrRhjjVnBWDIckXb1BPl4Pq7v7q+5RSExEc3lWEyIuNc7gXQBh0UFs5W14dOZmX2V5577h2RtAgMBAAECgYBMJBvC3/AzN7SwmTFupVifKLnwDpjM44ePxZDoQS11GE27qwy30871elHUZCw+B2qmMzuB8OHYhwdtYAXvZpnLrw+5R+L0PYjGtGWQTmk7Cu4325FQ0Ppo84jG/oL40oZ72pTkq33Wa2neAww+sgasbMY1oAmabesN3RCZzjMsoQJBAPLix445YXZ1sRub3tXFAGK3nbu2SJEtfcG1W+oKaUFn9WCjaoyU8P5uoK5bNYm35Su9h2LSY7gA5arZV8gdeDUCQQCOIv5wYeDXY4Te7N0uGtx4rhQrN4nj4pg5Vjb5V497Kzbq7iXMYRN0wgsDxRBJPXME4psOVO3ADzeUSFL03XJZAkAVsvr/EtNJQQR8ofVLhdkd+KeH4KYlCjpk9u3qP9nddQswAgl/28KYCIwkZ5Ol5R79RGZ3BrLP+oyKMfassy1NAkA4Z2ye/khyUNzOdiKDhEdPYI1CZSTEGQydXDgulG+sygZeDilTxIYrBEHIui/vUIJPQvmTI2LBn4hHwLei0inpAkEA21DAmRGy7LcERtXC7g93wzjUAuHbRF00WfOD1ZlQMqFWMEJZKZBbvxkGatq2saQiYZ6lC+Ig7onmCYT6ZZwl6w==';
        $scanPayQueryModel->setPrivateKey($privateKey);

        $queryOrderScanPayService = new QueryOrderScanPayService();
        var_dump($queryOrderScanPayService->query($scanPayQueryModel));
    }

    /**
     * 测试错误码
     *
     * @return void
     */
    public function testGetErrorCode()
    {
        $newPay = new NewPayCode();
        print $newPay->getErrorCode('A0001483');

        print $newPay->getErrorCode('111');
    }

    /**
     * 测试返回码
     *
     * @return void
     */
    public function testGetResultCode()
    {
        $newPay = new NewPayCode();
        print $newPay->getResultCode('4444');

        print $newPay->getResultCode('111');
    }

    public function testSinglePay($merId, $orderId, $payeeName, $payeeAccount, $tranAmt)
    {
        $data = [
            'merId' => $merId,
            'merOrderId' => $orderId,
            'payeeName' => $payeeName,
            'payeeAccount' => $payeeAccount,
            'tranAmt' => $tranAmt,

            'payType' => SinglePayInfoModel::PAYTYPE_BANK,
            'auditFlag' => SinglePayInfoModel::AUDITFLAG_NO,
            'payeeType' => SinglePayInfoModel::PAYEETYPE_PERSON,
        ];

        $singlePayService = new SinglePayService();
        $singlePayModel = new SinglePayModel();

        $singlePayModel->copy($data);

        $privateKey = 'MIICeAIBADANBgkqhkiG9w0BAQEFAASCAmIwggJeAgEAAoGBAL7ribDQ6o3u3KZHDys24FduzVAesq/+MrsMWnna+sLTQuZmO1ZLPYB7FegajsRkyB4vfitmUUP/wKzeeOyLUMsAXePd2nCRKDgV7GA3TmwAmXliIyJRRrO4b2iGovulsRiSupN6MPjRPwqSxty48wZv9xqzxxSlNo76/2xG4rPHAgMBAAECgYEAseF4f3KXJzinApFwc54EddH5ny2K5OjdUWNYJPK+8qitS0dy/5rIqZ2EsqWT4S3ZOi6byknha46tcSMy3YmL8otHVl+Gtk4nOWV+7e+fAKsRQ+GXV8dXWwA5M7EdxirRmTorebPS1xAN92X7zFpHSrT0Yy7L8zZp7mIfBgxyr3ECQQDpgMhZyI2NG0DitmxhLdpV6YhSwkMtNMd/ZO6EiNt68zqILKdjNtz0DIeQ486Gf8vxSeV4N1Is/aPcVC7qUH1pAkEA0VB4dv9JBUbe2u52wAH4gUcnXuWsMdIpTSSr/6ObGhxjQW+SsxiaAU60YPkAft1YfLW8JtMjQtE6y12n/yERrwJAN5tCmxcGlp7x4cudnbrkrubxXvwCMWbLR4xKvOc2lV4NB1bS+e6bycaeFiQaD6+paqm1at6JxEsW1aZ6kbRfWQJBAIviMX+lQBGMuWaqsyXCq2cKPF+JMjjhcMSjW2cu2XrrudGDVRDnwhRZmuarwg8Gsho2AhYYSJpg5d//KUSxvvUCQQCnl4CMhDpWSI16vkHkGSJ9OwfamnDFZk7D7OCQ1xq+2agZqyT/bGGSVBc2WVVWwQzqPlMBNOJkcZpQ/0Iaz7jB';
        $publicKey = 'MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQCSU82GM2kEr/s9mvUmMvZjm/Yq15nWSHWl+rPemOP0/WGCu7xI96OnK94IA5YMMdmou27Nlk2M8+g29IEOBleAwZkI8MW9FO8ceFI+l0uyBuisd6GEKPLOB7CQb7XKkis/a9dLqxR+aGiWgkC7/E8dlNUmJUV53TGrgZ2yiVeyVQIDAQAB';

        $singlePayModel->setPrivateKey($privateKey);
        $singlePayModel->setPublicKey($publicKey);
        $result = $singlePayService->singlePay($singlePayModel);

        var_dump($result);
    }

    /**
     * 测试付款到银行的查询
     */
    public function testSinglePayQuery($merId, $orderId, $payeeName, $payeeAccount, $tranAmt, $submitTime)
    {
        $data = [
            'merId' => $merId,
            'merOrderId' => $orderId,
            'submitTime' => $submitTime,
        ];

        $singlePayQueryModel = new SinglePayQueryModel();
        $singlePayQueryModel->copy($data);

        $privateKey = 'MIICeAIBADANBgkqhkiG9w0BAQEFAASCAmIwggJeAgEAAoGBAL7ribDQ6o3u3KZHDys24FduzVAesq/+MrsMWnna+sLTQuZmO1ZLPYB7FegajsRkyB4vfitmUUP/wKzeeOyLUMsAXePd2nCRKDgV7GA3TmwAmXliIyJRRrO4b2iGovulsRiSupN6MPjRPwqSxty48wZv9xqzxxSlNo76/2xG4rPHAgMBAAECgYEAseF4f3KXJzinApFwc54EddH5ny2K5OjdUWNYJPK+8qitS0dy/5rIqZ2EsqWT4S3ZOi6byknha46tcSMy3YmL8otHVl+Gtk4nOWV+7e+fAKsRQ+GXV8dXWwA5M7EdxirRmTorebPS1xAN92X7zFpHSrT0Yy7L8zZp7mIfBgxyr3ECQQDpgMhZyI2NG0DitmxhLdpV6YhSwkMtNMd/ZO6EiNt68zqILKdjNtz0DIeQ486Gf8vxSeV4N1Is/aPcVC7qUH1pAkEA0VB4dv9JBUbe2u52wAH4gUcnXuWsMdIpTSSr/6ObGhxjQW+SsxiaAU60YPkAft1YfLW8JtMjQtE6y12n/yERrwJAN5tCmxcGlp7x4cudnbrkrubxXvwCMWbLR4xKvOc2lV4NB1bS+e6bycaeFiQaD6+paqm1at6JxEsW1aZ6kbRfWQJBAIviMX+lQBGMuWaqsyXCq2cKPF+JMjjhcMSjW2cu2XrrudGDVRDnwhRZmuarwg8Gsho2AhYYSJpg5d//KUSxvvUCQQCnl4CMhDpWSI16vkHkGSJ9OwfamnDFZk7D7OCQ1xq+2agZqyT/bGGSVBc2WVVWwQzqPlMBNOJkcZpQ/0Iaz7jB';
        $publicKey = 'MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQCSU82GM2kEr/s9mvUmMvZjm/Yq15nWSHWl+rPemOP0/WGCu7xI96OnK94IA5YMMdmou27Nlk2M8+g29IEOBleAwZkI8MW9FO8ceFI+l0uyBuisd6GEKPLOB7CQb7XKkis/a9dLqxR+aGiWgkC7/E8dlNUmJUV53TGrgZ2yiVeyVQIDAQAB';

        $singlePayQueryModel->setPrivateKey($privateKey);
        $singlePayQueryModel->setPublicKey($publicKey);

        $singlePayQueryService = new SinglePayQueryService();
        $result = $singlePayQueryService->query($singlePayQueryModel);

        var_dump($result);
    }

    public function testRefund(string $merId, string $orderId, string $orgMerOrderId, string $orgSubmitTime, string $orderAmt, string $refundOrderAmt)
    {
        $data = [
            'merId' => $merId,
            'merOrderId' => $orderId,

            'orgMerOrderId' => $orgMerOrderId,
            'orgSubmitTime' => $orgSubmitTime,
            'orderAmt' => $orderAmt,
            'refundOrderAmt' => $refundOrderAmt,
        ];

        $refundModel = new \Sxqibo\FastPayment\NewPay\RefundModel();
        $refundService = new \Sxqibo\FastPayment\NewPay\RefundService();

        $refundModel->copy($data);

        $privateKey = 'MIICeAIBADANBgkqhkiG9w0BAQEFAASCAmIwggJeAgEAAoGBAIMIQTOH5YAbfGlyQnvxhcQ9KUQUVATswehSCv35SwT33Dzqdz3G2fH54Ol8+jo4MeIdPFYzBlAzAauN7cSp3Wb9YgnJseXFeESfqm/c/IZdF9Qq64qAQMOvwivM5/oDkY9kQ+O+AqjxoxiSYbkkJknzwGYfBhws1i9v8TpLbLd7AgMBAAECgYADgUAFOdhYseOFCopeuFokoqIs8QJCU3boWPF0U6u/CUY51ueznlMMFuv+MtqanhAvhSqs/5ZmpMahqR04CncilUuNoEHUcr6Ll7TUICLbg/3GKyal1BoN76kAMw9gOxjhS5MpCehPBpQOKJvA29kd2d9p6lBYFY1Q+fm9gWivqQJBAL+9NLfxh83oMPFUCdqIhOYE7YQWEwfJLrif01oLMrwGm6ts+GRPTzqGdT4XTmKem0HQFZ2xJCoVLyiZIBVHf50CQQCu8ojd16E0m0W8ANinCZjHpJ71kuCnEcop9rOQ3JcEydYCqrogZ2v8d4KqnEZOKmjkSEI7x/yNkzb/1AQjbMP3AkEAnqobbfvYvXNezNUWMli2YQHC6oK5zL+WggEADIsnuKBfQUQzaA6ZKX0KmA8BNmq5X4Sp3owvuQM+uwG7ouA/oQJBAJjVbrYHG2CeyTPttVdvrdWzPd8hWjr58pfoWoSSASiAvwKDbajDUPY03aT4cv70U8AiVCJvmnWAB0tFR/w+l48CQQCqFYXBYUl9eI5pooY85puqdM3dVgBTbviKJulzaSd/dMV9/05E06VP4h4eZdFniYO9iAydZBUyzZwkJSzOdE0b';
        $publicKey = 'MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQC4Ybi8UscW3Cq4yFoLqZAmTv+3dtzBvc6mOKg/Ec75OJm+BfOpR8wM9eKa/rhBXnudSgXsoDEaTO7wmRtSHL+aLpdHQfVTwPjzkJjKx7rMHwTqgCu5ASDabz4vY6QCSJ9KoYET5lsRU/qB7/XQxNnSDA7Q8I7jEGXpEfLmTrOZrQIDAQAB';

        $refundModel->setPrivateKey($privateKey);
        $refundModel->setPublicKey($publicKey);

        $result = $refundService->refund($refundModel);

        var_dump($result);
    }
}

/**
 * 扫码付款
 *
 * @return void
 */
function test1()
{
    $merId = '11000008001';
    $orderId = substr(md5(rand()), 20);
    $newPayTest = new NewPayTest();
    $weChatMchId = '2309072230343403012';
    $newPayTest->testScanPay($merId, $orderId, $weChatMchId);

    print PHP_EOL . '----------' . PHP_EOL;
    $newPayTest->scanPayQuery($merId, $orderId);
}

/**
 * 代付款到银行
 *
 * @return void
 */
function test2()
{
    $newPayTest = new NewPayTest();
    $merId = '11000008001';
//    $merId = '';
//    $orderId = 'e56fed8c52ad';
    $orderId = substr(md5(rand()), 20);
    $payeeName = '杨红伟';
    $payeeAccount = '6227000267070109093';
    $tranAmt = 0.01;
    print PHP_EOL . '----------' . PHP_EOL;
    $newPayTest->testSinglePay($merId, $orderId, $payeeName, $payeeAccount, $tranAmt);
//    exit;
    $submitTime = '20231012';
    print PHP_EOL . '----------' . PHP_EOL;
    $newPayTest->testSinglePayQuery($merId, $orderId, $payeeName, $payeeAccount, $tranAmt, $submitTime);
}

/**
 * 退款
 *
 * @return void
 */
function test3()
{
    $merId = '11000008298';
    $orderId = substr(md5(rand()), 20);
    echo '............' . $orderId;
    $orgMerOrderId = '2c72dfeb1fd0';
    $orgSubmitTime = '20231113141442';
    $orderAmt = '0.01';
    $refundOrderAmt = '0.01';
    $newPayTest = new NewPayTest();
    $newPayTest->testRefund($merId, $orderId, $orgMerOrderId, $orgSubmitTime, $orderAmt, $refundOrderAmt);
}

//test1();
//test2();
test3();
