<?php

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $data \admin\models\User */

$this->title = $store->name;
$this->params['breadcrumbs'][] = ['label' => '推荐人报表', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $referee->username, 'url' => ['index', 'mobile' => $referee->mobile]];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="ibox">
    <div id="w0" class="ibox-content">
        <div class="table-responsive">
            <table class="table table-striped table-layout-fix">
                <tr>
                    <th>会员总数</th>
                    <td colspan="3"><?= $data['member'] ?> 人</td>
                </tr>
                <tr>
                    <th>总业绩</th>
                    <td><?= $data['performance'] ?> 元</td>
                    <th>所分佣金</th>
                    <td><?= $data['brokerage'] ?> 元</td>
                </tr>
                <tr>
                    <th>线下总业绩</th>
                    <td><?= $data['offline_performance'] ?> 元</td>
                    <th>线下总利润</th>
                    <td><?= $data['offline_performance'] - $data['offline_costing'] ?> 元</td>
                </tr>
                <tr>
                    <th>线上总业绩</th>
                    <td><?= $data['online_performance'] ?> 元</td>
                    <th>线上总利润</th>
                    <td><?= $data['online_performance'] - $data['online_costing'] ?> 元</td>
                </tr>
            </table>
        </div>
    </div>
</div>

