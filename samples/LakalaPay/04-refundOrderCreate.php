<?php
require_once '../../vendor/autoload.php';

use GuzzleHttp\Exception\GuzzleException;
use Sxqibo\FastPayment\LakalaPay\services\Merchant;

/**
 * 商户服务 - 其他 - 扫码银行卡退货 - 退货
 * @param $config
 * @return array
 * @throws GuzzleException
 */
function refundOrderCreate($config): array
{
    $lakalaMerchantService = new Merchant($config['basic']);

    $params = $config['merchant_refund_order_create'];
    return $lakalaMerchantService->tradeRefund($params['out_order_no'], $params['refund_amount'], $params['origin_out_order_no'], $params['origin_log_no'], $params['location_info']);
}

$config = include 'config.php';

$result = null;
try {
    $result = refundOrderCreate($config);
} catch (Throwable $e) {
    echo $e->getMessage();
}
print_r($result);

/*

Array
(
    [trade_state] => SUCCESS
    [refund_type] => ALL
    [merchant_no] => 82229007392000A
    [out_trade_no] => TR20240919120719
    [trade_no] => 20240920110110001231120010300062
    [log_no] => 31120010300062
    [acc_trade_no] => 2024092022001488291447584760
    [account_type] => ALI
    [total_amount] => 1
    [refund_amount] => 1
    [payer_amount] => 1
    [trade_time] => 20240920120724
    [origin_trade_no] => 2024092066210516530524
    [origin_out_trade_no] => D929638120240920120438106ODBCYDE
    [origin_log_no] => 66210516530524
    [channel_ret_desc] => RFD00000#成功
)

*/
