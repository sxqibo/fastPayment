<?php

namespace Sxqibo\FastPayment\NewPay;

/**
 * 查询接口
 * 类说明：对应新生支付文档 5.5
 * 文档地址：https://www.yuque.com/chenyanfei-sjuaz/uhng8q/gbxazy
 */
final class QueryService
{
    public function query(QueryModel $queryModel)
    {
        $request = $queryModel->getModelData();

        // post请求接口
        $content = HttpUtil::post($request, QueryModel::REQUEST_URL);

        $content = json_decode($content, true);

        // 对返回值的验签
        // $bool = $queryModel->verifySign($content);

        // if ($bool) {
            return $content;
        // }

        // return '返回值验签失败';
    }
}
