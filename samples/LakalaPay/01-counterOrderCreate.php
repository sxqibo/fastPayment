<?php
require_once '../../vendor/autoload.php';

use GuzzleHttp\Exception\GuzzleException;
use Sxqibo\FastPayment\LakalaPay\services\AggregationCashierDesk;

/**
 * 聚合收银台 - 订单创建
 * @param $config
 * @return array
 * @throws GuzzleException
 */
function counterOrderCreate($config): array
{
    $lakalaCounterService = new AggregationCashierDesk($config['basic']);

    $params = $config['counter_order_info_create'];
    return $lakalaCounterService->counterOrderSpecialCreate($params['out_order_no'], $params['order_amount'], $params['order_title'], $params['extra_params']);
}

$config = include 'config.php';

$result = null;
try {
    $result = counterOrderCreate($config);
} catch (Throwable $e) {
    echo $e->getMessage();
}
print_r($result);

/*

Array
(
    [merchant_no] => 82229007392000A
    [channel_id] => 95
    [out_order_no] => TS20240913120213
    [order_create_time] => 20240920120215
    [order_efficient_time] => 20240927120213
    [pay_order_no] => 24092011012001101011001340580
    [total_amount] => 1
    [counter_url] => https://pay.wsmsd.cn/r/0000?pageStyle%3DV2%26token%3DCCSSIZ5wkKmYmBg2EXc-U40gGcj4rhoKuxVhhVI7XyulHEUboR1J21bNpZ9LN0KdLGu9gNw6ItIHF0NXfQ%3D%3D%26amount%3D1%26payOrderNo%3D24092011012001101011001340580%26mndf%3D1
)

 */
