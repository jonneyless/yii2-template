<?php

use yii\helpers\Html;
use ijony\admin\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model admin\models\AdminRole */
/* @var $form \ijony\admin\widgets\ActiveForm */
?>

<?php $form = ActiveForm::begin([
    'layout' => 'horizontal',
    'options' => [
        'class' => 'tabs-container',
    ],
    'fieldConfig' => [
        'inline' => true,
        'template' => "{label}\n{beginWrapper}\n{input}\n{hint}\n{error}\n{endWrapper}",
        'horizontalCssClasses' => [
            'label' => 'col-sm-2',
            'offset' => 'col-sm-offset-2',
            'wrapper' => 'col-sm-10',
            'error' => '',
            'hint' => '',
        ],
    ],
]); ?>

    <ul class="nav nav-tabs">
        <li class="active"><a data-toggle="tab" href="#tab-base">基本信息</a></li>
        <li class=""><a data-toggle="tab" href="#tab-auth">权限配置</a></li>
    </ul>

    <div class="tab-content">
        <div id="tab-base" class="tab-pane active">
            <div class="panel-body">

                <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
                <?= $form->field($model, 'description')->textarea() ?>
                <?= $form->field($model, 'status')->radioList($model->getStatusSelectData()) ?>

                <div class="form-group">
                    <div class="col-sm-4 col-sm-offset-2">
                        <?= Html::resetButton('重置', ['class' => 'btn btn-white']) ?>
                        <?= Html::submitButton('保存', ['class' => 'btn btn-primary']) ?>
                    </div>
                </div>

            </div>
        </div>

        <div id="tab-auth" class="tab-pane">
            <div class="panel-body">

                <table class="table table-bordered table-responsive table-auth">
                    <thead>
                    <tr>
                        <th width="150">一级权限</th>
                        <th>二级权限</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php $inputName = Html::getInputName($model, 'auth'); ?>
                    <input type="hidden" name="<?= $inputName ?>" value=""/>
                    <?php $auths = $model::getAllAuth(); ?>
                    <?php foreach ($auths as $auth) { ?>
                        <?php $checkParant = in_array($auth->key, $model->authArr); ?>
                        <tr>
                            <th>

                                <div class="checkbox checkbox-inline">
                                    <input id="auth-<?= $auth->key ?>" type="checkbox" name="<?= $inputName ?>[]"<?php if ($checkParant) { ?> checked<?php } ?> value="<?= $auth->key ?>"/>
                                    <label for="auth-<?= $auth->key ?>"><strong><?= $auth->name ?></strong></label>
                                </div>
                            </th>
                            <td>
                                <?php foreach ($auth->childAuth as $child) { ?>
                                    <?php $checkChild = $checkParant ? $checkParant : in_array($child->key, $model->authArr); ?>
                                    <div class="checkbox checkbox-inline">
                                        <input id="auth-<?= $child->key ?>" type="checkbox" name="<?= $inputName ?>[]"<?php if ($checkChild) { ?> checked<?php } ?> value="<?= $child->key ?>"/>
                                        <label for="auth-<?= $child->key ?>"><?= $child->name ?></label>
                                    </div>
                                <?php } ?>
                            </td>
                        </tr>
                    <?php } ?>
                    </tbody>
                </table>

                <div class="form-group">
                    <div class="col-sm-12 text-center">
                        <?= Html::resetButton('重置', ['class' => 'btn btn-white']) ?>
                        <?= Html::submitButton('保存', ['class' => 'btn btn-primary']) ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

<?php ActiveForm::end(); ?>

<?php

$js = <<<JS

    $('.table-auth th input:checked').each(function(index, item){
        $(item).closest('tr').find('input').prop('checked', $(this).prop('checked'));
        $(item).closest('tr').find('td input').prop('disabled', $(this).prop('checked'));
    });

    $('.table-auth th input').click(function(){
        $(this).closest('tr').find('input').prop('checked', $(this).prop('checked'));
        $(this).closest('tr').find('td input').prop('disabled', $(this).prop('checked'));
    });

    $('.table-auth td input').click(function(){
        var noChecked = $(this).closest('td').find('input:not(:checked)').length;
        
        if(noChecked == 0){
            $(this).closest('tr').find('th input').prop('checked', true);
            $(this).closest('tr').find('td input').prop('disabled', true);
        }
    });

JS;

$this->registerJs($js);
