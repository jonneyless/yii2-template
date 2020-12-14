<?php

namespace common\models;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "{{%category}}".
 *
 * @property string $id
 * @property string $parent_id
 * @property string $name
 * @property integer $status
 */
class Category extends namespace\base\Category
{

    /**
     * @var 禁用
     */
    const STATUS_DELETED = 0;
    /**
     * @var 启用
     */
    const STATUS_ACTIVE = 9;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return ArrayHelper::merge(parent::rules(), [
            ['status', 'default', 'value' => self::STATUS_ACTIVE],
            ['status', 'in', 'range' => [self::STATUS_ACTIVE, self::STATUS_DELETED]],
        ]);
    }

    public static function getSelectDatas($parent_id = 0)
    {
        return static::find()->select(['name', 'id'])->where(['parent_id' => $parent_id])->indexBy('id')->column();
    }
}
