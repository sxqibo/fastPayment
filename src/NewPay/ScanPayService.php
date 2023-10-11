<?php

namespace Sxqibo\FastPayment\NewPay;

/**
 * 微信&支付宝扫码（C扫B）
 * 文档地址：https://www.yuque.com/chenyanfei-sjuaz/uhng8q/uoce7b#wZTrE
 */
final class ScanPayService
{
    /**
     * 扫码支付
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function scanPay(ScanPayModel $scanPayModel)
    {
        $verify = $scanPayModel->verify();

        if (!empty($verify)) {
            return $verify;
        }

        // 对指定字段进行生成签名
        $request = $scanPayModel->getData();
        $signData = $scanPayModel->getSignData();
        $request['signMsg'] = $this->buildSign($signData, $scanPayModel->privateKey);

        // post请求接口
        $content = HttpUtil::post($request, ScanPayModel::REQUEST_URL);

        // 对返回值的验签
        $bool = $this->verifySign(json_decode($content, true), $scanPayModel->publicKey);
        if ($bool) {
            return $content;
        }

        return '返回值验签失败';
    }

    /**
     * 生成签名
     *  发送数据时的签名
     *
     * @param $request
     * @param $privateKey
     * @return string
     * @throws \Exception
     */
    public function buildSign($request, $privateKey): string
    {
        $res = openssl_get_privatekey($privateKey);
        openssl_sign($request, $signature, $res, OPENSSL_ALGO_SHA1);

        return bin2hex($signature);
    }

    /**
     * 接受返回数据的验签
     *
     * @param $data
     * @param $publicKey
     * @return bool
     * @throws \Exception
     */
    public function verifySign($data, $publicKey): bool
    {
        // 生成验签字符串
        $signParam = Util::getStringData(ScanPayModel::VERIFY_FIELD, $data);

        // 验签
        $res = openssl_get_publickey($publicKey);
        return (bool)openssl_verify($signParam, hex2bin($data['signMsg']), $res);
    }
}
