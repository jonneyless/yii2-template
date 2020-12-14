<?php

namespace libs\pospal\api;

/**
 * @author Jony
 */
class User extends Base
{

    /**
     * @param array $params
     *
     * @return \libs\pospal\Result|\Psr\Http\Message\ResponseInterface
     * @throws \yii\base\ErrorException
     * @throws \yii\web\HttpException
     */
    public function create(array $params)
    {
        return $this->api->request('pospal-api2/openapi/v1/customerOpenApi/add', $params);
    }

    /**
     * @param array $params
     *
     * @return \libs\pospal\Result|\Psr\Http\Message\ResponseInterface
     * @throws \yii\base\ErrorException
     * @throws \yii\web\HttpException
     */
    public function update(array $params)
    {
        return $this->api->request('pospal-api2/openapi/v1/customerOpenApi/updateBaseInfo', $params);
    }

    /**
     * @param array $params
     *
     * @return \libs\pospal\Result|\Psr\Http\Message\ResponseInterface
     * @throws \yii\base\ErrorException
     * @throws \yii\web\HttpException
     */
    public function updateBalance(array $params)
    {
        return $this->api->request('pospal-api2/openapi/v1/customerOpenApi/updateBalancePointByIncrement', $params);
    }

    /**
     * @param array $params
     *
     * @return \libs\pospal\Result|\Psr\Http\Message\ResponseInterface
     * @throws \yii\base\ErrorException
     * @throws \yii\web\HttpException
     */
    public function resetPass(array $params)
    {
        return $this->api->request('pospal-api2/openapi/v1/customerOpenApi/updateCustomerPassword', $params);
    }

    /**
     * @param $mobile
     *
     * @return \libs\pospal\Result|\Psr\Http\Message\ResponseInterface
     * @throws \yii\base\ErrorException
     * @throws \yii\web\HttpException
     */
    public function findByMobile($mobile)
    {
        return $this->api->request('pospal-api2/openapi/v1/customerOpenapi/queryBytel', [
            'customerTel' => $mobile,
        ]);
    }

    /**
     * @param $code
     *
     * @return \libs\pospal\Result|\Psr\Http\Message\ResponseInterface
     * @throws \yii\base\ErrorException
     * @throws \yii\web\HttpException
     */
    public function findByCode($code)
    {
        return $this->api->request('pospal-api2/openapi/v1/customerOpenapi/queryByNumber', [
            'customerNum' => $code,
        ]);
    }

    /**
     * @return \libs\pospal\Result|\Psr\Http\Message\ResponseInterface
     * @throws \yii\base\ErrorException
     * @throws \yii\web\HttpException
     */
    public function queryMemberInfo()
    {
        return $this->api->request('pospal-api2/openapi/v1/customerOpenApi/queryAllCustomerCategory');
    }
}

?>
