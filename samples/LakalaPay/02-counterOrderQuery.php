<?php
require_once '../../vendor/autoload.php';

use GuzzleHttp\Exception\GuzzleException;
use Sxqibo\FastPayment\LakalaPay\services\AggregationCashierDesk;

/**
 * 聚合收银台 - 订单查询
 * @param $config
 * @return array
 * @throws GuzzleException
 */
function counterOrderQuery($config): array
{
    $lakalaCounterService = new AggregationCashierDesk($config['basic']);

    $params = $config['counter_order_info_query'];
    return $lakalaCounterService->counterOrderQuery($params['out_order_no']);
}

$config = include 'config.php';

$result = null;
try {
    $result = counterOrderQuery($config);
} catch (Throwable $e) {
    echo $e->getMessage();
}
print_r($result);


/*

Array
(
    [pay_order_no] => 24092011012001101011001340580
    [out_order_no] => TS20240913120213
    [channel_id] => 95
    [trans_merchant_no] => 82229007392000A
    [trans_term_no] => D9296381
    [merchant_no] => 82229007392000A
    [term_no] => D9296381
    [order_status] => 2
    [order_info] => 测试商品00120240913120213
    [total_amount] => 1
    [order_create_time] => 20240920120215
    [order_efficient_time] => 20240927120213
    [settle_type] => 0
    [split_mark] =>
    [counter_param] =>
    [counter_remark] =>
    [busi_type_param] =>
    [sgn_info] => Array
        (
        )

    [goods_mark] =>
    [goods_field] =>
    [shop_name] =>
    [order_trade_info_list] => Array
        (
            [0] => Array
                (
                    [trade_no] => 2024092066210516530524
                    [acc_settle_amount] => 1
                    [acc_mdiscount_amount] =>
                    [acc_discount_amount] =>
                    [acc_other_discount_amount] =>
                    [log_No] => 66210516530524
                    [trade_ref_no] =>
                    [trade_type] => PAY
                    [trade_status] => S
                    [trade_amount] => 1
                    [payer_amount] => 1
                    [user_id1] => war***@126.com
                    [user_id2] => 2088602141888291
                    [busi_type] => SCPAY
                    [trade_time] => 20240920120447
                    [acc_trade_no] => 2024092022001488291447584760
                    [payer_account_no] =>
                    [payer_name] =>
                    [payer_account_bank] =>
                    [acc_type] => 00
                    [pay_mode] => ALIPAY
                    [client_batch_no] =>
                    [client_seq_no] =>
                    [settle_merchant_no] => 82229007392000A
                    [settle_term_no] => D9296381
                    [origin_trade_no] =>
                    [auth_code] =>
                    [bank_type] =>
                    [result_desc] => 成功
                )

            [1] => Array
                (
                    [trade_no] => 2024092066210516530522
                    [acc_settle_amount] => 0
                    [acc_mdiscount_amount] =>
                    [acc_discount_amount] =>
                    [acc_other_discount_amount] =>
                    [log_No] => 66210516530522
                    [trade_ref_no] =>
                    [trade_type] => PAY
                    [trade_status] => F
                    [trade_amount] => 1
                    [payer_amount] => 0
                    [user_id1] => oOOL0wEQGKmtj0WZRAOf3Avhssfw
                    [user_id2] =>
                    [busi_type] => SCPAY
                    [trade_time] => 20240920120414
                    [acc_trade_no] =>
                    [payer_account_no] =>
                    [payer_name] =>
                    [payer_account_bank] =>
                    [acc_type] =>
                    [pay_mode] => WECHAT
                    [client_batch_no] =>
                    [client_seq_no] =>
                    [settle_merchant_no] => 82229007392000A
                    [settle_term_no] => D9296381
                    [origin_trade_no] =>
                    [auth_code] =>
                    [bank_type] =>
                    [result_desc] => sub_mch_id与sub_appid不匹配
                )

        )

)

*/
