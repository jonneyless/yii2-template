<?php

namespace api\models;

use ijony\helpers\Url;
use Yii;

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
class UserWithdraw extends \common\models\UserWithdraw
{

    const API_STATUS_CANCEL = 'cancel';
    const API_STATUS_NEW = 'new';
    const API_STATUS_WAITING = 'waiting';
    const API_STATUS_DONE = 'done';

    private static $_status = [
        self::API_STATUS_CANCEL => self::STATUS_CANCEL,
        self::API_STATUS_NEW => self::STATUS_NEW,
        self::API_STATUS_WAITING => self::STATUS_WAITING,
        self::API_STATUS_DONE => self::STATUS_DONE,
    ];

    private static $_api_status = [
        self::STATUS_CANCEL => self::API_STATUS_CANCEL,
        self::STATUS_NEW => self::API_STATUS_NEW,
        self::STATUS_WAITING => self::API_STATUS_WAITING,
        self::STATUS_DONE => self::API_STATUS_DONE,
    ];

    public function cancel()
    {
        $this->status = self::STATUS_CANCEL;

        return $this->save();
    }

    public function parseType()
    {
        $data = [
            'alipay' => '支付宝',
            'wechat' => '微信',
            'bank' => '银行卡',
        ];

        return isset($data[$this->type]) ? $data[$this->type] : $this->type;
    }

    public function buildListData()
    {

        return [
            'withdraw_id' => $this->withdraw_id,
            'amount' => $this->amount,
            'type' => $this->parseType(),
            'account' => $this->account,
            'created_at' => date("Y-m-d H:i:s", $this->created_at),
            'status' => self::parseApiStatus($this->status),
            'detail' => Url::getFull('withdraw-' . $this->withdraw_id . '.html'),
        ];
    }

    public function buildViewData()
    {
        return [
            'withdraw_id' => $this->withdraw_id,
            'amount' => $this->amount,
            'type' => $this->parseType(),
            'account' => $this->account,
            'created_at' => date("Y-m-d H:i:s", $this->created_at),
            'status' => self::parseApiStatus($this->status),
            'detail' => Url::getFull('withdraw-' . $this->withdraw_id . '.html'),
        ];
    }

    public static function parseStatus($status)
    {
        return isset(self::$_status[$status]) ? self::$_status[$status] : '';
    }

    public static function parseApiStatus($status)
    {
        return isset(self::$_api_status[$status]) ? self::$_api_status[$status] : '';
    }
}
