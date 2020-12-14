<?php

use ijony\admin\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $data \admin\models\User */

$this->title = '店铺会员';
$this->params['breadcrumbs'][] = ['label' => '店铺报表', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $store->name, 'url' => ['view']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="ibox">
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'layoutFix' => true,
        'columns' => [
            'user_id',
            [
                'attribute' => 'referee',
                'value' => function($data){
                    return $data->showReferee();
                }
            ],
            'username',
            'mobile',
            [
                'attribute' => 'expire_at',
                'value' => function($data){
                    return $data->showExpire();
                }
            ],
        ],
    ]); ?>
</div>

