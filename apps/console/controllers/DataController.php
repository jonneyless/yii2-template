<?php

namespace console\controllers;

use admin\models\Goods;
use admin\models\UserSettle;
use api\models\Order;
use api\models\Payment;
use api\models\UserIncome;
use api\models\UserRenew;
use common\models\GoodsInfo;
use common\models\Product;
use common\models\ProductGallery;
use common\models\Ad;
use common\models\GoodsGallery;
use common\models\User;
use ijony\helpers\Folder;
use ijony\helpers\Image;
use Yii;
use yii\base\ErrorException;
use yii\console\Controller;

class DataController extends Controller
{

    public function actionFix()
    {
        /* @var Order[] $orders */
        $orders = Order::find()->where(['status' => Order::STATUS_DONE])->orderBy(['created_at' => SORT_ASC])->all();
        foreach($orders as $order){
            if($order->is_offline){
                $order->setOfflineReward();
            }else{
                $order->setReward();
            }
        }

        /* @var UserRenew[] $renews */
        $renews = UserRenew::find()->where(['status' => UserRenew::STATUS_DONE])->orderBy(['created_at' => SORT_ASC])->all();
        foreach($renews as $renew){
            $renew->setReward();
        }
    }

    public function actionUserAmount()
    {
        User::updateAll(['amount' => 0]);

        $user_ids = UserIncome::find()->select('user_id')->groupBy('user_id')->column();

        foreach($user_ids as $userId){
            $user = \api\models\User::findOne($userId);
            $user->amount = UserIncome::find()->where(['user_id' => $userId])->sum('amount');
            $user->save();
        }

        $settles = UserSettle::find()->where(['status' => 1])->all();
        foreach($settles as $settle){
            $user = \api\models\User::findOne($settle->user_id);
            $user->amount = $user->amount - $settle->amount;
            $user->save();
        }

        $payments = Payment::find()->where(['status' => Payment::STATUS_DONE, 'pay_type' => 'balance'])->all();
        foreach($payments as $payment){
            $user = \api\models\User::findOne($payment->user_id);
            $user->amount = $user->amount - $payment->amount;
            $user->save();
        }
    }

    public function actionResync($id = 0)
    {
        $data = Goods::find()->where([
            'and',
            ['>', 'goods_id', $id],
            ['=', 'store_id', 29],
            ['<>', 'pospal_id', ''],
            ['=', 'status', Goods::STATUS_ACTIVE],
        ])->all();
        foreach($data as $datum){
            if(!$datum->sync()){
                echo $datum->goods_id . " error.\n";
            }
        }
    }

    public function actionReferee()
    {
        $this->fixReferee(0);
    }

    public function fixReferee($referee_id = 0)
    {
        $referee = User::findOne($referee_id);

        $users = User::find()->where(['referee' => $referee_id])->all();

        foreach($users as $user){
            if($referee){
                if($referee->type == 2){
                    $user->company = $referee->user_id;
                }else{
                    $user->company = $referee->company;
                }
            }else{
                $user->company = 0;
            }
            $user->save();

            $this->fixReferee($user->user_id);
        }
    }

    public function actionGoods()
    {
        /* @var Goods[] $data */
        $data = Goods::find()->where(['preview' => ''])->all();
        foreach($data as $datum){
            if($datum->gallery){
                $datum->preview = $datum->gallery[0]->image;
                $datum->save();
            }
        }
    }

    public function actionImage()
    {
        /* @var Goods[] $data */
        $data = Goods::find()->all();
        foreach($data as $datum){
            $image = $datum->preview;

            if(!file_exists(Folder::getStatic('tmp/' . $image))) continue;

            Image::copyImg('tmp/' . $image, $image);
        }

        /* @var Product[] $data */
        $data = Product::find()->all();
        foreach($data as $datum){
            $image = $datum->preview;

            if(!file_exists(Folder::getStatic('tmp/' . $image))) continue;

            Image::copyImg('tmp/' . $image, $image);
        }
    }

    public function actionAd()
    {
        /* @var Ad[] $data */
        $data = Ad::find()->all();
        foreach($data as $datum){
            $image = $datum->image;

            if(!file_exists(Folder::getStatic('tmp/' . $image))) continue;

            Image::copyImg('tmp/' . $image, $image);
        }
    }

    public function actionGallery()
    {
        /* @var GoodsGallery[] $data */
        $data = GoodsGallery::find()->all();
        foreach($data as $datum){
            $image = $datum->image;

            if(!file_exists(Folder::getStatic('tmp/' . $image))) continue;

            Image::copyImg('tmp/' . $image, $image);
        }

        /* @var ProductGallery[] $data */
        $data = ProductGallery::find()->all();
        foreach($data as $datum){
            $image = $datum->image;

            if(!file_exists(Folder::getStatic('tmp/' . $image))) continue;

            Image::copyImg('tmp/' . $image, $image);
        }
    }

    public function actionInfo()
    {
        $data = Goods::find()->all();
        foreach($data as $datum){
            if(!$datum->info){
                (new GoodsInfo(['goods_id' => $datum->goods_id, 'stock' => 1000, 'sell' => 0]))->save();
            }
        }
    }
}