<?php

namespace api\modules\v1\controllers\user;

use api\controllers\ApiController;
use api\models\Message;
use api\models\UserMessage;
use ijony\helpers\Url;
use Yii;
use yii\data\ActiveDataProvider;
use api\filters\QueryParamAuth;
use yii\web\BadRequestHttpException;

class MessageController extends ApiController
{

    public $modelClass = 'api\models\Message';

    /**
     * {@inheritdoc}
     * @return array
     */
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['authenticator'] = [
            'class' => QueryParamAuth::className(),
        ];
        return $behaviors;
    }

    public function actions()
    {
        $actions = parent::actions();

        unset($actions['view'], $actions['delete']);

        return $actions;
    }

    public function actionIndex()
    {
        $types = Message::getMessageTypeData();

        $return = [];
        foreach($types as $type => $name){
            $return[] = [
                'name' => $name,
                'type' => $type,
                'icon' => Url::getStatic('message/' . $type . '.png'),
                'message' => Message::getLastByType($type),
                'unread' => Message::getUnreadCountByType($type),
            ];
        }

        return $return;
    }

    public function actionList($type)
    {
        $params = Yii::$app->getRequest()->getQueryParams();

        $query = Message::find()->where([
            'or',
            ['=', 'is_all', Message::IS_ALL_YES],
            [
                'and',
                ['=', 'is_all', Message::IS_ALL_NO],
                ['in', 'message_id', UserMessage::find()->select('message_id')->where(['user_id' => Yii::$app->user->id])->column()],
            ]
        ])->andWhere(['status' => Message::STATUS_SHOW, 'type' => $type]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'validatePage' => false,
                'params'       => $params,
            ],
            'sort' => [
                'defaultOrder' => [
                    'created_at' => SORT_DESC
                ]
            ]
        ]);

        return array_map(function($data){
            return $data->buildData();
        }, $dataProvider->getModels());
    }

    public function actionRead($id)
    {
        $message = Message::findOne($id);

        if(!$message){
            throw new BadRequestHttpException('消息不存在！');
        }

        if(!$message->checkAuth()){
            throw new BadRequestHttpException('你无权阅读该消息！');
        }

        if($message->read){
            $read = $message->read;
        }else{
            $read = new UserMessage();
            $read->message_id = $message->message_id;
            $read->user_id = Yii::$app->user->id;
        }

        $read->is_read = UserMessage::IS_READ_YES;
        if(!$read->save()){
            throw new BadRequestHttpException('标记已读失败！');
        }

        return [
            'message' => '已读标记成功！',
        ];
    }

    public function actionCount()
    {
        $isReadMsgIds = UserMessage::find()->select('message_id')->where(['is_read' => UserMessage::IS_READ_YES, 'user_id' => Yii::$app->user->id])->column();
        $unReadMsgCount_isAll = Message::find()->where([
            'and',
            ['=', 'is_all', Message::IS_ALL_YES],
            ['=', 'status', Message::STATUS_SHOW],
            ['not in', 'message_id', $isReadMsgIds],
        ])->count();
        $unReadMsgCount_notAll = UserMessage::find()->where(['is_read' => UserMessage::IS_READ_NO, 'user_id' => Yii::$app->user->id])->count();

        return [
            'msg' => $unReadMsgCount_isAll + $unReadMsgCount_notAll,
        ];
    }
}