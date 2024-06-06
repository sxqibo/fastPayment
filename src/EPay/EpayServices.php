<?php
/* *
 * 彩虹易支付SDK服务类
 * 说明：
 * 包含发起支付、查询订单、回调验证等功能
 */

namespace Sxqibo\FastPayment\EPay;

class EpayServices
{
    private mixed $pid;
    private mixed $key;
    private string $submit_url;
    private string $mapi_url;
    private string $api_url;
    private string $sign_type = 'MD5';

    function __construct($config)
    {
        $this->pid        = $config['pid'];
        $this->key        = $config['key'];
        $this->submit_url = $config['apiurl'] . 'submit.php';
        $this->mapi_url   = $config['apiurl'] . 'mapi.php';
        $this->api_url    = $config['apiurl'] . 'api.php';
    }

    /**
     * 发起支付（页面跳转）
     * @param $param_tmp
     * @param string $button
     * @return string
     */
    public function pagePay($param_tmp, string $button = '正在跳转'): string
    {
        $param = $this->buildRequestParam($param_tmp);

        $html = '<form id="dopay" action="' . $this->submit_url . '" method="post">';
        foreach ($param as $k => $v) {
            $html .= '<input type="hidden" name="' . $k . '" value="' . $v . '"/>';
        }
        $html .= '<input type="submit" value="' . $button . '"></form><script>document.getElementById("dopay").submit();</script>';

        return $html;
    }

    /**
     * 发起支付（获取链接）
     * @param $param_tmp
     * @return string
     */
    public function getPayLink($param_tmp): string
    {
        $param = $this->buildRequestParam($param_tmp);
        return $this->submit_url . '?' . http_build_query($param);
    }

    /**
     * 发起支付（API接口）
     * @param $param_tmp
     * @return mixed
     */
    public function apiPay($param_tmp): mixed
    {
        $param    = $this->buildRequestParam($param_tmp);
        $response = $this->getHttpResponse($this->mapi_url, http_build_query($param));
        $arr      = json_decode($response, true);
        return $arr;
    }

    /**
     * 异步回调验证
     * @return bool
     */
    public function verifyNotify(): bool
    {
        if (empty($_GET)) return false;

        $sign = $this->getSign($_GET);

        if ($sign === $_GET['sign']) {
            $signResult = true;
        } else {
            $signResult = false;
        }

        return $signResult;
    }

    /**
     * 同步回调验证
     * @return bool
     */
    public function verifyReturn(): bool
    {
        if (empty($_GET)) return false;

        $sign = $this->getSign($_GET);

        if ($sign === $_GET['sign']) {
            $signResult = true;
        } else {
            $signResult = false;
        }

        return $signResult;
    }

    /**
     * 查询订单支付状态
     * @param $trade_no
     * @return bool
     */
    public function orderStatus($trade_no)
    {
        $result = $this->queryOrder($trade_no);
        if ($result['status'] == 1) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 查询订单
     * @param $trade_no
     * @return mixed
     */
    public function queryOrder($trade_no)
    {
        $url      = $this->api_url . '?act=order&pid=' . $this->pid . '&key=' . $this->key . '&trade_no=' . $trade_no;
        $response = $this->getHttpResponse($url);
        return json_decode($response, true);
    }

    /**
     * 订单退款
     * @param $trade_no
     * @param $money
     * @return mixed
     */
    public function refund($trade_no, $money)
    {
        $url      = $this->api_url . '?act=refund';
        $post     = 'pid=' . $this->pid . '&key=' . $this->key . '&trade_no=' . $trade_no . '&money=' . $money;
        $response = $this->getHttpResponse($url, $post);
        return json_decode($response, true);
    }

    /**
     * 建议请求
     * @param $param
     * @return mixed
     */
    private function buildRequestParam($param)
    {
        $param['sign']      = $this->getSign($param);
        $param['sign_type'] = $this->sign_type;
        return $param;
    }

    /**
     * 计算签名
     * @param $param
     * @return string
     */
    private function getSign($param): string
    {
        ksort($param);
        reset($param);
        $signStr = '';

        foreach ($param as $k => $v) {
            if ($k != "sign" && $k != "sign_type" && $v != '') {
                $signStr .= $k . '=' . $v . '&';
            }
        }
        $signStr = substr($signStr, 0, -1);
        $signStr .= $this->key;
        return md5($signStr);
    }

    /**
     * 请求外部资源
     * @param $url
     * @param false $post
     * @return bool|string
     */
    private function getHttpResponse($url, false $post = false): bool|string
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        $httpheader[] = "Accept: */*";
        $httpheader[] = "Accept-Language: zh-CN,zh;q=0.8";
        $httpheader[] = "Connection: close";
        curl_setopt($ch, CURLOPT_HTTPHEADER, $httpheader);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        if ($post) {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
        }
        $response = curl_exec($ch);
        curl_close($ch);
        return $response;
    }
}
