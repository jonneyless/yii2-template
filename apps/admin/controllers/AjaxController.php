<?php

namespace admin\controllers;

use admin\models\Store;
use ijony\helpers\Image;
use libs\Grab;
use Yii;
use yii\helpers\Html;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\Json;
use yii\helpers\Url;

/**
 * Ajax Controller
 */
class AjaxController extends \yii\web\Controller
{
    public $enableCsrfValidation = false;

    private $return = [
        'error' => 1,
        'msg' => '系统错误！',
    ];

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
//            'access' => [
//                'class' => AccessControl::className(),
//                'rules' => [
//                    [
//                        'allow' => true,
//                        'roles' => ['@'],
//                    ],
//                ],
//            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'post' => ['select'],
                ],
            ],
        ];
    }

    /**
     * 级联下拉表单
     *
     * @param $model
     * @param $input
     * @param $exclude
     *
     * @return string
     */
    public function actionSelect($model, $input, $exclude)
    {
        $select = '';
        $parent_id = Yii::$app->request->post('parent_id');

        if($parent_id){
            $datas = $model::getSelectData($parent_id, $exclude);
            if($datas){
                $params = [
                    'class' => 'form-control form-control-inline',
                    'ajax-select' => Url::to(['ajax/select', 'model' => $model, 'input' => $input, 'exclude' => $exclude]),
                ];

                if(!$parent_id){
                    $params['prompt'] = '请选择';
                }

                $select = Html::dropDownList($input, '', $datas, $params);
            }
        }

        return $this->success(['html' => $select]);
    }

    public function actionFilterStore()
    {
        $keyword = Yii::$app->request->post('keyword');

        return $this->success(['html' => Store::getOptions(['like', 'name', $keyword])]);
    }

    public function actionOptions($model, $field)
    {
        $value = Yii::$app->request->post('value');

        return $this->success(['html' => $model::getOptions([$field => $value])]);
    }

    public function actionGrab()
    {
        $url = Yii::$app->request->post('url');

        $data = array();
        if($url){
            Grab::run()->init($url);

            $data['name'] = Grab::api()->getTitle();

            $preview = Grab::api()->getIcon();

            $data['preview'] = Grab::api()->grabImage($preview);
            $data['preview_static'] = \ijony\helpers\Url::getStatic($data['preview']);
            $data['barcode'] = Grab::api()->getNumber();

            $galleries = Grab::api()->getImages();

            $images = [];

            if($galleries){
                foreach($galleries as $gallery){
                    $gallery = Grab::api()->grabImage($gallery);
                    $images[] = [
                        'path' => $gallery,
                        'thumb' => Image::getImg($gallery, 170, 170),
                        'name' => '',
                    ];
                }
            }

            $data['images'] = $images;

            $data['desc'] = Grab::api()->getDesc();
            $data['platform'] = Grab::run()->platform;
            $data['platform_name'] = Grab::run()->name;
        }

        return $this->success(['json' => $data]);
    }

    private function error($params)
    {
        $return = array_merge($this->return, $params);
        $return['error'] = 1;
        return Json::encode($return);
    }

    private function success($params)
    {
        $return = array_merge($this->return, $params);
        $return['error'] = 0;
        return Json::encode($return);
    }
}
