<?php

namespace Sxqibo\FastPayment\NewPay;

use GuzzleHttp\Client;

/**
 * Http请求工具类
 */
final class HttpUtil
{
    /**
     * post请求
     *
     * @param $requestParam
     * @param $reuqestUrl
     * @return string
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public static function post($requestParam, $reuqestUrl)
    {
        $client = new Client();

        $result = $client->request('POST', $reuqestUrl, [
            'form_params' => $requestParam
        ]);

        return $result->getBody()->getContents();
    }
}
