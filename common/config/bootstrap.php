<?php
Yii::setAlias('@common', dirname(__DIR__));
Yii::setAlias('@libs', dirname(dirname(__DIR__)) . '/libs');
Yii::setAlias('@wap', dirname(dirname(__DIR__)) . '/apps/wap');
Yii::setAlias('@admin', dirname(dirname(__DIR__)) . '/apps/admin');
Yii::setAlias('@console', dirname(dirname(__DIR__)) . '/apps/console');
Yii::setAlias('@static', dirname(dirname(__DIR__)) . '/webs/static');

defined('THUMB_FOLDER') or define('THUMB_FOLDER', 'thumb');
defined('BUFFER_FOLDER') or define('BUFFER_FOLDER', 'buffer');
defined('UPLOAD_FOLDER') or define('UPLOAD_FOLDER', 'upload');

Yii::setAlias('@thumb', dirname(dirname(__DIR__)) . '/webs/static/' . THUMB_FOLDER);
Yii::setAlias('@buffer', dirname(dirname(__DIR__)) . '/webs/static/' . BUFFER_FOLDER);
Yii::setAlias('@upload', dirname(dirname(__DIR__)) . '/webs/static/' . UPLOAD_FOLDER);
