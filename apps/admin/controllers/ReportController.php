<?php

namespace admin\controllers;

use common\models\Goods;
use common\models\GoodsGroup;
use common\models\Group;
use common\models\Organization;
use common\models\Report;
use moonland\phpexcel\Excel;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;

/**
 * ReportController implements the CRUD actions for Category model.
 */
class ReportController extends Controller
{
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

    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionStock($time = '')
    {
        if (!$time) {
            $time = date("Y-m-d", strtotime('-1 day'));
        }

        $model = Report::findByDate($time);

        if (!$model) {
            return $this->redirect(['index']);
        }

        $datas = unserialize($model->stock);

        return $this->render('stock', ['datas' => $datas, 'time' => strtotime($time)]);
    }

    public function actionEvent($time = '')
    {
        if (!$time) {
            $time = date("Y-m-d", strtotime('-1 day'));
        }

        $model = Report::findByDate($time);

        if (!$model) {
            return $this->redirect(['index']);
        }

        $datas = unserialize($model->event);

        return $this->render('event', ['datas' => $datas, 'time' => strtotime($time)]);
    }

    public function actionSales($time = '')
    {
        if (!$time) {
            $time = date("Y-m-d", strtotime('-1 day'));
        }

        $model = Report::findByDate($time);

        if (!$model) {
            return $this->redirect(['index']);
        }

        $datas = unserialize($model->sales);

        return $this->render('sales', ['datas' => $datas, 'time' => strtotime($time)]);
    }

    public function actionDetail()
    {
        if (Yii::$app->request->getIsPost()) {
            $begin = Yii::$app->request->post('begin');
            $end = Yii::$app->request->post('end');

            $query = Group::find()->where(['status' => Group::STATUS_OVER]);

            if ($begin && $end) {
                $begin = strtotime($begin);
                $end = strtotime($end) + 3600 * 24;
                $query->andWhere(['between', 'created_at', $begin, $end]);
            }

            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename="拼单活动清算明细.csv"');
            header('Cache-Control: max-age=0');

            $fp = fopen('php://output', 'a');

            $head = ['手机号码', '交易日期', '商品名称', '拼单人数', '补贴额'];
            foreach ($head as $i => $v) {
                $head[$i] = iconv('utf-8', 'gbk', $v);
            }
            fputcsv($fp, $head);

            $organization = Organization::find()->select('name')->indexBy('id')->column();
            $goods = Goods::find()->indexBy('id')->all();
            foreach ($goods as &$row) {
                $groups = GoodsGroup::find()->where(['goods_id' => $row->id])->indexBy('quantity')->all();

                $cost = [];
                foreach ($groups as $group) {
                    $cost[$group->quantity] = $group->cost - ($group->leader + $group->price * ($group->quantity - 1));
                }

                $item = [
                    'name' => $row->name,
                    'cost' => $cost,
                ];

                $row = $item;
            }

            $index = 0;
            $limit = 5000;

            $datas = $query->orderBy(['created_at' => SORT_ASC])->asArray()->all();

            /* @var $data \common\models\Group */
            foreach ($datas as $data) {
                $index++;

                if ($limit == $index) {
                    ob_flush();
                    flush();
                    $index = 0;
                }

                $group = [];
                $group[] = (string) "\t" . $data['phone'];
                $group[] = (string) "\t" . date("Y-m-d H:i:s", $data['created_at']);
                $group[] = (string) "\t" . iconv('utf-8', 'gbk', $goods[$data['goods_id']]['name']);
                $group[] = (string) "\t" . $data['quantity'];
                $group[] = $goods[$data['goods_id']]['cost'][$data['quantity']];

                fputcsv($fp, $group);
            }

            fclose($fp);
            die();
        }

        return $this->render('detail');
    }
}
