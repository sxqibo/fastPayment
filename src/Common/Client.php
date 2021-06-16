<?php

namespace Sxqibo\FastPayment\Common;

use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Handler\CurlHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use Exception;

class Client
{
    private       $timeout = 30;
    public static $clientInstance;
    protected     $wechatpayMiddleware;
    protected     $options = [
        'wechatMiddleware' => ''
    ];

    public function __construct($options = [])
    {
        if (!empty($options)) {
            if (!empty($options['wechatMiddleware'])) {
                $this->options['wechatMiddleware'] = $options['wechatMiddleware'];
            }
        }

    }

    /**
     * 请求API接口
     *
     * @param $endPoint
     * @param array $query
     * @param array $body
     * @param array $headers
     * @param false $raw
     * @return array|mixed
     * @throws Exception
     */
    public function requestApi($endPoint, $query = [], $body = null, $headers = [], $raw = false)
    {
        try {
            $options = [
                'query' => $query,
            ];

            if (!empty($headers)) {
                $options['headers'] = $headers;
            }

            $options['timeout'] = $this->timeout;

            if (!empty($body)) {
                $contentType = $headers['Content-Type'] ?? '';
                if ($contentType == 'application/x-www-form-urlencoded') {
                    $options['form_params'] = $body;
                } else {
                    $options['body'] = $body;
                }
            }

            $client   = $this->getClient();
            $response = $client->request($endPoint['method'], $endPoint['url'], $options);

            $body = $response->getBody();
            if ($raw) {
                $content = $body->getContents();

                $json = json_decode($content, true);
                if ($json && $content != $json) {
                    return $json;
                }
                return $content;
            } else {
                if (strpos(strtolower($response->getHeader('Content-Type')[0]), 'xml') !== false) {
                    return $this->xmlToArray($body);
                } else {
                    return $body;
                }
            }
        } catch (GuzzleException $e) {
            $paramString = json_encode($options, JSON_UNESCAPED_UNICODE);
            $errorMsg    = "请求API失败，API:{$endPoint['url']}，参数:{$paramString}，错误信息:[{$e->getMessage()}]";

            throw new Exception($errorMsg);
        }
    }

    /**
     * Convert an xml string to an array
     * @param string $xmlstring
     * @return array
     */
    protected function xmlToArray($xmlstring)
    {
        return json_decode(json_encode(simplexml_load_string($xmlstring)), true);
    }

    /**
     * @return mixed
     */
    protected function getClient()
    {
        if (!isset(static::$clientInstance)) {
            $handlerStack = HandlerStack::create(new CurlHandler());
            $handlerStack->push(Middleware::retry($this->retryDecider(), $this->retryDelay()));

            // 判断微信中间件是否存在
            if (!empty($this->options['wechatMiddleware'])) {
                $handlerStack->push($this->options['wechatMiddleware'], 'wechatpay');
            }

            static::$clientInstance = new \GuzzleHttp\Client(['handler' => $handlerStack]);
        }

        return static::$clientInstance;
    }

    /**
     * 返回一个匿名函数，该匿名函数返回下次重试的时间（毫秒）
     * @return mixed
     */
    private function retryDelay()
    {
        return function ($numberOfRetries) {
            return 1000 * $numberOfRetries;
        };
    }

    /**
     * retryDecider
     * 返回一个匿名函数, 匿名函数若返回false 表示不重试，反之则表示继续重试
     * @return mixed
     */
    private function retryDecider()
    {
        return function (
            $retries,
            \GuzzleHttp\Psr7\Request $request,
            \GuzzleHttp\Psr7\Response $response = null,
            \GuzzleHttp\Exception\RequestException $exception = null
        ) {
            // Limit the number of retries to 3
            if ($retries >= 3) {
                return false;
            }

            // Retry connection exceptions
            if ($exception instanceof ConnectException) {
                return true;
            }

            if ($response) {
                // Retry on server errors
                if ($response->getStatusCode() >= 500) {
                    return true;
                }
            }

            return false;
        };
    }
}
