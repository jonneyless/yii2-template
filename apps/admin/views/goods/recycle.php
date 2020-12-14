<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '回收站';
$this->params['breadcrumbs'][] = ['label' => '商品管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="goods-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('商品管理', ['index'], ['class' => 'btn btn-success']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            'id',
            'name',
            'stock',
            'sales',

            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{view} {virtual} {outlet}',
                'buttons' => [
                    'view' => function ($url, $model, $key) {
                        /* @var $model \common\models\Goods */
                        return Html::a('查看', ['goods/view', 'id' => $model->id]);
                    },
                    'virtual' => function ($url, $model, $key) {
                        if ($model->is_virtual == 1) {
                            return "| " . Html::a('虚拟卡', ['goods/virtual', 'id' => $model->id]);
                        }
                    },
                    'outlet' => function ($url, $model, $key) {
                        if ($model->is_virtual == 1) {
                            return "| " . Html::a('门店', ['goods/outlet', 'id' => $model->id]);
                        }
                    },
                ],
            ],
        ],
    ]); ?>
</div>
