<?php
namespace api\controllers;

use Yii;
use yii\web\Controller;

/**
 * Site controller
 */
class SiteController extends Controller
{

    /**
     * Displays homepage.
     *
     * @return mixed
     */
    public function actionIndex()
    {
        return $this->render('index');
    }

    /**
     * 错误信息
     *
     * @return array
     */
    public function actionError()
    {
        return ['error' => Yii::$app->getErrorHandler()->exception->getMessage()];
    }
}
