<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '商品管理';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="goods-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('添加商品', ['create'], ['class' => 'btn btn-success']) ?>
        <?= Html::a('回收站', ['recycle'], ['class' => 'btn btn-success']) ?>
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
                'template' => '{view} {update} {delete}',
                'buttons' => [
                    'view' => function ($url, $model, $key) {
                        /* @var $model \common\models\Goods */
                        return Html::a('查看', ['goods/view', 'id' => $model->id]);
                    },
                    'update' => function ($url, $model, $key) {
                        return "| " . Html::a('编辑', $url);
                    },
                    'delete' => function ($url, $model, $key) {
                        $options = [
                            'title' => '删除',
                            'aria-label' => '删除',
                            'data-confirm' => '确定要放入回收站嘛？',
                            'data-method' => 'post',
                            'data-pjax' => '0',
                        ];

                        return "| " . Html::a('删除', $url, $options);
                    },
                ],
            ],
        ],
    ]); ?>
</div>
