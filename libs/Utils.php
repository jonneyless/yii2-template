<?php

namespace libs;

use Imagine\Image\ManipulatorInterface;
use Yii;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\imagine\Image;

/**
 * 通用工具类
 *
 * @package libs
 */
class Utils
{

    /**
     * 用于调试的变量输出
     *
     * @param      $data 要打印的变量
     * @param bool $end 程序中断开关
     *
     * @throws \yii\base\ExitException
     */
    public static function dump($data, $end = true)
    {
        echo '<pre>' . print_r($data, true) . '</pre>';

        if ($end) {
            if (class_exists('Yii')) {
                Yii::$app->end();
            } else {
                die();
            }
        }
    }

    /**
     * 生成页面 Title
     *
     * @param $title
     *
     * @return string
     */
    public static function headTitle($title)
    {
        if (is_array($title)) {
            $title = join(" - ", $title);
        }

        if ($title) {
            $title = " - " . $title;
        }

        return Html::encode(Yii::$app->name . $title);
    }

    /**
     * 获取 Cookie
     *
     * @param string $key
     * @param string $default
     *
     * @return mixed
     */
    public static function getCookie($key, $default = '')
    {
        return Yii::$app->request->getCookies()->getValue($key, $default);
    }

    /**
     * 写入 Cookie
     *
     * @param string $key
     * @param string $value
     */
    public static function setCookie($key, $value)
    {
        return Yii::$app->request->getCookies()->add(new Cookie([
            'name' => $key,
            'value' => $value,
        ]));
    }

    /**
     * 去掉字串中的 html 标签代码
     *
     * @param string $string 要处理的字符串
     *
     * @return string
     */
    public static function emptyTag($string)
    {
        if ($string) {
            $string = strip_tags(trim($string));
            $string = preg_replace("|&.+?;|", '', $string);
        }

        return $string;
    }

    /**
     * 遍历生成目录
     *
     * @param string $dirpath 要生成的目录路径
     *
     * @return string
     */
    public static function mkdir($dirpath)
    {
        $root = Yii::getAlias('@static') . DIRECTORY_SEPARATOR;
        $root = preg_replace('/[\\\\\/]/', DIRECTORY_SEPARATOR, $root);
        $dirpath = preg_replace('/[\\\\\/]/', DIRECTORY_SEPARATOR, $dirpath);

        if ($dirpath != $root && !file_exists($dirpath)) {
            $path = explode(DIRECTORY_SEPARATOR, str_replace($root, '', $dirpath));

            $dirpath = $root . array_shift($path);

            if (!file_exists($dirpath)) {
                @mkdir($dirpath);
                @chmod($dirpath, 0777);
            }

            foreach ($path as $dir) {
                $dirpath .= DIRECTORY_SEPARATOR . $dir;

                if ($dir != '.' && $dir != '..') {
                    if (!file_exists($dirpath)) {
                        @mkdir($dirpath);
                        @chmod($dirpath, 0777);
                    }
                }
            }
        }

        return $dirpath;
    }

    /**
     * 遍历删除目录以及其所有子目录和文件
     *
     * @param string $folder 要删除的目录路径
     *
     * @return bool
     */
    public static function rmdir($folder)
    {
        set_time_limit(0);

        if (!file_exists($folder)) {
            return false;
        }

        $files = array_diff(scandir($folder), ['.', '..']);

        foreach ($files as $file) {
            $file = $folder . DIRECTORY_SEPARATOR . $file;
            (is_dir($file) && !is_link($folder)) ? self::rmdir($file) : unlink($file);
        }

        return rmdir($folder);
    }

    /**
     * 获取绝对物理路径
     *
     * @param string $path 获取指定路径的绝对路径的相对目录路径
     *
     * @return string
     */
    public static function staticFolder($path = null)
    {
        if ($path) {
            $path = Yii::getAlias('@static/' . ltrim($path, '/'));
        }

        return $path;
    }

    /**
     * 获取绝对网址路径
     *
     * @param string $path 要生成静态文件绝对网址的相对目录路径
     *
     * @return string
     */
    public static function staticUrl($path = null)
    {
        if ($path) {
            $path = 'http://static.gxtewang.lvh.me/' . ltrim($path, '/');
        }

        return $path;
    }

    public static function fullUrl($url)
    {
        if (is_array($url)) {
            $url = Url::to($url);
        }

        return 'http://' . Yii::$app->request->getHostName() . $url;
    }

    /**
     * 生成文件名
     *
     * @param string $ext 文件后缀
     *
     * @return string
     */
    public static function newFileName($ext)
    {
        list($usec, $sec) = explode(" ", microtime());

        $fix = substr($usec, 2, 4);

        return date('YmdHis') . $fix . "." . ltrim($ext, ".");
    }

    /**
     * 根据后缀生成上传文件相对路径
     *
     * @param        $ext
     * @param string $folder
     *
     * @return string
     */
    public static function newFile($ext, $folder = 'image')
    {
        if ($folder) {
            $folder = '/' . ltrim($folder, '/');
        }

        $folder = UPLOAD_FOLDER . $folder . '/' . date('Ym') . '/' . date('d') . '/' . date('H') . '/';

        self::mkdir(self::staticFolder($folder));

        $newFile = $folder . self::newFileName(ltrim($ext, "."));

        while (file_exists(self::staticFolder($newFile))) {
            $newFile = $folder . self::newFileName(ltrim($ext, "."));
        }

        return $newFile;
    }

    /**
     * 生成用户临时图片地址
     *
     * @param string $ext 图片后缀
     *
     * @return string
     */
    public static function newBufferFile($ext)
    {
        $ext = strtolower($ext);

        if (Yii::$app->user->getIsGuest()) {
            $bufferFolder = BUFFER_FOLDER . '/temp/';
        } else {
            $bufferFolder = BUFFER_FOLDER . '/' . Yii::$app->user->id . '/';
        }

        self::mkdir(self::staticFolder($bufferFolder));

        $newImg = $bufferFolder . self::newFileName(ltrim($ext, "."));

        while (file_exists(self::staticFolder($newImg))) {
            $newImg = $bufferFolder . self::newFileName(ltrim($ext, "."));
        }

        return $newImg;
    }

    /**
     * 将临时图片转入正式文件夹
     *
     * @param string $image
     * @param bool $clear
     *
     * @return string
     */
    public static function coverBufferImage($image, $clear = false)
    {
        if ($image && substr($image, 0, 6) == BUFFER_FOLDER) {
            $oldImg = $image;
            $newImg = Utils::copyImg($image);

            if ($newImg && $clear) {
                Utils::delFile($oldImg, true);
            }

            return $newImg;
        }

        return $image;
    }

    /**
     * 清理用户临时图片
     */
    public static function clearBuffer()
    {

        if (!Yii::$app->user->getIsGuest()) {
            $bufferFolder = BUFFER_FOLDER . '/' . Yii::$app->user->id . '/';
            self::rmdir(self::staticFolder($bufferFolder));
        }
    }

    /**
     * 复制图片
     *
     * @param string $oldImg 要复制的图片地址
     * @param string $newImg 指定要生成的图片地址，如不指定则自动生成新图地址
     * @param boolean $full 是否返回新图绝对地址的开关，默认：false
     *
     * @return boolean或者字串，复制失败就返回 false，否则返回新图地址
     */
    public static function copyImg($oldImg, $newImg = '', $full = false)
    {
        if (substr($oldImg, 0, 4) != 'http') {
            $oldImgFull = self::staticFolder($oldImg);

            if (!$oldImg || !file_exists($oldImgFull)) {
                return $oldImg;
            }
        } else {
            $oldImgFull = $oldImg;
        }

        if (!$newImg) {
            $newImg = self::newFile(pathinfo($oldImgFull, PATHINFO_EXTENSION), 'image');
        }

        $newImgFull = self::staticFolder($newImg);

        self::mkdir(pathinfo($newImgFull, PATHINFO_DIRNAME));

        if (self::saveFile($newImgFull, $oldImgFull)) {
            return $full ? $newImgFull : $newImg;
        }

        return false;
    }

    /**
     * 将文件保存
     *
     * @param string $file 目标文件
     * @param string $source 源文件
     *
     * @return boolean
     */
    public static function saveFile($file, $source)
    {
        if (@copy($source, $file)) {
            return true;
        } else {
            if (function_exists('move_uploaded_file') && @move_uploaded_file($source, $file)) {
                return true;
            } else {
                if (@is_readable($source) && (@$fp_s = fopen($source, 'rb')) && (@$fp_t = fopen($file, 'wb'))) {

                    while (!feof($fp_s)) {
                        $s = @fread($fp_s, 1024 * 512);
                        @fwrite($fp_t, $s);
                    }

                    fclose($fp_s);
                    fclose($fp_t);

                    return true;
                } else {
                    return false;
                }
            }
        }
    }

    /**
     * 将文件删除，第二个参数用于清理缩略图
     *
     * @param      $file
     * @param bool $image
     */
    public static function delFile($file, $image = false)
    {
        $fileFull = self::staticFolder($file);

        if (file_exists($fileFull) && !is_dir($fileFull)) {
            @unlink($fileFull);
        }

        if ($image) {
            $pathInfo = pathinfo($file);

            if ($pathInfo['dirname'] == UPLOAD_FOLDER) {
                $thumbFolder = THUMB_FOLDER;
            } else {
                $thumbFolder = str_replace(UPLOAD_FOLDER . '/', THUMB_FOLDER . '/', $pathInfo['dirname']);
            }

            $thumbs = $thumbs = glob(self::staticFolder($thumbFolder . '/' . $pathInfo['filename'] . '_*.' . $pathInfo['extension']));

            if ($thumbs) {
                foreach ($thumbs as $thumb) {
                    @unlink($thumb);
                }
            }
        }
    }

    /**
     * 输出组图大图
     *
     * @param     $image
     * @param int $width
     *
     * @return string
     */
    public static function galleryImage($image, $width = 800)
    {
        return self::getImg($image, $width);
    }

    /**
     * 输出组图小图
     *
     * @param     $image
     * @param int $width
     * @param int $height
     *
     * @return string
     */
    public static function galleryNav($image, $width = 80, $height = 80)
    {
        return self::getImg($image, $width, $height);
    }

    /**
     * 图片处理，用于生成小图
     *
     * @param string $oldImg 原图地址
     * @param int $width 图片宽度
     * @param int $height 图片高度
     * @param bool $default 默认图开关
     * @param string $cut 切图模式
     *
     * @return string 图片地址
     */
    public static function getImg($oldImg, $width = 0, $height = 0, $default = true, $cut = ManipulatorInterface::THUMBNAIL_OUTBOUND)
    {
        if (substr($oldImg, 0, 4) == 'http') {
            return $oldImg;
        }

        $oldImgFull = self::staticFolder($oldImg);

        if (!file_exists($oldImgFull) || is_dir($oldImgFull)) {
            if (YII_ENV === 'dev' && $oldImg) {
                return 'http://static.lingzan.net/' . $oldImg;
            }

            if ($default) {
                if ($default === true) {
                    return self::staticUrl('upload/default.jpg');
                } else {
                    return self::staticUrl($default);
                }
            } else {
                return '';
            }
        }

        if ($width == 0) {
            return self::staticUrl($oldImg);
        }

        if ($height == 0) {
            list($oWidth, $oHeight) = getimagesize($oldImgFull);
            $height = intval(($oHeight * $width) / $oWidth);
        }

        $pathInfo = pathinfo($oldImg);

        if ($pathInfo['dirname'] == UPLOAD_FOLDER) {
            $thumbFolder = THUMB_FOLDER;
        } else {
            $thumbFolder = str_replace(UPLOAD_FOLDER . '/', THUMB_FOLDER . '/', $pathInfo['dirname']);
        }

        $newImg = $thumbFolder . '/' . $pathInfo['filename'] . '_' . $width . 'x' . $height . '.' . $pathInfo['extension'];

        $newImgFull = self::staticFolder($newImg);

        if (!file_exists($newImgFull)) {
            self::mkdir(self::staticFolder($thumbFolder));

            Image::thumbnail($oldImgFull, $width, $height, $cut)
                ->save($newImgFull, ['quality' => 100]);
        }

        return self::staticUrl($newImg);
    }

    /**
     * 生成随机字符串
     *
     * @param integer $len 要获得的随机字符串长度
     * @param bool $onlyNum
     *
     * @return string
     */
    public static function getRand($len = 12, $onlyNum = false)
    {
        $chars = $onlyNum ? '0123456789' : 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
        mt_srand((double) microtime() * 1000000 * getmypid());
        $return = '';
        while (strlen($return) < $len) {
            $return .= substr($chars, (mt_rand() % strlen($chars)), 1);
        }

        return $return;
    }

    /**
     * UTF8 字符串截取
     *
     * @param string $str 要截取的字符串
     * @param integer $start 截取起始位置
     * @param integer $len 截取长度
     *
     * @return string
     */
    public static function substr($str, $start = 0, $len = 50)
    {
        return mb_strlen($str) > $len ? mb_substr($str, $start, $len, 'UTF-8') . "…" : $str;
    }

    /**
     * 字符串中间部分星号加密
     * 如果是邮箱地址，则只加密位于 @ 前的字串
     *
     * @param string $str 要星号加密的字符串
     *
     * @return string
     */
    public static function starcode($str)
    {
        $suffix = '';

        if (filter_var($str, FILTER_VALIDATE_EMAIL)) {
            list($str, $suffix) = explode("@", $str);
        }

        $len = intval(strlen($str) / 2);
        $str = substr_replace($str, str_repeat('*', $len), ceil(($len) / 2), $len);

        return $suffix ? $str . '@' . $suffix : $str;
    }

    /**
     * 判断是否是手机登录
     *
     * @return bool
     */
    public static function isMobile()
    {
        // 如果有HTTP_X_WAP_PROFILE则一定是移动设备
        if (isset ($_SERVER['HTTP_X_WAP_PROFILE'])) {
            return true;
        }
        // 如果via信息含有wap则一定是移动设备,部分服务商会屏蔽该信息
        if (isset ($_SERVER['HTTP_VIA'])) {
            // 找不到为flase,否则为true
            return stristr($_SERVER['HTTP_VIA'], "wap") ? true : false;
        }
        // 脑残法，判断手机发送的客户端标志,兼容性有待提高
        if (isset ($_SERVER['HTTP_X_REQUESTED_WITH'])) {
            $clientkeywords = [
                'nokia',
                'sony',
                'ericsson',
                'mot',
                'samsung',
                'htc',
                'sgh',
                'lg',
                'sharp',
                'sie-',
                'philips',
                'panasonic',
                'alcatel',
                'lenovo',
                'iphone',
                'ipod',
                'blackberry',
                'meizu',
                'android',
                'netfront',
                'symbian',
                'ucweb',
                'windowsce',
                'palm',
                'operamini',
                'operamobi',
                'openwave',
                'nexusone',
                'cldc',
                'midp',
                'wap',
                'mobile',
            ];
            // 从HTTP_USER_AGENT中查找手机浏览器的关键字
            if (preg_match("/(" . implode('|', $clientkeywords) . ")/i", strtolower($_SERVER['HTTP_X_REQUESTED_WITH']))) {
                return true;
            }
        }
        // 脑残法，判断手机发送的客户端标志,兼容性有待提高
        if (isset ($_SERVER['HTTP_USER_AGENT'])) {
            $clientkeywords = [
                'nokia',
                'sony',
                'ericsson',
                'mot',
                'samsung',
                'htc',
                'sgh',
                'lg',
                'sharp',
                'sie-',
                'philips',
                'panasonic',
                'alcatel',
                'lenovo',
                'iphone',
                'ipod',
                'blackberry',
                'meizu',
                'android',
                'netfront',
                'symbian',
                'ucweb',
                'windowsce',
                'palm',
                'operamini',
                'operamobi',
                'openwave',
                'nexusone',
                'cldc',
                'midp',
                'wap',
                'mobile',
            ];
            // 从HTTP_USER_AGENT中查找手机浏览器的关键字
            if (preg_match("/(" . implode('|', $clientkeywords) . ")/i", strtolower($_SERVER['HTTP_USER_AGENT']))) {
                return true;
            }
        }
        // 协议法，因为有可能不准确，放到最后判断
        if (isset ($_SERVER['HTTP_ACCEPT'])) {
            // 如果只支持wml并且不支持html那一定是移动设备
            // 如果支持wml和html但是wml在html之前则是移动设备
            if ((strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') !== false) && (strpos($_SERVER['HTTP_ACCEPT'], 'text/html') === false || (strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') < strpos($_SERVER['HTTP_ACCEPT'], 'text/html')))) {
                return true;
            }
        }

        return false;
    }

    public static function escape($string, $in_encoding = 'UTF-8', $out_encoding = 'UCS-2')
    {
        $return = '';
        if (function_exists('mb_get_info')) {
            for ($x = 0; $x < mb_strlen($string, $in_encoding); $x++) {
                $str = mb_substr($string, $x, 1, $in_encoding);
                if (strlen($str) > 1) { // 多字节字符
                    $return .= '%u' . strtoupper(bin2hex(mb_convert_encoding($str, $out_encoding, $in_encoding)));
                } else {
                    //$return .= '%' . strtoupper ( bin2hex ( $str ) );
                    /*if(preg_match('/^[a-zA-Z0-9]$/', $str)){
                        $return .= $str;
                    }else{
                        $return .= '%' . strtoupper ( bin2hex ( $str ) );
                    }*/
                    $return .= $str;
                }
            }
        }

        return $return;
    }

    public static function arrayToStr($items)
    {
        $str = [];

        foreach ($items as $name => $item) {
            $str[] = $name . '=' . $item;
        }

        return join("&", $str);
    }
}

?>