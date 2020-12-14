<?php

namespace libs\pospal\api;

/**
 * @author Jony
 */
class Order extends Base
{

    /**
     * @param array $params
     *
     * @return \libs\pospal\Result|\Psr\Http\Message\ResponseInterface
     * @throws \yii\base\ErrorException
     * @throws \yii\web\HttpException
     */
    public function add(array $params)
    {
        return $this->api->request('pospal-api2/openapi/v1/orderOpenApi/addOnLineOrder', $params);
    }
}

?>
