<?php

namespace common\models\base;

use Yii;

/**
 * This is the model class for table "{{%service_attachment}}".
 *
 * @property string $service_id 售后编号
 * @property string $type 类型
 * @property string $file 文件
 */
class ServiceAttachment extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%service_attachment}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['service_id', 'type', 'file'], 'required'],
            [['service_id', 'type'], 'string', 'max' => 20],
            [['file'], 'string', 'max' => 150],
            [['service_id', 'type', 'file'], 'unique', 'targetAttribute' => ['service_id', 'type', 'file']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'service_id' => '售后编号',
            'type' => '类型',
            'file' => '文件',
        ];
    }
}
