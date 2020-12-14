<?php

/* @var $this yii\web\View */

$this->title = '系统提示';

?>

<div class="container-fluid" style="padding-top: 100px;">
    <div class="panel panel-default">
        <div class="panel-heading">
            <h2 class="panel-title">系统提示</h2>
        </div>
        <div class="panel-body">
            <div class="col-md-1"></div>
            <div class="col-md-10">
                <p>&nbsp;</p>
                <p class="text-center">未验证通过用户，不能参与活动！</p>
                <p>&nbsp;</p>
                <p class="text-center gray">请点击进入
                    <a class="red" href="<?= Yii::$app->params['zyd.url'] ?>/person.aspx">个人中心</a></p>
                <p>&nbsp;</p>
            </div>
            <div class="col-md-1"></div>
        </div>
    </div>
</div>
