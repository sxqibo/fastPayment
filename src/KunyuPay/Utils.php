<?php
namespace Sxqibo\FastPayment\KunyuPay;

class Utils
{
    /**
     * 打印输出函数
     * @param $var
     * @return void
     */
    public static function fdump($var, $format = false): void
    {
        if ($format) echo '<pre>';
        print_r($var);
        if ($format) echo '</pre>';
        echo PHP_EOL;
    }

    public static function fecho(string | int $var, string $type = 'Success'):void {
        echo "[{$type}]" . $var . PHP_EOL;
    }
}
