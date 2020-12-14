<?php
namespace admin\controllers;

use ijony\helpers\File;
use ijony\helpers\Folder;
use ijony\helpers\Image;
use ijony\helpers\Url;
use Yii;
use yii\base\ErrorException;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\Json;
use yii\web\Controller as BaseController;

/**
 * 配合 Uploadifive 使用的上传类
 */
class UploadController extends BaseController
{
    private $return = [
        'error' => 1,
        'msg' => '',
        'html' => '',
        'json' => '',
        'link' => ''
    ];
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'image' => ['post'],
                ],
            ],
        ];
    }

    public function actionImage()
    {
        $request = Yii::$app->request;
        $timestamp = $request->post('timestamp');
        $token = $request->post('token');
        $verifyToken = md5('laijiusheng_' . $timestamp);
        $thumbWidth = $request->post('width', 0);
        $thumbHeight = $request->post('height', 0);

        try{
            if(empty($_FILES) || $token != $verifyToken){
                throw new ErrorException('参数错误！');
            }

            $imgInfo = pathinfo($_FILES['Filedata']['name']);
            $newImg = File::newBufferFile($imgInfo['extension'], Yii::$app->user->id);
            if(!in_array(strtolower($imgInfo['extension']), ['jpg', 'gif', 'png', 'jpeg'])){
                throw new ErrorException('文件格式错误！' . $imgInfo['extension']);
            }

            $tempFile = $_FILES['Filedata']['tmp_name'];

            if(!File::saveFile(Folder::getStatic($newImg), $tempFile)){
                throw new ErrorException('保存失败！');
            }

            if($thumbWidth){
                $return['thumb'] = Image::getImg($newImg, $thumbWidth, $thumbHeight);
            }

            $return['error'] = 0;
            $return['url'] = Url::getStatic($newImg);
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
