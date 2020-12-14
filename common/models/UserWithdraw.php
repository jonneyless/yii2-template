<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "{{%user_withdraw}}".
 *
 * @property string $withdraw_id 提现 ID
 * @property string $user_id 用户 ID
 * @property string $amount 金额
 * @property string $type 提现方式
 * @property string $account 提现账号
 * @property int $created_at 申请时间
 * @property int $updated_at 更新时间
 * @property int $status 状态
 */
class UserWithdraw extends \common\models\base\UserWithdraw
{

    const STATUS_CANCEL = 0;
    const STATUS_NEW = 1;
    const STATUS_WAITING = 2;
    const STATUS_DONE = 9;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return ArrayHelper::merge(parent::rules(), [
            ['status', 'default', 'value' => self::STATUS_NEW],
            ['status', 'in', 'range' => [self::STATUS_CANCEL, self::STATUS_NEW, self::STATUS_WAITING, self::STATUS_DONE]],
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
    }
}
