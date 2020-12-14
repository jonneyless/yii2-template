<?php

namespace console\controllers;

use common\models\Group;
use common\models\Report;
use libs\Utils;
use Yii;
use common\models\Order;
use yii\console\Controller;

/**
 * 报表结算
 *
 * @package mtwap\controllers
 * @author Jony <jonneyless@163.com>
 * @since 2016.11.21
 */
class ReportController extends Controller
{

    public function actionGen($time = '')
    {
        if (!$time) {
            $time = date("Ymd");
        }

        $id = $time;

        $model = Report::find()->where(['id' => $id])->one();

        if (!$model) {
            $model = new Report();
            $model->id = $id;
        }

        $model->genStock();
        $model->save();
    }
}