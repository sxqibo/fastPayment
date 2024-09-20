<?php
require_once '../../vendor/autoload.php';

use GuzzleHttp\Exception\GuzzleException;
use Sxqibo\FastPayment\LakalaPay\services\Merchant;

/**
 * 商户服务 - 其他 - 扫码银行卡退货 - 退货查询
 * @param $config
 * @return array
 * @throws GuzzleException
 */
function refundOrderQuery($config): array
{
    $lakalaMerchantService = new Merchant($config['basic']);

    $params = $config['merchant_refund_order_query'];
    return $lakalaMerchantService->tradeRefundQuery($params['out_order_no']);
}

$config = include 'config.php';

$result = null;
try {
    $result = refundOrderQuery($config);
} catch (Throwable $e) {
    echo $e->getMessage();
}
print_r($result);

/*
Array
(
    [out_trade_no] => TR20240919120719
    [trade_no] => 20240920110110001231120010300062
    [log_no] => 31120010300062
    [acc_trade_no] => 2024092022001488291447584760
    [trade_time] => 20240920120724
    [trade_state] => SUCCESS
    [refund_amount] => 1
    [pay_mode] =>
    [crd_no] =>
    [account_type] => ALIPAY
    [payer_amount] => 1
    [acc_settle_amount] =>
    [acc_mdiscount_amount] =>
    [acc_discount_amount] =>
    [channel_ret_desc] => RFD00000#成功
    [origin_trade_no] => 2024092066210516530524
    [origin_out_trade_no] => D929638120240920120438106ODBCYDE
    [origin_log_no] => 66210516530524
    [origin_total_amount] => 1
    [refund_split_info] =>
)
*/
