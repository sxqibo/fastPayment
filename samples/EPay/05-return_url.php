<?php
require_once '../../vendor/autoload.php';

use Sxqibo\FastPayment\EPay\EpayServices;

$config = include 'config.php';

//计算得出通知验证结果
$epay          = new EpayServices($config['basic']);
$verify_result = $epay->verifyReturn();

if ($verify_result) { // 验证成功

    //商户订单号
    $out_trade_no = $_GET['out_trade_no'];

    //支付宝交易号
    $trade_no = $_GET['trade_no'];

    //交易状态
    $trade_status = $_GET['trade_status'];

    //支付方式
    $type = $_GET['type'];


    if ($_GET['trade_status'] == 'TRADE_SUCCESS') {
        //判断该笔订单是否在商户网站中已经做过处理
        //如果没有做过处理，根据订单号（out_trade_no）在商户网站的订单系统中查到该笔订单的详细，并执行商户的业务程序
        //如果有做过处理，不执行商户的业务程序
    } else {
        echo "trade_status=" . $_GET['trade_status'];
    }

    echo "<h3>验证成功</h3><br />";
} else {
    //验证失败
    echo "<h3>验证失败</h3>";
}
