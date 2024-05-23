<?php

namespace Sxqibo\FastPayment\Common;

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
    public static function post($requestParam, $requestUrl): string
    {
        $client = new Client();

        $result = $client->request('POST', $requestUrl, [
            'form_params' => $requestParam
        ]);

        return $result->getBody()->getContents();
    }
}
