<?php

namespace common\models\base;

use Yii;

/**
 * This is the model class for table "{{%user_info}}".
 *
 * @property int $user_id 用户 ID
 * @property int $birthday 生日
 * @property string $gander 性别
 * @property string $truename 真实姓名
 * @property string $idcard 身份证号
 * @property string $mobile 手机号码
 * @property string $bankno 银行卡号
 * @property string $bankname 支行名称
 * @property int $can_modify 可以修改
 */
class UserInfo extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%user_info}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id'], 'required'],
            [['user_id', 'birthday', 'can_modify'], 'integer'],
            [['gander'], 'string', 'max' => 1],
            [['truename', 'mobile'], 'string', 'max' => 60],
            [['idcard', 'bankno', 'bankname'], 'string', 'max' => 120],
            [['user_id'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'user_id' => '用户 ID',
            'birthday' => '生日',
            'gander' => '性别',
            'truename' => '真实姓名',
            'idcard' => '身份证号',
            'mobile' => '手机号码',
            'bankno' => '银行卡号',
            'bankname' => '支行名称',
            'can_modify' => '可以修改',
        ];
    }
}
