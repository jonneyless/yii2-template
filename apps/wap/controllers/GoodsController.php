<?php
namespace wap\controllers;

use wap\models\Goods;
use Yii;

/**
 * Goods controller
 */
class GoodsController extends Controller
{

    public function actionView($id)
    {
        $model = Goods::findOne($id);

        if(!$model){
            return $this->message('商品不存在！');
        }

        return $this->render('view', [
            'model' => $model
        ]);
    }
}
