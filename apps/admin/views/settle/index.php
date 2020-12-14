<?php

use ijony\admin\grid\GridView;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $data \admin\models\UserSettle */

$this->title = '奖励结算管理';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="ibox">
    <?php echo $this->render('_search', ['date' => $date, 'dates' => $dates]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'layoutFix' => true,
        'columns' => [
            'user.username',
            'user.mobile',
            'amount',
            'user.info.truename',
            'user.info.bankno',
            'user.info.bankname',

            [
                'class' => 'ijony\admin\grid\ActionColumn',
                'headerOptions' => [
                    'class' => 'text-right',
                ],
                'template' => '{done}',
                'buttons' => [
                    'done' => function($url, $model, $key){
                        if($model->status == 1){
                            return Html::tag('span', '已结', ['class' => 'btn-success btn btn-xs']);
                        }else{
                            return Html::a('结算', $url, ['class' => 'btn-danger btn btn-xs']);
                        }
                    },
                ],
            ],
        ],
    ]); ?>
</div>

