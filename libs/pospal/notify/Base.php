<?php

namespace libs\pospal\notify;

use api\models\Store;
use Yii;
use yii\base\ErrorException;

/**
 * @author Jony
 *
 * @property \api\models\Store $store
 * @property \libs\pospal\Pospal $api
 */
class Base
{

    public $store;
    public $api;

    public function __construct(Store $store)
    {
        $this->store = $store;
        $this->api = Yii::$app->pospal->setStore($store->pospal_app_id, $store->pospal_app_key);
    }

    public function out($msg, $throw = false)
    {
        if($throw){
            throw new ErrorException($msg);
        }else{
            echo $msg;
        }
        Yii::$app->end();
    }
}

?>
