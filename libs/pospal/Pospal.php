<?php

namespace libs\pospal;

use api\models\Store;
use Yii;
use yii\base\Component;
use yii\base\ErrorException;
use yii\helpers\Json;

/**
 * @author Jony
 *
 * @property $apiUrl
 * @property $appId
 * @property $appKey
 * @property \libs\pospal\Api $api
 *
 * @property \libs\pospal\api\Product product
 * @property \libs\pospal\api\User user
 * @property \libs\pospal\api\Ticket ticket
 * @property \libs\pospal\api\Base base
 * @property \libs\pospal\api\Order order
 */
class Pospal extends Component
{

    public $apiUrl;
    public $appId;
    public $appKey;

    private $api;

    /**
     * {@inheritdoc}
     */
	public function init()
	{
		$this->api = new Api($this->apiUrl, $this->appId, $this->appKey);
	}

	public function setStore($appId, $appKey)
	{
		if(!$appId || !$appKey){
			throw new ErrorException('appId 和 appKey 不能为空！');
		}

		$this->appId = $appId;
		$this->appKey = $appKey;

		$this->init();

		return $this;
	}

	public static function notify()
	{
        $notifyRaw = Yii::$app->request->getRawBody();
        $signature = Yii::$app->request->getHeaders()->get('data-signature');
        $notify = Json::decode($notifyRaw);
        $notify['body'] = Json::decode($notify['body']);

        Yii::info("Signature => " . $signature . "\nRaw => " . $notifyRaw . "\nNotify => " . print_r($notify, true), 'notify');

        $cmd = $notify['cmd'];
        $appId = $notify['appId'];
        $params = $notify['body'];

        $store = Store::findByAppId($appId);

        if(!$store){
            return;
        }

		list($className, $action) = explode(".", $cmd);

		if(!$className || !$action){
			return;
		}

        $class = '\libs\pospal\notify\\' . ucfirst($className);
		$function = 'action' . ucfirst($action);

		if(!method_exists($class, $function)){
		    return;
        }

        return (new $class($store))->$function($params);
	}

    /**
     * @param $name
     *
     * @return mixed
     */
	public function __get($name)
    {
    	if(isset($this->$name)){
    		return $this->$name;
		}else{
    		$class = '\libs\pospal\api\\' . ucfirst($name);

    		return new $class($this->api);
		}
    }
}

?>
