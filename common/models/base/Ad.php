<?php

namespace common\models\base;

use Yii;

/**
 * This is the model class for table "{{%ad}}".
 *
 * @property string $ad_id 广告 ID
 * @property int $type 广告类型
 * @property string $mode 跳转模式
 * @property string $name 名称
 * @property string $image 广告图
 * @property string $url 链接
 * @property string $sort 排序
 * @property int $status 状态
 */
class Ad extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%ad}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['type', 'sort', 'status'], 'integer'],
            [['name', 'image'], 'required'],
            [['mode'], 'string', 'max' => 60],
            [['name', 'image', 'url'], 'string', 'max' => 150],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'ad_id' => '广告 ID',
            'type' => '广告类型',
            'mode' => '跳转模式',
            'name' => '名称',
            'image' => '广告图',
            'url' => '链接',
            'sort' => '排序',
            'status' => '状态',
        ];
    }
}
