<?php

namespace console\controllers;

use api\models\User;
use common\models\Message;
use liasica\XingeApp\Message as MessageAndroid;
use liasica\XingeApp\MessageIOS;
use liasica\XingeApp\TimeInterval;
use liasica\XingeApp\XingeApp;
use libs\Utils;
use Yii;
use yii\console\Controller;

class PushController extends Controller
{

    public function actionSend($msg)
    {
        $users = User::find()->where(['<>', 'device', ''])->all();

        foreach($users as $user){
            $mess = new MessageIOS();
            $mess->setExpireTime(86400);
            $mess->setAlert($msg);
            $mess->setBadge(1);
            $mess->setSound("beep.wav");
            $custom = array('key1'=>'value1', 'key2'=>'value2');
            $mess->setCustom($custom);
            $acceptTime1 = new TimeInterval(0, 0, 23, 59);
            $mess->addAcceptTime($acceptTime1);

            Utils::xinge()->PushSingleDevice($user->device, $mess, XingeApp::IOSENV_PROD);
        }
//
//        Utils::xinge()->PushSingleDevice($user->device, $mess, 0);
    }

    public function actionMsg($type, $title, $content)
    {
        $model = new Message();
        $model->admin_id = 0;
        $model->type = $type;
        $model->title = $title;
        $model->content = $content;
        $model->is_all = Message::IS_ALL_YES;
        $model->status = Message::STATUS_SHOW;
        $model->save();

        $mess = new MessageIOS();
        $mess->setExpireTime(86400);
        $mess->setAlert($content);
        $mess->setBadge(1);
        $mess->setSound("beep.wav");
        $custom = array('key1'=>'value1', 'key2'=>'value2');
        $mess->setCustom($custom);
        $acceptTime1 = new TimeInterval(0, 0, 23, 59);
        $mess->addAcceptTime($acceptTime1);

        $return = Utils::xinge(false)->CreateMultipush($mess, XingeApp::IOSENV_PROD);

        if(isset($return['result']['push_id'])){
            $pushId = $return['result']['push_id'];
            $deviceList = User::find()->where([
                'and',
                ['<>', 'device', ''],
                ['=', 'device_type', 'iphone'],
            ])->select('device')->column();
            Utils::xinge(false)->PushDeviceListMultiple($pushId, $deviceList);
        }

        $mess = new MessageAndroid();
        $mess->setExpireTime(86400);
        $mess->setContent($content);
        $mess->setTitle('来就省商城');
        $mess->setType(MessageAndroid::TYPE_NOTIFICATION);
        $custom = array('key1'=>'value1', 'key2'=>'value2');
        $mess->setCustom($custom);
        $acceptTime1 = new TimeInterval(0, 0, 23, 59);
        $mess->addAcceptTime($acceptTime1);

        $return = Utils::xinge()->CreateMultipush($mess);

        if(isset($return['result']['push_id'])){
            $pushId = $return['result']['push_id'];
            $deviceList = User::find()->where([
                'and',
                ['<>', 'device', ''],
                ['=', 'device_type', 'android'],
            ])->select('device')->column();
            Utils::xinge()->PushDeviceListMultiple($pushId, $deviceList);
        }
    }
}