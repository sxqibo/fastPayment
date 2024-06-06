<?php

/* *
 * 彩虹易支付SDK服务类
 * 说明：
 * 包含发起支付、查询订单、回调验证等功能
 * @doc https://wx.56xr.cn/doc.html
 */

namespace Sxqibo\FastPayment\EPay;

class EpayServices
{
    private string|int $pid;
    private string $key;
    private string $submit_url;
    private string $mapi_url;
    private string $api_url;
    private string $sign_type = 'MD5';

    public function __construct(array $config)
    {
        $this->pid        = $config['pid'];
        $this->key        = $config['key'];
        $this->submit_url = $config['apiurl'] . 'submit.php';
        $this->mapi_url   = $config['apiurl'] . 'mapi.php';
        $this->api_url    = $config['apiurl'] . 'api.php';
    }

    /**
     * 1. 发起支付（页面跳转）
     * @param array $params
     * @param string $button
     * @return string
     */
    public function submitPage(array $params, string $button = '正在跳转'): string
    {
        $param = $this->buildRequestParam($params);

        $html = '<form id="dopay" action="' . $this->submit_url . '" method="post">';
        foreach ($param as $k => $v) {
            $html .= '<input type="hidden" name="' . htmlspecialchars($k) . '" value="' . htmlspecialchars($v) . '"/>';
        }
        $html .= '<input type="submit" value="' . htmlspecialchars($button) . '"></form><script>document.getElementById("dopay").submit();</script>';

        return $html;
    }

    /**
     * 2. 发起支付（获取链接）
     * @param array $params
     * @return string
     */
    public function submitLink(array $params): string
    {
        $param = $this->buildRequestParam($params);
        return $this->submit_url . '?' . http_build_query($param);
    }

    /**
     * 3. mapi发起支付（API接口）
     * @param array $params
     * @return mixed
     * @throws \Exception
     */
    public function mapi(array $params): mixed
    {
        $param    = $this->buildRequestParam($params);
        $response = $this->getHttpResponse($this->mapi_url, http_build_query($param));
        return json_decode($response, true);
    }

    /**
     * 4. 异步回调验证
     * @return bool
     */
    public function verifyNotify(): bool
    {
        if (empty($_GET)) return false;

        $sign = $this->getSign($_GET);

        return $sign === $_GET['sign'];
    }

    /**
     * 5. 同步回调验证
     * @return bool
     */
    public function verifyReturn(): bool
    {
        if (empty($_GET)) return false;

        $sign = $this->getSign($_GET);

        return $sign === $_GET['sign'];
    }

    /**
     * 6. 查询订单支付状态
     * @param string $trade_no
     * @return bool
     * @throws \Exception
     */
    public function orderStatus(string $trade_no): bool
    {
        $result = $this->queryOrder($trade_no);
        return $result['status'] == 1;
    }

    /**
     * 7. 查询订单
     * @param string $tradeNo
     * @return mixed
     * @throws \Exception
     */
    public function queryOrder(string $tradeNo): mixed
    {
        $url      = $this->api_url . '?act=order&pid=' . $this->pid . '&key=' . $this->key . '&trade_no=' . $tradeNo;
        $response = $this->getHttpResponse($url);
        return json_decode($response, true);
    }

    /**
     * 8. 订单退款
     * @param string $trade_no
     * @param float $money
     * @return mixed
     * @throws \Exception
     */
    public function refund(string $trade_no, float $money): mixed
    {
        $url      = $this->api_url . '?act=refund';
        $post     = http_build_query([
            'pid'      => $this->pid,
            'key'      => $this->key,
            'trade_no' => $trade_no,
            'money'    => $money
        ]);
        $response = $this->getHttpResponse($url, $post);
        return json_decode($response, true);
    }

    /**
     * 构建请求参数
     * @param array $param
     * @return array
     */
    private function buildRequestParam(array $param): array
    {
        $param['sign']      = $this->getSign($param);
        $param['sign_type'] = $this->sign_type;
        return $param;
    }

    /**
     * 计算签名
     * @param array $param
     * @return string
     */
    private function getSign(array $param): string
    {
        ksort($param);
        $signStr = '';

        foreach ($param as $k => $v) {
            if ($k !== "sign" && $k !== "sign_type" && $v !== '') {
                $signStr .= $k . '=' . $v . '&';
            }
        }
        $signStr = substr($signStr, 0, -1);
        $signStr .= $this->key;
        return md5($signStr);
    }

    /**
     * 请求外部资源
     * @param string $url
     * @param string|false $post
     * @return bool|string
     * @throws \Exception
     */
    private function getHttpResponse(string $url, string|false $post = false): bool|string
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        $httpHeader = [
            "Accept: */*",
            "Accept-Language: zh-CN,zh;q=0.8",
            "Connection: close"
        ];
        curl_setopt($ch, CURLOPT_HTTPHEADER, $httpHeader);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        if ($post) {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
        }
        $response = curl_exec($ch);
        if ($response === false) {
            throw new \Exception('Curl error: ' . curl_error($ch));
        }
        curl_close($ch);
        return $response;
    }
}
