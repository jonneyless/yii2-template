<?php

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $data \admin\models\User */

$this->title = $store->name;
$this->params['breadcrumbs'][] = ['label' => '店铺报表', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="ibox">
    <div id="w0" class="ibox-content">
        <div class="table-responsive">
            <table class="table table-striped table-layout-fix">
                <tr>
                    <th>直接推荐会员数</th>
                    <td><?= $data['direct'] ?> 人</td>
                    <th>间接推荐会员数</th>
                    <td colspan="3"><?= $data['indirect'] ?> 人</td>
                </tr>
                <tr>
                    <th>库存商品总成本</th>
                    <td><?= $data['stockCosting'] ?> 元</td>
                    <th>总利润率</th>
                    <td><?= round(($data['stockPrice'] - $data['stockCosting']) / $data['stockPrice'], 4) * 100 ?>%</td>
                    <th>总利润</th>
                    <td><?= $data['stockPrice'] - $data['stockCosting'] ?> 元</td>
                </tr>
                <tr>
                    <th>总销售额</th>
                    <td colspan="5"><?= $data['saleIncome'] ?> 元</td>
                </tr>
                <tr>
                    <th>总销售成本</th>
                    <td><?= $data['saleCosting'] ?> 元</td>
                    <th>利润率</th>
                    <td><?= round(($data['saleIncome'] - $data['saleCosting']) / $data['saleIncome'], 4) * 100 ?>%</td>
                    <th>利润</th>
                    <td><?= $data['saleIncome'] - $data['saleCosting'] ?> 元</td>
                </tr>
                <tr>
                    <th>线下当日收入</th>
                    <td><?= floatval($data['offlineTodayIncome']) ?> 元</td>
                    <th>线下当月收入</th>
                    <td colspan="3"><?= $data['offlineMonthIncome'] ?> 元</td>
                </tr>
            </table>
        </div>
    </div>
</div>

