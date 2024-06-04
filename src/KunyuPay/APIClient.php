<?php
namespace Sxqibo\FastPayment\KunyuPay;

class APIClient
{
    private Signature $signatureHandle;

    public function __construct(string $privateKey, string $publicKey)
    {
        // 初始化签名类
        $this->signatureHandle = new Signature();
        $this->signatureHandle->setPrivateKey($privateKey);
        $this->signatureHandle->setPublicKey($publicKey);
    }

    /**
     * @param string $url
     * @param array  $params
     * @return mixed
     * @throws \Exception
     */
    public function callApi(string $url, array $params): mixed
    {
        // 生成签名
        $signature = $this->signatureHandle->generateSignature($params);

        // 将签名添加到请求参数尾部
        $params['signature'] = $signature;
        // exit(var_export($params));

        // 发送api请求
        $response = $this->sendRequest($url, $params);
        if ($response['resultCode'] <> '0000') {
            Utils::fdump($response);
            // throw new \Exception($response['message']);
            return false;
        }

        // 验证签名
        $signResponseData = array_filter($response, function ($item) {
            // 返回的数据排除resultCode，message，signature这3个字段都参与签名
            return !in_array($item, ['resultCode', 'message', 'signature']);
        }, ARRAY_FILTER_USE_KEY);
        $isValid          = $this->signatureHandle->verifySignature($signResponseData, $response['signature']);

        // 如果验证通过，则返回api响应数据
        if ($isValid) {
            return $response;
        } else {
            throw new \Exception('签名错误');
        }
    }

    /**
     * curl发送post请求
     * @param string $url
     * @param array  $params
     * @return mixed
     */
    private function sendRequest(string $url, array $params): mixed
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);

        if ($response === false) {
            echo 'CURLRequest Error: ' . curl_error($ch);
            exit();
        }

        curl_close($ch);

        return json_decode($response, true);
    }
}
