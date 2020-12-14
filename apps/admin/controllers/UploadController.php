<?php
namespace admin\controllers;

use Yii;
use yii\base\ErrorException;
use yii\helpers\Json;
use yii\web\Controller;
use libs\Utils;

/**
 * 配合 Uploadifive 使用的上传类
 *
 * @package home\controllers
 */
class UploadController extends Controller
{
    private $return = [
        'error' => 1,
        'msg' => '',
        'html' => '',
        'json' => '',
        'link' => '',
    ];

    public function actionImage()
    {
        $request = Yii::$app->request;
        $timestamp = $request->post('timestamp');
        $token = $request->post('token');
        $verifyToken = md5(Yii::$app->params['md5.authKey'] . $timestamp);
        $thumbWidth = $request->post('width', 0);
        $thumbHeight = $request->post('height', 0);

        try{
            if(empty($_FILES) || $token != $verifyToken){
                throw new ErrorException('参数错误！');
            }

            $imgInfo = pathinfo($_FILES['Filedata']['name']);

            if(!in_array(strtolower($imgInfo['extension']), ['jpg', 'gif', 'png', 'jpeg'])){
                throw new ErrorException('文件格式错误！' . $imgInfo['extension']);
            }

            $newImg = Utils::newBufferFile($imgInfo['extension']);
            $tempFile = $_FILES['Filedata']['tmp_name'];

            if (!Utils::saveFile(Utils::staticFolder($newImg), $tempFile)) {
                throw new ErrorException('保存失败！');
            }

            if($thumbWidth){
                $return['thumb'] = Utils::getImg($newImg, $thumbWidth, $thumbHeight);
            }

            $return['error'] = 0;
            $return['url'] = Utils::staticUrl($newImg);
            $return['path'] = $newImg;
            $return['name'] = str_replace('.' . $imgInfo['extension'], '', $imgInfo['basename']);

            $this->output($return);

        }catch (ErrorException $e){
            $this->output(['msg' => $e->getMessage()]);
        }
    }

    private function output($params)
    {
        $output = array_merge($this->return, $params);
        echo Json::encode($output);
        Yii::$app->end();
    }
}
