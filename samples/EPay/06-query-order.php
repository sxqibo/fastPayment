<?php
require_once '../../vendor/autoload.php';

use Sxqibo\FastPayment\EPay\EpayServices;

$config = include 'config.php';

$epay      = new EpayServices($config['basic']);

$result = $epay->queryOrder($config['query_info']['order_no']);  // 发起支付（页面跳转）
print_r($result);