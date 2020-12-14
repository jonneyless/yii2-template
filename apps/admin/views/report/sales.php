<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $data \common\models\Organization */

$this->title = '活动商品销售明细表';
$this->params['breadcrumbs'][] = ['label' => '报表管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<?php ActiveForm::begin([
    'id' => 'report',
    'action' => ['report/sales'],
    'method' => 'get',
]); ?>

<div class="report-search">
    <div class="form-group">
        选择时间：
        <?php echo Html::tag(
            'div',
            Html::textInput('time', date("Y-m-d", $time), ['id' => 'time', 'class' => 'form-control form-control-inline']) .
            Html::tag(
                'span',
                Html::button(
                    Html::tag(
                        'span',
                        '',
                        [
                            'class' => 'glyphicon glyphicon-calendar',
                            'aria-hidden' => 'true',
                        ]
                    ),
                    [
                        'class' => 'btn btn-default',
                        'onclick' => "laydate({elem: '#time', format: 'YYYY-MM-DD', istoday: false});",
                    ]
                ),
                [
                    'class' => 'input-group-btn',
                ]
            ),
            [
                'class' => 'input-group input-group-inline',
            ]
        ); ?>

        <input type="submit" class="btn btn-primary" value="筛选"/>
    </div>

    <div class="form-group">
        <a class="btn btn-primary" href="<?= \yii\helpers\Url::to(['report/stock']) ?>">商品库存清单</a>
        <a class="btn btn-primary" href="<?= \yii\helpers\Url::to(['report/event']) ?>">活动统计报表</a>
        <a class="btn btn-primary" href="<?= \yii\helpers\Url::to(['report/sales']) ?>">活动商品销售明细表</a>
        <a class="btn btn-primary" href="<?= \yii\helpers\Url::to(['report/detail']) ?>">活动结算明细</a>
    </div>
</div>

<?php ActiveForm::end(); ?>

<div class="site-index">
    <table class="table table-bordered table-report">
        <caption align="center">“爱拼才会赢”活动商品销售明细表</caption>
        <tr>
            <td align="right" colspan="10" style="text-align: right!important;">日期：截止<?= date("m月d日", $time) ?></td>
        </tr>
        <tr>
            <th align="center" rowspan="2">分行</th>
            <?php $data = current($datas); ?>
            <?php foreach ($data as $name => $group) { ?>
                <th align="center" colspan="<?= count($group) ?>"><?= $name ?></th>
            <?php } ?>
            <th align="center" rowspan="2">累计</th>
        </tr>
        <tr>
            <?php foreach ($data as $name => $group) { ?>
                <?php foreach ($group as $quantity => $sales) { ?>
                    <th align="center"><?= $quantity ?>人拼</th>
                <?php } ?>
            <?php } ?>
        </tr>
        <?php $column = []; ?>
        <?php foreach ($datas as $name => $data) { ?>
            <?php $row = 0; ?>
            <tr>
                <td align="center"><?= $name ?></td>
                <?php foreach ($data as $groupKey => $group) { ?>
                    <?php foreach ($group as $salesKey => $sales) { ?>
                        <td align="center"><?= $sales ?></td>
                        <?php $row += $sales ?>
                        <?php
                        if (!isset($column[$groupKey][$salesKey])) {
                            $column[$groupKey][$salesKey] = 0;
                        }
                        ?>
                        <?php $column[$groupKey][$salesKey] += $sales ?>
                    <?php } ?>
                <?php } ?>
                <td><?= $row ?></td>
            </tr>
        <?php } ?>
        <?php $row = 0; ?>
        <tr>
            <td align="center">合计</td>
            <?php foreach ($column as $group) { ?>
                <?php foreach ($group as $sales) { ?>
                    <td align="center"><?= $sales ?></td>
                    <?php $row += $sales ?>
                <?php } ?>
            <?php } ?>
            <td><?= $row ?></td>
        </tr>
    </table>
</div>
<?php
$js = <<<JS
        
laydate({
    elem: '#time',
    event: 'focus',
    format: 'YYYY-MM-DD',
    istime: false,
    istoday: false
});

$('a[report]').click(function(){
    $('#route').val($(this).attr('report'));
    $('#report').submit();
    return false;
})

JS;

$this->registerJsFile('web/js/laydate/laydate.js', ['depends' => 'admin\assets\AppAsset']);

$this->registerJs($js);
?>
