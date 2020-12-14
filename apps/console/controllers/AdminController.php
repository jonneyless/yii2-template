<?php

namespace console\controllers;

use admin\models\Admin;
use Yii;
use yii\console\Controller;

class AdminController extends Controller
{

    public function actionPassword($username, $password)
    {
        $user = Admin::findByUsername($username);
        $user->password = $password;
        $user->setPassword();
        $user->save();
    }
}