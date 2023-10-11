<?php

namespace Sxqibo\FastPayment\NewPay;

use Exception;

final class Util
{
    public static function getStringData($signFields, $param): string
    {
        $fieldString = '';

        foreach($signFields as $field) {
            if (!isset($param[$field])) {
                throw new Exception('参数无效！' . $field);
            }

            $fieldString .= $field . '=[' . $param[$field] . ']';
        }

        return $fieldString;
    }
}
