<?php

namespace libs\grab;

use yii\helpers\Json;

/**
 * 京东抓取类
 *
 * @author Jony
 */
class JD extends Base
{

	private $imgUrl = 'http://img1.360buyimg.com/n1/s800x800_';

	public function getTitle()
	{
		preg_match('/name: \'(.*)\',/', $this->_page, $matchs);

		return isset($matchs[1]) ? $this->fromAscii($matchs[1]) : '';
	}

	public function getIcon()
	{
		preg_match('/src: \'([^\']*)\',/', $this->_page, $matchs);

		if(isset($matchs[1])){
			return $this->imgUrl . $matchs[1];
		}

		return '';
	}

	public function getImages()
	{
        preg_match('/imageList: (.*),/', $this->_page, $matchs);

		$imgs = [];

		if(isset($matchs[1])){
			$imgs = Json::decode($matchs[1]);
            $imgs = array_slice($imgs, 1);
            $imgs = array_map(function($img){
                return $this->imgUrl . $img;
			}, $imgs);
		}

		return $imgs;
	}

	public function getDesc()
	{
		preg_match('/<div id="J-detail-content">([\s\S]*)<\/div><\!\-\- #J\-detail\-content \-\->/', $this->_page, $matchs);

		$content = '';
		if(isset($matchs[1])){
			$content = preg_replace('/<div class="formwork_bt"><div class="formwork_bt_dz"><span>[^<]*<\/span><span class="[^"<]*">[^<]*<\/span><\/div><\/div>/', '', $matchs[1]);
			$content = str_replace('data-lazyload="', 'src="', $content);
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
