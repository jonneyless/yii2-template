<?php

namespace admin\controllers;

use libs\Utils;
use Yii;
use yii\helpers\Json;
use yii\web\Controller;

/**
 * 这对编辑器的文件上传管理
 *
 * 因为编辑器不太好做 Csrf，所以独立出来，取消 Csrf
 *
 * @package home\controllers
 */
class FileController extends Controller
{
    /**
     * @var bool
     */
    public $enableCsrfValidation = false;

    public $extensions = [
        'image' => ['gif', 'jpg', 'jpeg', 'png', 'bmp'],
        'flash' => ['swf', 'flv'],
        'media' => ['swf', 'flv', 'mp3', 'wav', 'wma', 'wmv', 'mid', 'avi', 'mpg', 'asf', 'rm', 'rmvb'],
        'file' => ['doc', 'docx', 'xls', 'xlsx', 'ppt', 'htm', 'html', 'txt', 'zip', 'rar', 'gz', 'bz2'],
    ];

    public $order = 'name';

    /**
     * 预设 Ajax 回调
     *
     * @var array
     */
    private $return = [
        'error' => 1,
        'msg' => '',
    ];

    public function actionUpload()
    {
        try {
            $folder = Yii::$app->request->get('dir', 'image');

            if (!isset($this->extensions[$folder])) {
                throw new ErrorException('目录错误！');
            }

            $extensions = $this->extensions[$folder];

            if (empty($_FILES)) {
                throw new ErrorException('参数错误！');
            }

            $fileInfo = pathinfo($_FILES['imgFile']['name']);
            $newFile = Utils::newFile($fileInfo['extension'], $folder);
            if (!in_array(strtolower($fileInfo['extension']), $extensions)) {
                throw new ErrorException('文件格式错误！' . $fileInfo['extension']);
            }

            $tempFile = $_FILES['imgFile']['tmp_name'];

            if (!Utils::saveFile(Utils::staticFolder($newFile), $tempFile)) {
                throw new ErrorException('上传失败！');
            }

            $this->output(['error' => 0, 'url' => Utils::staticUrl($newFile)]);
        } catch (ErrorException $e) {
            $this->output(['message' => $e->getMessage()]);
        }
    }

    public function actionFilemanager()
    {
        $root_path = rtrim(Yii::getAlias('@upload/'), '\\/') . '/';
        $root_url = Utils::staticUrl('upload/');

        $folder = Yii::$app->request->get('dir');

        if (!$folder) {
            if (!isset($this->extensions[$dir_name])) {
                echo '目录错误！';
                Yii::$app->end();
            }

            $root_path .= $folder . "/";
            $root_url .= $folder . "/";
        }

        $extensions = $this->extensions[$folder];

        Utils::mkdir($root_path);

        //根据path参数，设置各路径和URL
        if (empty($_GET['path'])) {
            $current_path = realpath($root_path) . '/';
            $current_url = $root_url;
            $current_dir_path = '';
            $moveup_dir_path = '';
        } else {
            $current_path = realpath($root_path) . '/' . $_GET['path'];
            $current_url = $root_url . $_GET['path'];
            $current_dir_path = $_GET['path'];
            $moveup_dir_path = preg_replace('/(.*?)[^\/]+\/$/', '$1', $current_dir_path);
        }
        //echo realpath($root_path);
        //排序形式，name or size or type
        $this->order = empty($_GET['order']) ? 'name' : strtolower($_GET['order']);

        //不允许使用..移动到上一级目录
        if (preg_match('/\.\./', $current_path)) {
            echo 'Access is not allowed.';
            exit;
        }
        //最后一个字符不是/
        if (!preg_match('/\/$/', $current_path)) {
            echo 'Parameter is not valid.';
            exit;
        }
        //目录不存在或不是目录
        if (!file_exists($current_path) || !is_dir($current_path)) {
            echo 'Directory does not exist.';
            exit;
        }

        //遍历目录取得文件信息
        $file_list = [];
        if ($handle = opendir($current_path)) {
            $i = 0;
            while (false !== ($filename = readdir($handle))) {
                if ($filename{0} == '.') {
                    continue;
                }
                $file = $current_path . $filename;
                if (is_dir($file)) {
                    $file_list[$i]['is_dir'] = true; //是否文件夹
                    $file_list[$i]['has_file'] = (count(scandir($file)) > 2); //文件夹是否包含文件
                    $file_list[$i]['filesize'] = 0; //文件大小
                    $file_list[$i]['is_photo'] = false; //是否图片
                    $file_list[$i]['filetype'] = ''; //文件类别，用扩展名判断
                } else {
                    $file_list[$i]['is_dir'] = false;
                    $file_list[$i]['has_file'] = false;
                    $file_list[$i]['filesize'] = filesize($file);
                    $file_list[$i]['dir_path'] = '';
                    $file_ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                    $file_list[$i]['is_photo'] = in_array($file_ext, $extensions);
                    $file_list[$i]['filetype'] = $file_ext;
                }
                $file_list[$i]['filename'] = $filename; //文件名，包含扩展名
                $file_list[$i]['datetime'] = date('Y-m-d H:i:s', filemtime($file)); //文件最后修改时间
                $i++;
            }
            closedir($handle);
        }

        usort($file_list, [$this, 'sort']);

        $result = [];
        //相对于根目录的上一级目录
        $result['moveup_dir_path'] = $moveup_dir_path;
        //相对于根目录的当前目录
        $result['current_dir_path'] = $current_dir_path;
        //当前目录的URL
        $result['current_url'] = $current_url;
        //文件数
        $result['total_count'] = count($file_list);
        //文件列表数组
        $result['file_list'] = $file_list;

        $this->output($result);
    }

    protected function sort($a, $b)
    {
        $order = $this->order;
        if ($a['is_dir'] && !$b['is_dir']) {
            return -1;
        } else if (!$a['is_dir'] && $b['is_dir']) {
            return 1;
        } else {
            if ($order == 'size') {
                if ($a['filesize'] > $b['filesize']) {
                    return 1;
                } else if ($a['filesize'] < $b['filesize']) {
                    return -1;
                } else {
                    return 0;
                }
            } else if ($order == 'type') {
                return strcmp($a['filetype'], $b['filetype']);
            } else {
                return strcmp($a['filename'], $b['filename']);
            }
        }
    }

    private function output($params)
    {
        $output = array_merge($this->return, $params);
        echo Json::encode($output);
        Yii::$app->end();
    }
}