<?php
namespace api\controllers;

use libs\pospal\Pospal;
use Yii;
use yii\web\Controller;

/**
 * Pospal controller
 */
class PospalController extends Controller
{

    public $enableCsrfValidation = false;

    /**
     * 银豹通知回调
     */
    public function actionNotify()
    {
        //Pospal::notify();
    }
}
