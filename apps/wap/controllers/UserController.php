<?php
namespace wap\controllers;

use ijony\helpers\Url;
use Yii;

/**
 * User controller
 */
class UserController extends Controller
{

    public function actionIndex()
    {
        return $this->message('功能开发中！', ['site/index']);
    }

    public function actionShare($id)
    {
        $promotionUrl = Url::getFull(Url::to(['system/share', 'id' => $id]));

        /* @var $qrcode \Da\QrCode\QrCode */
        $qrcode = Yii::$app->qrcode;
        $qrcode = $qrcode->useForegroundColor(236, 90, 89);

        return $this->render('share', [
            'qrcode' => $qrcode->setMargin(30)->setText($promotionUrl)->writeDataUri(),
        ]);
    }
}
