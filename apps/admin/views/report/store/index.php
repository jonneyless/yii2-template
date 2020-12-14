<?php

use ijony\admin\grid\GridView;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $data \admin\models\User */

$this->title = '店铺报表';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="ibox">
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'layoutFix' => true,
        'columns' => [
            'store_id',
            [
                'attribute' => 'referee',
                'value' => function($data){
                    return $data->getReferee('username');
                }
            ],
            'name',

            [
                'class' => 'ijony\admin\grid\ActionColumn',
                'headerOptions' => [
                    'class' => 'text-right',
                ],
                'template' => '{view} {member} {statement}',
                'buttons' => [
                    'member' => function($url, $model, $key){
                        return Html::a('会员', $url, ['class' => 'btn-white btn btn-xs']);
                    },
                    'statement' => function($url, $model, $key){
                        return Html::a('月结', $url, ['class' => 'btn-white btn btn-xs']);
                    },
                ],
            ],
        ],
    ]); ?>
</div>

