<?php

$publicKey = ''; // 公钥
$params = array();


foreach ($_POST as $key => $val) {//动态遍历获取所有收到的参数,此步非常关键,因为收银宝以后可能会加字段,动态获取可以兼容由于收银宝加字段而引起的签名异常
    $params[$key] = $val;
}

if (count($params) < 1) {//如果参数为空,则不进行处理
    echo "error";
    exit();
}

if (\Sxqibo\FastPayment\Common\Utility::validUnionPaySign($params, $publicKey)) {//验签成功
    //此处进行业务逻辑处理
    echo "success";
} else {
    echo "error";
}

?>
