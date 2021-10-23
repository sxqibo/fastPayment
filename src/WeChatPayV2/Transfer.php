<?php

namespace Sxqibo\FastPayment\WeChatPayV2;

/**
 * 微信商户打款
 * Class Transfer
 * @package Sxqibo\FastPayment\WeChatPay
 */
class Transfer extends BaseService
{
    /**
     * 企业付款到零钱
     * @param array $options
     * @return array
     */
    public function create(array $data)
    {
        $url = $this->base . '/mmpaymkttransfers/promotion/transfers';

        $this->params->offsetUnset('appid');
        $this->params->offsetUnset('mch_id');
        $this->params->set('mchid', $this->config->get('mch_id'));
        $this->params->set('mch_appid', $this->config->get('appid'));

        $result = $this->callPostApi($url, $data, true, 'MD5');

        return $this->handleResult($result);
    }

    /**
     * 企业付款到银行卡
     * @param array $data
     * @return array
     */
    public function createBank(array $data)
    {
        $url = $this->base . '/mmpaysptrans/pay_bank';

        $this->params->offsetUnset('mch_id');
        $this->params->set('mch_id', $this->config->get('mch_id'));

        $result = $this->callPostApi($url, $data, true, 'MD5');

        return $this->handleResult($result);
    }

    /**
     * 查询企业付款到零钱
     * @param string $partnerTradeNo 商户调用企业付款API时使用的商户订单号
     * @return array
     */
    public function query($partnerTradeNo)
    {
        $this->params->offsetUnset('mchid');
        $this->params->offsetUnset('mch_appid');
        $this->params->set('appid', $this->config->get('appid'));
        $this->params->set('mch_id', $this->config->get('mch_id'));

        $url = $this->base . '/mmpaymkttransfers/gettransferinfo';

        $result = $this->callPostApi($url, ['partner_trade_no' => $partnerTradeNo], true, 'MD5');

        return $this->handleResult($result);
    }

    /**
     * 查询企业付款到银行卡
     * @param string $partnerTradeNo 商户调用企业付款API时使用的商户订单号
     * @return array
     */
    public function queryBank($partnerTradeNo)
    {
        $this->params->offsetUnset('mch_id');
        $this->params->set('mch_id', $this->config->get('mch_id'));

        $url = $this->base . '/mmpaysptrans/query_bank';

        $result = $this->callPostApi($url, ['partner_trade_no' => $partnerTradeNo], true, 'MD5');

        return $this->handleResult($result);
    }

}
