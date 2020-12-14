<?php

namespace wap\controllers;

use common\models\Area;
use libs\SMS;
use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\web\Controller;
use yii\bootstrap\Html;
use yii\filters\VerbFilter;
use yii\helpers\Url;

/**
 * Ajax 接口
 *
 * @package wap\controllers
 * @author Jony <jonneyless@163.com>
 * @since 2016.11.21
 */
class AjaxController extends Controller
{

    private $return = [
        'error' => 1,
        'msg' => '',
    ];

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    '*' => ['post'],
                ],
            ],
        ];
    }

    /**
     * 级联地区接口
     */
    public function actionSelectArea()
    {
        $parentId = Yii::$app->request->post('parentId');
        $inputName = Yii::$app->request->post('inputName');

        if (!$parentId) {
            $this->output(['error' => 0]);
        }

        $datas = Area::getSelectData($parentId);

        if ($datas) {
            $this->output(['html' => Html::dropDownList($inputName, '', $datas, ['class' => 'form-control form-control-inline', 'ajax-select' => Url::to(['ajax/select-area'])])]);
        } else {
            $this->output(['error' => 0]);
        }
    }

    /**
     * 发送验证码
     */
    public function actionVcode()
    {
        $mobile = Yii::$app->request->post('mobile');

        SMS::vcode($mobile, true);
    }

    protected function output($params)
    {
        $output = ArrayHelper::merge($this->return, $params);
        echo Json::encode($output);
        Yii::$app->end();
    }
}
