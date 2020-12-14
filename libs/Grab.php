<?php

namespace libs;

/**
 * 网店产品信息抓取类
 *
 * @author Jony
 *
 * @property \libs\grab\JD|\libs\grab\Taobao|\libs\grab\TMall $api;
 */
class Grab
{
	private static $_instance;

	public static $_platform = array(
		'detail.tmall.com' => array('class' => 'TMall', 'name' =>'天猫', 'charset' =>'GBK'),
		'detail.tmall.hk' => array('class' => 'TMall', 'name' =>'天猫', 'charset' =>'GBK'),
		'chaoshi.detail.tmall.com' => array('class' => 'TMall', 'name' =>'天猫', 'charset' =>'GBK'),
		'item.taobao.com' => array('class'=>'Taobao', 'name'=>'淘宝', 'charset'=>'GBK'),
		'item.jd.com' => array('class'=>'JD', 'name'=>'京东', 'charset'=>'GBK'),
	);

	public $platform = 'Base';
	public $name = '';
	public $charset = '';
	protected $api;

	public function __construct()
	{

	}

	public static function run()
	{
		if(self::$_instance === null){
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	public function init($param)
	{
		if(!$this->checkPlatform($param)){
			$data = parse_url($param);
			if(isset($data['host']) && isset(self::$_platform[$data['host']]) && self::$_platform[$data['host']]){
				$this->platform = self::$_platform[$data['host']]['class'];
				$this->name = self::$_platform[$data['host']]['name'];
				$this->charset = self::$_platform[$data['host']]['charset'];
			}
		}

		$className = 'libs\\grab\\' . $this->platform;

		$this->api = new $className($param);
	}

	public static function api()
	{
		return self::run()->api;
	}

	private function checkPlatform($name)
	{
		foreach(self::$_platform as $platform){
			if($platform['class'] == $name){
				$this->platform = $platform['class'];
				$this->name = $platform['name'];
				$this->charset = $platform['charset'];
				return true;
			}
		}

		return false;
	}
}

?>
