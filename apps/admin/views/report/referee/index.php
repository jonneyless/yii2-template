<?php

use ijony\admin\grid\GridView;
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $data \admin\models\User */

$this->title = '推荐人所推荐店铺';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="ibox">
    <div class="ibox-content m-b-sm border-bottom">
        <?php $form = ActiveForm::begin([
            'action' => ['index'],
            'method' => 'get',
        ]); ?>

        <div class="row">

            <div class="col-sm-2">
                <div class="form-group">
                    <label class="control-label" for="mobile">推荐人手机</label>
                    <input id="mobile" class="form-control" name="mobile" type="text" />
                </div>
            </div>

            <div class="col-sm-1">
                <div class="form-group">
                    <label>&nbsp;</label>
                    <?= Html::submitButton('搜索', ['class' => 'btn btn-primary form-control']) ?>
                </div>
            </div>

        </div>

        <?php ActiveForm::end(); ?>
    </div>

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
                'class' => 'ijony\admin\grid\ActionColumn',
                'headerOptions' => [
                    'class' => 'text-right',
                ],
                'template' => '{view}',
            ],
        ],
    ]); ?>
</div>

