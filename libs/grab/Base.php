<?php

namespace libs\grab;

use ijony\helpers\File;
use ijony\helpers\Folder;
use libs\Grab;
use Yii;

class Base
{

	public $_page = '';

	public function __construct($param)
	{
		if(substr($param, 0, 4) == 'http'){
			$this->_page = self::grab($param);
		}
	}

    public function fromAscii($string) {
        $string = sprintf('["%s"]', $string);
        $charCode = json_decode($string, true);
        $result = '';
        foreach ($charCode as $code) {
            $result .= $code;
        };
        return $result;
    }

	public function getTitle()
	{
		return '';
	}

	public function getNumber()
	{
		return '';
	}

	public function getIcon()
	{
		return '';
	}

	public function getImages()
	{
		return array();
	}

	public function getDesc()
	{
		return '';
	}

	public static function grab($url, $referer = '')
	{
		if(!$referer){
			$referer = 'http://www.google.com.hk/';
		}

		if(substr($url, 0, 2) == '//'){
            $url = 'http:' . $url;
		}

		$ch = curl_init();

		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_TIMEOUT, 30);
		curl_setopt($ch, CURLOPT_REFERER, $referer);

        if(substr($url, 0, 4) == 'http'){
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        }

		curl_setopt($ch, CURLOPT_HTTPHEADER, array("Referer: $referer"));
		curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 9.0; Windows NT 6.1; Trident/5.0)');
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);

		$content = curl_exec($ch);

		curl_close($ch);

		if(preg_match('/302/', $content)){
			$content = file_get_contents($url);
		}

		return $content;
	}

	public function grabImage($url)
	{
	    if(substr($url, 0, 2) == '//'){
            $url = 'http:' . $url;
        }

		$img = @file_get_contents($url);
		$url = parse_url($url);
		$newImg = File::newBufferFile(substr($url['path'], strrpos($url['path'], '.')), Yii::$app->user->id);
		$newImgFull = Folder::getStatic($newImg);
		@file_put_contents($newImgFull, $img);
		@chmod($newImgFull, 0777);
		return $newImg;
	}

	public function replace($content)
	{
		return $content;
	}

	public function iconv($string)
	{
        $string = trim($string);

		if(Grab::run()->charset == 'GBK'){
			$string = mb_convert_encoding($string, "UTF-8", "GBK");
		}

		return $string;
	}
}

?>
