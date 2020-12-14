<?php

namespace console\controllers;

use common\models\Admin;
use yii\console\Controller;

/**
 * 管理密码重置
 *
 * @package mtwap\controllers
 * @author Jony <jonneyless@163.com>
 * @since 2016.11.21
 */
class AdminController extends Controller
{

    public function actionReset($password = '123456')
    {
        $model = Admin::findOne(1);
        $model->setPassword($password);
        $model->save();
    }
}