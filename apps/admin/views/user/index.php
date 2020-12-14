<?php

use ijony\admin\grid\GridView;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $data \admin\models\User */

$this->title = '会员管理';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="ibox">
    <?php echo $this->render('_search', ['model' => $searchModel]); ?>

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

            [
                'class' => 'ijony\admin\grid\ActionColumn',
                'headerOptions' => [
                    'class' => 'text-right',
                ],
                'template' => '{renew} {agent} {sync}',
                'buttons' => [
                    'renew' => function($url, $model, $key){
                        return Html::a('续费', $url, ['class' => 'btn-white btn btn-xs']);
                    },
                    'agent' => function($url, $model, $key){
                        return Html::a('代理', $url, ['class' => 'btn-white btn btn-xs']);
                    },
                    'sync' => function($url, $model, $key){
                        return Html::a('同步', $url, ['class' => 'btn-white btn btn-xs']);
                    }
                ],
            ],
        ],
    ]); ?>
</div>

