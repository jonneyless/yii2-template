<?php

use common\widgets\ActiveField;
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Config */

$this->title = '更新活动介绍';
$this->params['breadcrumbs'][] = ['label' => '活动介绍', 'url' => ['view', 'id' => $model->set_name]];
$this->params['breadcrumbs'][] = '更新';
?>
<div class="config-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="config-form">

        <?php $form = ActiveForm::begin([
            'fieldClass' => ActiveField::className(),
            'layout' => 'horizontal',
            'fieldConfig' => [
                'inline' => true,
                'horizontalCssClasses' => [
                    'label' => 'col-sm-4',
                    'offset' => 'col-sm-offset-4',
                    'wrapper' => 'col-sm-6',
                    'error' => '',
                    'hint' => '',
                ],
            ],
        ]); ?>

        <?= $form->field($model, 'set_value', [
            'horizontalCssClasses' => [
                'label' => '',
                'offset' => '',
                'wrapper' => 'col-sm-12',
                'error' => '',
                'hint' => '',
            ],
        ])->label(false)->editor() ?>

        <div class="form-group text-center">
            <?= Html::submitButton('更新', ['class' => 'btn btn-primary']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>

</div>
