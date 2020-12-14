<?php

namespace api\models;

use Yii;

/**
 * This is the model class for table "{{%message}}".
 *
 * {@inheritdoc}
 *
 * @property \api\models\UserMessage $read
 */
class Message extends \common\models\Message
{

    private static $userMsgIds;
    private static $userMsgIdsByType;

    public function getRead()
    {
        return $this->hasOne(UserMessage::className(), ['message_id' => 'message_id'])->andWhere(['user_id' => Yii::$app->user->id]);
    }

    public function checkAuth()
    {
        if($this->is_all === self::IS_ALL_YES){
            return true;
        }

        return $this->read !== null;
    }

    public function checkIsRead()
    {
        if($this->read){
            return $this->read->is_read == UserMessage::IS_READ_YES;
        }

        return false;
    }

    public function buildData()
    {
        return [
            'message_id' => $this->message_id,
            'title' => $this->title,
            'content' => $this->content,
            'created_at' => date("Y-m-d", $this->created_at),
            'is_read' => $this->checkIsRead(),
        ];
    }

    public static function getMessageTypeData()
    {
        return [
            self::TYPE_SYSTEM => '系统消息',
            self::TYPE_DELIVERY => '物流提醒',
            self::TYPE_PROMOTION => '促销信息',
        ];
    }

    public static function getUserMsgIds()
    {
        if(self::$userMsgIds === null){
            self::$userMsgIds = Message::find()->select('message_id')->where([
                'or',
                ['=', 'is_all', Message::IS_ALL_YES],
                [
                    'and',
                    ['=', 'is_all', Message::IS_ALL_NO],
                    ['in', 'message_id', UserMessage::find()->select('message_id')->where(['user_id' => Yii::$app->user->id])->column()],
                ]
            ])->andWhere(['status' => Message::STATUS_SHOW])->column();
        }

        return self::$userMsgIds;
    }

    public static function getUserMsgIdsByType($type)
    {
        return Message::find()->select('message_id')->where([
            'or',
            ['=', 'is_all', Message::IS_ALL_YES],
            [
                'and',
                ['=', 'is_all', Message::IS_ALL_NO],
                ['in', 'message_id', UserMessage::find()->select('message_id')->where(['user_id' => Yii::$app->user->id])->column()],
            ]
        ])->andWhere(['status' => Message::STATUS_SHOW, 'type' => $type])->column();
    }

    public static function getLastByType($type)
    {
        $userMsgIds = self::getUserMsgIds();

        $message = Message::find()->where(['status' => Message::STATUS_SHOW, 'type' => $type, 'message_id' => $userMsgIds])->orderBy(['created_at' => SORT_DESC])->one();

        if(!$message){
            return [
                'message_id' => 0,
                'title' => '暂无消息',
                'content' => '',
                'created_at' => '',
                'is_read' => false,
            ];
        }

        return $message->buildData();
    }

    public static function getUnreadCountByType($type)
    {
        $userMsgIds = self::getUserMsgIdsByType($type);

        $isReadMsgIds = UserMessage::find()->select('message_id')->where(['is_read' => UserMessage::IS_READ_YES, 'message_id' => $userMsgIds, 'user_id' => Yii::$app->user->id])->column();
        $unReadMsgCount_isAll = Message::find()->where([
            'and',
            ['=', 'status', Message::STATUS_SHOW],
            ['=', 'is_all', Message::IS_ALL_YES],
            ['=', 'type', $type],
            ['not in', 'message_id', $isReadMsgIds],
        ])->count();
        $unReadMsgCount_notAll = UserMessage::find()->where(['is_read' => UserMessage::IS_READ_NO, 'message_id' => $userMsgIds, 'user_id' => Yii::$app->user->id])->count();

        return $unReadMsgCount_isAll + $unReadMsgCount_notAll;
    }
}
