<?php

namespace libs\grab;

/**
 * 天猫抓取类
 *
 * @author Jony
 */
class TMall extends Base
{

	public function getTitle()
	{
		preg_match('/<input type="hidden" name="title" value="([^"]*)" \/>/', $this->_page, $matchs);

		return isset($matchs[1]) ? $this->iconv($matchs[1]) : '';
	}

	public function getNumber()
	{
		preg_match('/<ul id="J_AttrUL">([\s\S]*)<div id="J_DcTopRightWrap">/', $this->_page, $matchs);
		if(!isset($matchs[1]) || !$matchs[1]) return '';
		$string = iconv('GBK', 'UTF-8', $matchs[1]);

		preg_match('/">商品条形码:&nbsp;([0-9]*)<\/li>/', $string, $matchs);

		return isset($matchs[1]) ? $matchs[1] : '';
	}

	public function getIcon()
	{
		preg_match('/<img src="([^"]*)_60x60(|q90).jpg" \/>/', $this->_page, $matchs);

		return isset($matchs[1]) ? $matchs[1] : '';
	}

	public function getImages()
	{
		preg_match_all('/<img src="([^"]*)_60x60(|q90).jpg" \/>/', $this->_page, $matchs);

		return isset($matchs[1]) ? $matchs[1] : array();
	}

	public function getDesc()
	{
		preg_match('/"descUrl"\s?:\s?"([^"]*)"/', $this->_page, $matchs);

		$content = '';
		if(isset($matchs[1])){
			$descUrl = $matchs[1];
			$content = self::grab($descUrl);
            $content = trim($content);
			$content = substr($content, 10, strlen($content) - 2);
		}

		$content = preg_replace('/<a[^>]*>/', '', $content);
		$content = preg_replace('/<\/a>/', '', $content);
        $content = preg_replace('/width="\d+"/', '', $content);
        $content = preg_replace('/height="\d+"/', '', $content);
        $content = preg_replace('/style="[^"]+"/', '', $content);

		return $this->iconv($content);
	}

	public function replace($content)
	{
		preg_match_all('/src[0-9]?\s*=\s*["|\']\s*(http[^"\']*)\s*["|\']/i', $content, $matchs);

		if(isset($matchs[1])){
			foreach($matchs[1] as $image){
				$file = $this->grabImage($image);
				$content = str_replace($image, $file, $content);
			}
		}

		return $content;
	}
}

?>
