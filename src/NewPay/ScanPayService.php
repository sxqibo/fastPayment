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
        $request['signMsg'] = RsaUtil::buildSignForBin2Hex($signData, $scanPayModel->privateKey);

        // post请求接口
        $content = HttpUtil::post($request, ScanPayModel::REQUEST_URL);

        $content = json_decode($content, true);
        // 对返回值的验签
        $bool = RsaUtil::verifySignForHex2Bin($content['signMsg'],
            $scanPayModel->publicKey,
            Util::getStringData(ScanPayModel::VERIFY_FIELD, $content));

        if ($bool) {
            return $content;
        }

        return '返回值验签失败';
    }
}
