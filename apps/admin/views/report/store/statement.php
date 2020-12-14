<?php

use ijony\admin\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $data \admin\models\User */

$this->title = '店铺月结对账单';
$this->params['breadcrumbs'][] = ['label' => '店铺报表', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $store->name, 'url' => ['view']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="ibox">
    <div class="ibox-title"><h4>本月数据</h4></div>

    <div class="ibox-content m-b-sm border-bottom">
        <div class="table-responsive">
            <table class="table table-striped table-layout-fix">
                <thead>
                <tr>
                    <th> 线下利润</th>
                    <th>平台分红</th>
                    <th>线上利润</th>
                    <th>店铺应收</th>
                    <th>顺逆差</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td><?= $currentMonth['offline'] ?></td>
                    <td><?= $currentMonth['offline'] * 0.1 ?></td>
                    <td><?= $currentMonth['online'] ?></td>
                    <td><?= $currentMonth['online'] * 0.9 ?></td>
                    <td><?= $currentMonth['offline'] * 0.1 - $currentMonth['online'] * 0.9 ?></td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>

    <div class="ibox-title"><h4>历史数据</h4></div>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'layoutFix' => true,
        'columns' => [
            'date',
            'offline',
            [
                'attribute' => '平台分红',
                'value' => function($data){
                    return $data->offline * 0.1;
                }
            ],
            'online',
            [
                'attribute' => '店铺应收',
                'value' => function($data){
                    return $data->online * 0.9;
                }
            ],
            [
                'attribute' => '顺逆差',
                'value' => function($data){
                    return $data->offline * 0.1 - $data->online * 0.9;
                }
            ]
        ],
    ]); ?>
</div>

