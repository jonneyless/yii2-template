<?php

namespace libs\grab;

use yii\helpers\Json;

/**
 * 淘宝抓取类
 *
 * @author Jony
 */
class Taobao extends Base
{

	public function getTitle()
	{
        preg_match('/title\s*:\s*\'(.*)\',/', $this->_page, $matchs);

        return isset($matchs[1]) ? $this->fromAscii($matchs[1]) : '';
	}

	public function getIcon()
	{
		preg_match('/auctionImages\s*:\s*\[(.*)\]/', $this->_page, $matchs);

		$json = isset($matchs[1]) ? $matchs[1] : '';
        $json = '[' . $json . ']';
		$json = Json::decode($json);

		return current($json);
	}

	public function getImages()
	{
        preg_match('/auctionImages\s*:\s*\[(.*)\]/', $this->_page, $matchs);

        $json = isset($matchs[1]) ? $matchs[1] : '';
        $json = '[' . $json . ']';
        $json = Json::decode($json);

        return array_slice($json, 1);
	}

	public function getDesc()
	{
		preg_match('/\'\/\/dsc.taobaocdn.com\/([^\']*)\'/', $this->_page, $matchs);

		$content = '';
		if(isset($matchs[1])){
			$descUrl = 'http://dsc.taobaocdn.com/' . $matchs[1];
			$content = trim(self::grab($descUrl));
			$content = substr($content, 10, strlen($content) - 2);
		}

		$content = preg_replace('/<a[^>]*>/', '', $content);
		$content = preg_replace('/<\/a>/', '', $content);
		$content = preg_replace('/\\\\/', '', $content);
        $content = preg_replace('/width="\d+"/', '', $content);
        $content = preg_replace('/height="\d+"/', '', $content);
        $content = preg_replace('/style="[^"]+"/', '', $content);

		return $this->iconv($content);
	}

	public function replace($content)
	{
		preg_match_all('/(background|src)[0-9]?\s*=\s*["|\']\s*(http[^"\']*)\s*["|\']/i', $content, $matchs);

		if(isset($matchs[2])){
			foreach($matchs[2] as $image){
				$file = $this->grabImage($image);
				$content = str_replace($image, $file, $content);
			}
		}

		return $content;
	}
}

?>
