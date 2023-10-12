<?php

namespace Sxqibo\FastPayment\NewPay;

use Exception;

/**
 * 格式化验签字符串工具类
 *
 * 并不是所有的验签都使用该格式，注意文档说明吧
 */
final class Util
{
    /**
     * 格式化验签字符串
     *
     * @param array $signFields 需要验签的字段
     * @param array $param 带验签的数据
     * @return string
     * @throws Exception
     */
    public static function getStringData(array $signFields, $param): string
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
