<?php

use common\models\Config;

/* @var $this yii\web\View */
/* @var $data \common\models\Group */

$this->title = '活动介绍';

?>

<div class="event-intro">
    <div class="event-intro-top">
        <div class="event-intro-bottom">
            <?= Config::showByName('event-intro') ?>
        </div>
    </div>
</div>