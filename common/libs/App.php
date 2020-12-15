<?php

namespace common\libs;

/**
 * 快捷方法
 */
/**
 * @return \yii\console\Application|\yii\web\Application
 */
function app()
{
    return \Yii::$app;
}

/**
 * @return \yii\db\Connection
 */
function db()
{
    return \Yii::$app->db;
}

/**
 * @return mixed|object|\yii\web\User|null
 */
function user()
{
    return \Yii::$app->user;
}