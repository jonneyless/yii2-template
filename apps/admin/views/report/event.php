<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $data \common\models\Organization */

$this->title = '活动统计报表';
$this->params['breadcrumbs'][] = ['label' => '报表管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<?php ActiveForm::begin([
    'id' => 'report',
    'action' => ['report/event'],
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
        <caption align="center">“爱拼才会赢”活动统计报表</caption>
        <tr>
            <td align="right" colspan="10" style="text-align: right!important;">日期：截止<?= date("m月d日", $time) ?></td>
        </tr>
        <tr>
            <th align="center" rowspan="2">分行</th>
            <th align="center" colspan="3">参与活动情况</th>
            <th align="center" colspan="3">商品销售情况</th>
            <th align="center" colspan="3">支付交易量</th>
        </tr>
        <tr>
            <th align="center">当日新增用户</th>
            <th align="center">比上日新增用户</th>
            <th align="center">累计参与用户</th>
            <th align="center">当日销售</th>
            <th align="center">比上日新增</th>
            <th align="center">累计销售</th>
            <th align="center">当日交易量</th>
            <th align="center">比上日新增交易量</th>
            <th align="center">累计交易量</th>
        </tr>
        <?php
        $total['report_user_today'] = 0;
        $total['report_user_for_yesterday'] = 0;
        $total['report_user_total'] = 0;
        $total['report_sales_today'] = 0;
        $total['report_sales_for_yesterday'] = 0;
        $total['report_sales_total'] = 0;
        $total['report_pay_today'] = 0;
        $total['report_pay_for_yesterday'] = 0;
        $total['report_pay_total'] = 0;
        ?>
        <?php foreach ($datas as $name => $data) { ?>
            <tr>
                <td align="center"><?= $name ?></td>
                <td align="center"><?= $data['report_user_today'] ?></td>
                <td align="center"><?= $data['report_user_today'] - $data['report_user_for_yesterday'] ?></td>
                <td align="center"><?= $data['report_user_total'] ?></td>
                <td align="center"><?= $data['report_sales_today'] ?></td>
                <td align="center"><?= $data['report_sales_today'] - $data['report_sales_for_yesterday'] ?></td>
                <td align="center"><?= $data['report_sales_total'] ?></td>
                <td align="center"><?= $data['report_pay_today'] ?></td>
                <td align="center"><?= $data['report_pay_today'] - $data['report_pay_for_yesterday'] ?></td>
                <td align="center"><?= $data['report_pay_total'] ?></td>
            </tr>
            <?php
            $total['report_user_today'] += $data['report_user_today'];
            $total['report_user_for_yesterday'] += $data['report_user_for_yesterday'];
            $total['report_user_total'] += $data['report_user_total'];
            $total['report_sales_today'] += $data['report_sales_today'];
            $total['report_sales_for_yesterday'] += $data['report_sales_for_yesterday'];
            $total['report_sales_total'] += $data['report_sales_total'];
            $total['report_pay_today'] += $data['report_pay_today'];
            $total['report_pay_for_yesterday'] += $data['report_pay_for_yesterday'];
            $total['report_pay_total'] += $data['report_pay_total'];
            ?>
        <?php } ?>
        <tr>
            <td align="center">合计</td>
            <td align="center"><?= $total['report_user_today'] ?></td>
            <td align="center"><?= $total['report_user_today'] - $total['report_user_for_yesterday'] ?></td>
            <td align="center"><?= $total['report_user_total'] ?></td>
            <td align="center"><?= $total['report_sales_today'] ?></td>
            <td align="center"><?= $total['report_sales_today'] - $total['report_sales_for_yesterday'] ?></td>
            <td align="center"><?= $total['report_sales_total'] ?></td>
            <td align="center"><?= $total['report_pay_today'] ?></td>
            <td align="center"><?= $total['report_pay_today'] - $total['report_pay_for_yesterday'] ?></td>
            <td align="center"><?= $total['report_pay_total'] ?></td>
        </tr>
        <tr>
            <td align="left" colspan="10" style="text-align: left!important;">
                说明：1、首笔成功支付交易作为“新增用户”的统计依据；2、客户身份转换，如游客变注册，后台只标注记录，不做新增统计处理；3、“商品销售情况”以拼团成功作为统计依据，交易统计到团长所在分行；4、“支付交易量”以实际付款成功为统计依据，“注册客户”统计到签约机构，“白名单客户”统计到二级分行上报机构，“游客”交易统计到“其他”。
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
