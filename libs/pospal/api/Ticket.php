<?php

namespace libs\pospal\api;

/**
 * @author Jony
 */
class Ticket extends Base
{

    /**
     * @param array $params
     *
     * @return \libs\pospal\Result|\Psr\Http\Message\ResponseInterface
     * @throws \yii\base\ErrorException
     * @throws \yii\web\HttpException
     */
    public function get(array $params)
    {
        return $this->api->request('pospal-api2/openapi/v1/ticketOpenApi/queryTicketBySn', $params);
    }
}

?>
