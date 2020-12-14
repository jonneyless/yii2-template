<?php

namespace libs\pospal\api;

/**
 * @author Jony
 */
class Product extends Base
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
        return $this->api->request('pospal-api2/openapi/v1/productOpenApi/addProductInfo', $params);
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
        return $this->api->request('pospal-api2/openapi/v1/productOpenApi/updateProductInfo', $params);
    }

    /**
     * @param array|string $params
     *
     * @return \libs\pospal\Result|\Psr\Http\Message\ResponseInterface
     * @throws \yii\base\ErrorException
     * @throws \yii\web\HttpException
     */
    public function queryById($params)
    {
        if(!is_array($params)){
            $params = ['productUid' => $params];
        }

        return $this->api->request('pospal-api2/openapi/v1/productOpenApi/queryProductByUid', $params);
    }

    /**
     * @param array|string $params
     *
     * @return \libs\pospal\Result|\Psr\Http\Message\ResponseInterface
     * @throws \yii\base\ErrorException
     * @throws \yii\web\HttpException
     */
    public function queryByBarcode($params)
    {
        if(!is_array($params)){
            $params = ['barcode' => $params];
        }

        return $this->api->request('pospal-api2/openapi/v1/productOpenApi/queryProductByBarcode', $params);
    }

    /**
     * @param array $params
     *
     * @return \libs\pospal\Result|\Psr\Http\Message\ResponseInterface
     * @throws \yii\base\ErrorException
     * @throws \yii\web\HttpException
     */
    public function member(array $params)
    {
        return $this->api->request('pospal-api2/openapi/v1/productOpenApi/updateProductCustomerPrice', $params);
    }
}

?>
