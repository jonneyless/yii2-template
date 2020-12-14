<?php

namespace libs\pospal;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Yii;
use yii\base\ErrorException;
use yii\helpers\Json;

/**
 * @author Jony
 *
 * @property $apiUrl
 * @property $appId
 * @property $appKey
 */
class Api
{

    private $apiUrl;
    private $appId;
    private $appKey;

    /**
     * Api constructor.
     *
     * @param $apiUrl
     * @param $appId
     * @param $appKey
     */
    public function __construct($apiUrl, $appId, $appKey)
	{
        $this->apiUrl = rtrim($apiUrl, '/') . '/';
        $this->appId = $appId;
        $this->appKey = $appKey;
	}

    /**
     * @param       $api
     * @param array $params
     *
     * @return \libs\pospal\Result|\Psr\Http\Message\ResponseInterface
     * @throws \yii\base\ErrorException
     * @throws \yii\web\HttpException
     */
    public function request($api, array $params = [])
	{
		$client = new Client();
		$apiUrl = $this->parseUrl($api);
		$params = $this->parseParams($params);

		try{
            $response = $client->post($apiUrl, $params);

            $result = Json::decode($response->getBody()->getContents());

            Yii::info("\nApi => " . $apiUrl . "\nParmas => " . print_r($params, true) . "\nResult => " . print_r($result, true), 'pospal');

            return new Result($result);
		}catch(GuzzleException $exception){
			throw new ErrorException($exception->getMessage());
		}
	}

    /**
     * @param $api
     *
     * @return string
     */
	private function parseUrl($api)
	{
		return $this->apiUrl . ltrim($api, '/');
	}

    /**
     * @param array $params
     *
     * @return array
     */
	private function parseParams(array $params)
	{
        list($msec, $sec) = explode(' ', microtime());
        $msectime =  (float)sprintf('%.0f', (floatval($msec) + floatval($sec)) * 1000);

        $params['appId'] = $this->appId;

        $json = Json::encode($params);

		return [
            'headers' => [
                'User-Agent' => 'openApi',
                'Content-Type' => 'application/json; charset=utf-8',
                'accept-encoding' => 'gzip,deflate',
                'time-stamp' => $msectime,
                'data-signature' => strtoupper(md5($this->appKey . $json))
            ],
            'body' => $json,
		];
	}
}

?>
