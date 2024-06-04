<?php

namespace Sxqibo\FastPayment\KunyuPay;

use Exception;
use Sxqibo\FastPayment\KunyuPay\APIClient;
use Sxqibo\FastPayment\KunyuPay\Utils;


/**
 * 坤域“新生”支付包
 */
class KunyuService
{
    private string $baseUrl = 'https://api.kunyukj.cn/api/';
    private string $privateKey = '';
    private string $publicKey = '';
    private string $merId;

    /**
     * @param $paramMerId
     * @param $privateKey
     * @param $publicKey
     */
    public function __construct($paramMerId, $privateKey, $publicKey)
    {
        $this->merId      = $paramMerId;
        $this->privateKey = $privateKey;
        $this->publicKey  = $publicKey;
    }

    /**
     * 1. 新生 扫码支付订单
     * @param $params
     * @return mixed|void
     */
    public function scanPayApply($params)
    {
        // 扫码支付测试
        $apiUrl = $this->baseUrl . '/PaymentCenter/scanPayApply';

        $params['mer_id'] = $this->merId;

        try {
            $apiClient = new APIClient($this->privateKey, $this->publicKey);
            return $apiClient->callApi($apiUrl, $params);

        } catch (Exception $e) {
             Utils::fecho($e->getMessage(), 'Error');
        }
    }

    /**
     * 2. 新生 单笔扫码支付订单查询
     * @param $params
     * @return mixed|void
     */
    public function scanPayQuery($params)
    {
        $apiUrl = $this->baseUrl . '/PaymentCenter/scanPayQuery';

        $params['mer_id'] = $this->merId;

        try {
            $apiClient = new APIClient($this->privateKey, $this->publicKey);
            return $apiClient->callApi($apiUrl, $params);

        } catch (Exception $e) {
            Utils::fecho($e->getMessage(), 'Error');
        }
    }

    /**
     * 3. 新生 退款申请订单
     * @param $params
     * @return mixed|void
     */
    public function refundApply($params)
    {
        $apiUrl = $this->baseUrl . '/PaymentCenter/refundApply';

        $params['mer_id'] = $this->merId;

        try {
            $apiClient = new APIClient($this->privateKey, $this->publicKey);
            return $apiClient->callApi($apiUrl, $params);

        } catch (Exception $e) {
            Utils::fecho($e->getMessage(), 'Error');
        }
    }

    /**
     * 4. 新生 退款订单查询
     * @return mixed|void
     */
    public function refundQuery($params)
    {
        $apiUrl = $this->baseUrl . '/PaymentCenter/refundQuery';

        $params['mer_id'] = $this->merId;

        try {
            $apiClient = new APIClient($this->privateKey, $this->publicKey);
            return $apiClient->callApi($apiUrl, $params);

        } catch (Exception $e) {
            Utils::fecho($e->getMessage(), 'Error');
        }
    }

    /**
     * 5. 新生 微信公众号支付订单
     * @param $params
     * @return mixed|void
     */
    public function mpPayApply($params)
    {
        // 扫码支付测试
        $apiUrl = $this->baseUrl . '/PaymentCenter/mpPayApply';

        $params['mer_id'] = $this->merId;

        try {
            $apiClient = new APIClient($this->privateKey, $this->publicKey);
            return $apiClient->callApi($apiUrl, $params);

        } catch (Exception $e) {
            Utils::fecho($e->getMessage(), 'Error');
        }
    }

    /**
     * 6. 新生 微信公众号支付订单
     * @param $params
     * @return mixed|void
     */
    public function mpScanPayApply($params)
    {
        // 扫码支付测试
        $apiUrl = $this->baseUrl . '/PaymentCenter/mpScanPayApply';

        $params['mer_id'] = $this->merId;

        try {
            $apiClient = new APIClient($this->privateKey, $this->publicKey);
            return $apiClient->callApi($apiUrl, $params);

        } catch (Exception $e) {
            Utils::fecho($e->getMessage(), 'Error');
        }
    }

    /**
     * 新生 微信公众号支付订单查询
     * @param $params
     * @return mixed|void
     */
    public function mpPayQuery($params)
    {
        $apiUrl = $this->baseUrl . '/PaymentCenter/mpPayQuery';

        $params['mer_id'] = $this->merId;

        try {
            $apiClient = new APIClient($this->privateKey, $this->publicKey);
            return $apiClient->callApi($apiUrl, $params);

        } catch (Exception $e) {
            Utils::fecho($e->getMessage(), 'Error');
        }
    }
}