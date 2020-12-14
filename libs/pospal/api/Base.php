<?php

namespace libs\pospal\api;

/**
 * @author Jony
 *
 * @property \libs\pospal\Api $api
 */
class Base
{

    protected $api;

    public function __construct($api)
    {
        $this->api = $api;
    }

    /**
     * @return \libs\pospal\Result|\Psr\Http\Message\ResponseInterface
     * @throws \yii\base\ErrorException
     * @throws \yii\web\HttpException
     */
    public function apiDaily(array $params)
    {
        return $this->api->request('pospal-api2/openapi/v1/openApiLimitAccess/queryDailyAccessTimesLog', $params);
    }

    /**
     * @return \libs\pospal\Result|\Psr\Http\Message\ResponseInterface
     * @throws \yii\base\ErrorException
     * @throws \yii\web\HttpException
     */
    public function notify()
    {
        return $this->api->request('pospal-api2/openapi/v1/openNotificationOpenApi/queryPushUrl');
    }

    /**
     * @param array $params
     *
     * @return \libs\pospal\Result|\Psr\Http\Message\ResponseInterface
     * @throws \yii\base\ErrorException
     * @throws \yii\web\HttpException
     */
    public function setNotify(array $params)
    {
        return $this->api->request('pospal-api2/openapi/v1/openNotificationOpenApi/updatePushUrl', $params);
    }
}

?>
