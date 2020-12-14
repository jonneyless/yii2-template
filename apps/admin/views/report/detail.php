<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $data \common\models\Organization */

$this->title = '活动结算明细';
$this->params['breadcrumbs'][] = ['label' => '报表管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<?php ActiveForm::begin([
    'id' => 'report',
    'action' => ['report/detail'],
    'method' => 'post',
]); ?>

<div class="report-search">
    <div class="form-group">
        选择时间：
        <?php echo Html::tag(
            'div',
            Html::textInput('begin', '', ['id' => 'begin', 'class' => 'form-control form-control-inline']) .
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
                        'onclick' => "laydate({elem: '#begin', format: 'YYYY-MM-DD', istoday: false});",
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
        到
        <?php echo Html::tag(
            'div',
            Html::textInput('end', '', ['id' => 'end', 'class' => 'form-control form-control-inline']) .
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
                        'onclick' => "laydate({elem: '#end', format: 'YYYY-MM-DD', istoday: false});",
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

        <input type="submit" class="btn btn-primary" value="导出"/>
    </div>

    <div class="form-group">
        <a class="btn btn-primary" href="<?= \yii\helpers\Url::to(['report/stock']) ?>">商品库存清单</a>
        <a class="btn btn-primary" href="<?= \yii\helpers\Url::to(['report/event']) ?>">活动统计报表</a>
        <a class="btn btn-primary" href="<?= \yii\helpers\Url::to(['report/sales']) ?>">活动商品销售明细表</a>
        <a class="btn btn-primary" href="<?= \yii\helpers\Url::to(['report/detail']) ?>">活动结算明细</a>
    </div>
</div>

<?php ActiveForm::end(); ?>
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
