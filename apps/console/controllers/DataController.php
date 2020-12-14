<?php

namespace console\controllers;

use common\models\Goods;
use common\models\GoodsVirtual;
use common\models\Group;
use common\models\Order;
use common\models\Report;
use common\models\User;
use libs\ccbpay\Ccb;
use libs\Utils;
use Yii;
use yii\console\Controller;

/**
 * 数据修正
 *
 * @package mtwap\controllers
 * @author Jony <jonneyless@163.com>
 * @since 2016.11.21
 */
class DataController extends Controller
{

    public function actionUpdate($do = false)
    {
        $goods = Goods::find()->all();
        foreach ($goods as $good) {
            $sale = intval(Group::find()->where(['goods_id' => $good->id, 'status' => [Group::STATUS_ACTIVE, Group::STATUS_UNACTIVE, Group::STATUS_OVER]])->sum('delivery'));
            $sql = 'update {{%goods}} set sales = ' . $sale . ' where id = ' . $good->id;
            Yii::$app->db->createCommand($sql)->execute();
        }
    }

    public function actionCheck()
    {
        $mobile = '13771996281';

        Utils::dump((new Ccb())->querySaleInfo(['mobile' => $mobile, 'xyname' => 'pdhd']));
    }

}
