# 拉卡拉开放平台 API对接 开发说明文档

## 开发范围

- 聚合收银台
    - 订单创建
    - 订单查询
    - 订单关单
- 商户服务
    - 扫码银行卡退货
    - 扫码银行卡退货查询
    - 统一退货
    - 退货查询

## 测试用例

将下面代码放到控制器，在浏览器路由访问即可。

```php

use Sxqibo\FastPayment\LakalaPay\services\AggregationCashierDesk;
use Sxqibo\FastPayment\LakalaPay\services\Merchant;

public function lakalaPayTest($config): void
{
    $options = [
        'appid'       => $config->app_id,
        'serial_no'   => $config->serial_no,
        'merchant_no' => $config->merchant_no,
        'term_no'     => $config->term_no,
        'private_key' => $config->merchant_private_key,
        'certificate' => $config->lakala_certificate,
        'test_env'    => $config->test_env == 1
    ];

    /**
     * 聚合收银台服务 实例对象
     */
    $aggregationCashierDeskService = new AggregationCashierDesk($options);

    /**
     * 商户服务 实例对象
     */
    $merchantService = new Merchant($options);

    switch ($this->request->param('action')) {
        case 'orderCreate': // 收银台订单创建测试
            $order = $aggregationCashierDeskService->counterOrderSpecialCreate('ORD' . date('YmdHis'), 1, '测试订单');
            dump($order);
            break;
        case 'responseSignVerify': // API响应验签测试
            $headers = [
                'Lklapi-Appid'     => 'OP00000003',
                'Lklapi-Serial'    => '1745381c327',
                'Lklapi-Timestamp' => '1724902691',
                'Lklapi-Nonce'     => '1KMsPLtVSiwI',
                'Lklapi-Signature' => 'XECZGy0wBygoM85BPROUeV1HXPzET53Ua/2E8gUuxugKwSjvwgmZFX8CY26iGVxAr2H+bJBqIUXX2CyohVfSaXMeDSCzxV3iYHJ2P7CpvqLTYSDH7XduCvPkJYStdQULSZsN2Y/Ano91f8ysVBGeejsS15GOywUPD+Jc9EqHn7XiWSvzyf/b45PZ1tsIfcexLOD0cD8Eq85ZLzp9WUrJfqDmm7euVdQtVjkSzlcbWCUkkSy0CJ7LsQXU07noGepTtJZEr1WVAgnC/F0P/90Pj/rHOOUXed3QR7tOCUHGDr6PHV2faOzpTy4cWmesfsaGAnx9QlPgu6Tj58GlG5JKtQ=='
            ];
            $body    = '{"code":"000000","msg":"操作成功","resp_time":"20240829113811","resp_data":{"merchant_no":"82229007392000A","channel_id":"95","out_order_no":"ORD20240829113808","order_create_time":"20240829113810","order_efficient_time":"20240905113808","pay_order_no":"24082911012001101011001335026","total_amount":"1","counter_url":"https://pay.wsmsd.cn/r/0000?pageStyle%3DV2%26token%3DCCSSIZ5wkKuamBs1G3Y1U40gGcn4pxoKuxVhhVI7XyulHEUboR1J21HIoJVNGGBpwQIXNpZLJz6CHCkaCw%3D%3D%26amount%3D1%26payOrderNo%3D24082911012001101011001335026%26mndf%3D1"}}';
            $result  = $aggregationCashierDeskService->signatureVerification($headers, $body);
            dump($result);
            break;
        case 'orderQuery': // 收银台订单查询测试
            $orderNo = 'ORD20240829153905'; 
            $result  = $aggregationCashierDeskService->counterOrderQuery($orderNo);
            dump($result);
            break;
        case 'orderClose': // 收银台订单关单测试（根据测试支付成功和已关单的订单无法关单）
            $orderNo = 'ORD20240829123955'; 
            $result  = $aggregationCashierDeskService->counterOrderClose($orderNo);
            dump($result);
            break;
        case 'tradeRefund': // 扫码银行卡退货
            $result = $merchantService->tradeRefund('OR' . date('YmdHis'), 1, 'ORD20240829153905', '66210316550275', ['request_ip' => '172.22.66.186']);
            dump($result);
            break;
        case 'tradeRefundQuery': // 扫码银行卡退货 查询
            $result = $merchantService->tradeRefundQuery('OR20240830155325');
            dump($result);
            break;
        case 'tradeUniformRefund': // 统一退货（未测试通过，余额不足）
            $outOrderNo   = 'ORDR' . date('YmdHis');
            $refundAmount = 1;
            $bizType      = 3;
            $tradeDate    = date('Ymd');
            $logNo        = '66210316550275';
            $result       = $merchantService->tradeUniformRefund($outOrderNo, $refundAmount, $bizType, $tradeDate, $logNo);
            dump($result);
            break;
        case 'tradeUniformRefundQuery': // 统一退货查询（未测试）
            $outOrderNo       = 'ORDR' . date('YmdHis');
            $bizType          = 3;
            $refundTradeDate  = date('Ymd');
            $refundOutOrderNo = '';
            $result           = $merchantService->tradeUniformRefundQuery($outOrderNo, $bizType, $refundTradeDate, $refundOutOrderNo);
            dump($result);
            break;
    }
}
```
