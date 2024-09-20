<?php
require_once '../../vendor/autoload.php';

use GuzzleHttp\Exception\GuzzleException;
use Sxqibo\FastPayment\LakalaPay\services\AggregationCashierDesk;

/**
 * 聚合收银台 - 订单关单
 * @param $config
 * @return array
 * @throws GuzzleException
 */
function counterOrderClose($config): array
{
    $lakalaCounterService = new AggregationCashierDesk($config['basic']);

    $params = $config['counter_order_info_close'];
    return $lakalaCounterService->counterOrderClose($params['out_order_no']);
}

$config = include 'config.php';

$result = null;
try {
    $result = counterOrderClose($config);
} catch (Throwable $e) {
    echo $e->getMessage();
}
print_r($result);
