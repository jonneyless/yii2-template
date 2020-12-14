<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */

$this->title = '商品库存清单';
$this->params['breadcrumbs'][] = ['label' => '报表管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<?php ActiveForm::begin([
    'id' => 'report',
    'action' => ['report/stock'],
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
        <caption align="center">“爱拼才会赢”商品库存清单</caption>
        <tr>
            <td align="right" colspan="11" style="text-align: right!important;">日期：截止<?= date("m月d日", $time) ?></td>
        </tr>
        <tr>
            <th align="center" rowspan="2">商品信息</th>
            <th align="center" rowspan="2">拼单方式</th>
            <th align="center" rowspan="2">参团人数</th>
            <th align="center" rowspan="2">采购总价</th>
            <th align="center" rowspan="2">拼单价格</th>
            <th align="center" colspan="4">商品库存（件）</th>
            <th align="center" rowspan="2">已售商品（份）</th>
            <th align="center" rowspan="2">补贴金额</th>
        </tr>
        <tr>
            <th align="center">已售商品</th>
            <th align="center">销售中商品</th>
            <th align="center">剩余商品</th>
            <th align="center">总库存</th>
        </tr>
        <?php
        $total['report_sales'] = 0;
        $total['report_wait'] = 0;
        $total['report_stock'] = 0;
        $total['report_total'] = 0;
        $total['sales'] = 0;
        $total['subsidy'] = 0;
        ?>
        <?php foreach ($datas as $data) { ?>
            <?php foreach ($data->group as $index => $group) { ?>
                <tr>
                    <?php if ($index == 0) { ?>
                        <td align="center" rowspan="<?= count($data->group) ?>"><?= $data->name ?></td>
                    <?php } ?>
                    <td align="center"><?= $data->one_delivery == 1 ? '帮我拼' : '一起拼' ?></td>
                    <td align="center"><?= $group->quantity ?></td>
                    <td align="center"><?= $group->cost ?></td>
                    <td align="center"><?= $group->amount ?></td>
                    <td align="center"><?= $group->report_sales ?></td>
                    <td align="center"><?= $group->report_wait ?></td>
                    <td align="center"><?= $group->report_stock ?></td>
                    <td align="center"><?= $group->report_total ?></td>
                    <td align="center"><?= $group->report_sales / $group->delivery ?></td>
                    <td align="center"><?= sprintf('%.02f', ($group->cost - $group->amount) * $group->report_sales / $group->delivery) ?></td>
                </tr>
                <?php
                $total['report_sales'] += $group->report_sales;
                $total['report_wait'] += $group->report_wait;
                $total['report_stock'] += $group->report_stock;
                $total['report_total'] += $group->report_total;
                $total['sales'] += ($group->report_sales / $group->delivery);
                $total['subsidy'] += (($group->cost - $group->amount) * $group->report_sales / $group->delivery);
                ?>
            <?php } ?>
        <?php } ?>
        <tr>
            <td align="center">合计</td>
            <td align="center"></td>
            <td align="center"></td>
            <td align="center"></td>
            <td align="center"></td>
            <td align="center"><?= $total['report_sales'] ?></td>
            <td align="center"><?= $total['report_wait'] ?></td>
            <td align="center"><?= $total['report_stock'] ?></td>
            <td align="center"><?= $total['report_total'] ?></td>
            <td align="center"><?= $total['sales'] ?></td>
            <td align="center"><?= sprintf('%.02f', $total['subsidy']) ?></td>
        </tr>
        <tr>
            <td align="left" colspan="10" style="text-align: left!important;">
                说明：1、补贴金额根据商品实际销售情况统计；2、补贴金额=（采购价格-拼单价格）*已售商品。
            </td>
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
