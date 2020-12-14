<?php

namespace api\models;

use Yii;

/**
 * 页面数据模型
 *
 * {@inheritdoc}
 */
class Page extends \common\models\Page
{

    public static function getPageById($id)
    {
        $model = self::findOne($id);

        $return = [
            'title' => '',
            'content' => '',
        ];

        if($model){
            $return = [
                'title' => $model->title,
                'content' => $model->content,
            ];
        }

        return $return;
    }
}
