<?php

use ijony\admin\grid\GridView;
use yii\helpers\Url;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $data \admin\models\Order */

$this->title = '发货管理';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="ibox">
    <?php echo $this->render('_search', ['model' => $searchModel, 'delivery' => true]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'layoutFix' => true,
        'columns' => [
            'order_id',
            'amount',
            'consignee',
            'phone',
            [
                'attribute' => 'created_at',
                'format' => [
                    'datetime',
                    'yyyy-MM-dd HH:mm:ss'
                ],
            ],

            [
                'class' => 'ijony\admin\grid\ActionColumn',
                'headerOptions' => [
                    'class' => 'text-right',
                ],
                'template' => '{view} {delivery}',
                'buttons' => [
                    'delivery' => function($url, $model, $key){
                        $url = Url::to(['order/delivery-done', 'id' => $key]);
                        return Html::a('发货', $url, ['class' => 'btn-white btn btn-xs']);
                    }
                ],
            ],
        ],
    ]); ?>
</div>

