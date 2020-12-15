<?php

namespace admin\controllers;

use ijony\helpers\Utils;
use Yii;
use yii\filters\AccessControl;
use yii\helpers\Json;

/**
 * Ajax controller
 */
class AjaxController extends BaseController
{

    private $return = [
        'error' => 1,
    ];

    /**
     * @inheritdoc
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
        ];
    }

    /**
     * 生成 Slug
     *
     * @return string
     */
    public function actionGetSlug()
    {
        $value = Yii::$app->request->post('value');

        if ($value) {
            $value = Utils::pinyin()->permalink($value);
        }

        return $this->output($value);
    }

    /**
     * 输出
     *
     * @param $params
     *
     * @return string
     */
    private function output($params)
    {
        if (is_array($params)) {
            $return = array_merge($this->return, $params);
        } else {
            $return = $this->return;
            $return['result'] = $params;
        }

        return Json::encode($return);
    }

    /**
     * 错误信息输出
     *
     * @param $params
     *
     * @return string
     */
    private function error($params)
    {
        if (is_array($params)) {
            $return = array_merge($this->return, $params);
        } else {
            $return = $this->return;
            $return['result'] = $params;
            $return['msg'] = '操作失败！';
        }
        $return['error'] = 1;

        return Json::encode($return);
    }

    /**
     * 成功信息输出
     *
     * @param $params
     *
     * @return string
     */
    private function success($params)
    {
        if (is_array($params)) {
            $return = array_merge($this->return, $params);
        } else {
            $return = $this->return;
            $return['result'] = $params;
            $return['msg'] = '操作成功！';
        }
        $return['error'] = 0;

        return Json::encode($return);
    }
}
