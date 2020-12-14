<?php

namespace wap\controllers;

use common\models\Ad;
use common\models\Address;
use common\models\Event;
use common\models\Goods;
use common\models\GoodsVirtual;
use common\models\Group;
use common\models\Order;
use common\models\User;
use libs\ccbpay\Ccb;
use wap\models\LoginForm;
use dosamigos\qrcode\lib\Enum;
use dosamigos\qrcode\QrCode;
use libs\ccbpay\Wap;
use libs\SMS;
use Yii;
use yii\base\ErrorException;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\helpers\Json;
use yii\helpers\Url;
use yii\widgets\LinkPager;

/**
 * 主流程控制器
 *
 * @package wap\controllers
 * @author Jony <jonneyless@163.com>
 * @since 2016.11.21
 */
class SiteController extends Controller
{

    public $enableCsrfValidation = false;

    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

    /**
     * 首页
     * @return string
     */
    public function actionIndex()
    {
        $listDatas = Goods::find()->where(['status' => Goods::STATUS_SHELVE])->indexBy('id')->orderBy(['sales' => SORT_DESC])->limit(2)->all();

        return $this->render('index', [
            'listDatas' => $listDatas,
        ]);
    }

    /**
     * 列表
     *
     * @param $type
     *
     * @return string
     */
    public function actionList($type = 0)
    {
        $dataProvider = new ActiveDataProvider([
            'query' => Goods::find()->where(['status' => Goods::STATUS_SHELVE, 'one_delivery' => $type])->orderBy(['sales' => SORT_DESC]),
            'pagination' => [
                'pageSize' => 10,
                'validatePage' => false,
            ],
        ]);

        $listDatas = $dataProvider->getModels();
        $listPages = $dataProvider->getPagination();

        if (Yii::$app->request->getIsAjax()) {
            $return = [
                'list' => $this->renderPartial('goods-list', ['listDatas' => $listDatas]),
                'page' => LinkPager::widget(['pagination' => $listPages]),
            ];

            return Json::encode($return);
        }

        return $this->render('list', [
            'listDatas' => $listDatas,
            'listPages' => $listPages,
            'type' => $type,
        ]);
    }

    /**
     * 商品详情
     *
     * @param $id
     *
     * @return string|\yii\web\Response
     */
    public function actionGoods($id)
    {
        $model = Goods::findOne($id);

        if (!$model) {
            return $this->message('该商品不存在！');
        }

        Yii::$app->session->set('goods_id', $model->id);

        return $this->render('goods', ['model' => $model]);
    }

    /**
     * 购物车
     *
     * @param int $goods_id
     * @param int $quantity
     *
     * @return string|\yii\web\Response
     */
    public function actionCart($goods_id = 0, $quantity = 0)
    {
        if (!$goods_id) {
            $goods_id = Yii::$app->session->get('goods_id');
        }

        if (!$quantity) {
            $quantity = Yii::$app->session->get('quantity');
        }

        $model = Goods::findOne($goods_id);

        if (!$model) {
            return $this->message('该商品不存在！');
        }

        if (!$model->checkStock($quantity)) {
            return $this->message('当前商品库存不足，请选择其他商品！', ['site/goods', 'id' => $model->id]);
        }

        $order = new Order();
        $order->goods_id = $goods_id;
        $order->price = $model->price;
        $order->quantity = $quantity;
        $order->amount = $quantity * $model->price;

        return $this->render('cart', ['model' => $model, 'order' => $order]);
    }

    /**
     * 确认订单
     *
     * @return string|\yii\web\Response
     */
    public function actionConfirm()
    {
        $goods_id = Yii::$app->request->post('goods_id');
        $quantity = Yii::$app->request->post('quantity');
        $consignee = Yii::$app->request->post('consignee', '');
        $area_id = intval(Yii::$app->request->post('area_id', 0));
        $address = Yii::$app->request->post('address', '');
        $phone = Yii::$app->request->post('phone', '');

        $begin_time = strtotime(date('Y-m-d 07:00:00', time()));
        $end_time = strtotime(date('Y-m-d 22:00:00', time()));

        if ($begin_time > time() || $end_time < time()) {
            return $this->message('每天活动时间为 07:00 ~ 22:00 !');
        }

        if (!$goods_id || !$quantity) {
            return $this->message('非法访问！');
        }

        $goods = Goods::findOne($goods_id);

        if (!$goods) {
            return $this->message('商品不存在！');
        }

        if (($goods->is_virtual == 0 && (!$consignee || !$phone || !$address || !$area_id)) ||
            ($goods->is_virtual == 1 && !$phone)) {
            return $this->message('请设置收货地址！');
        }

        if (!$goods->checkGroupQuantity($quantity)) {
            $quantity = $goods->getMinGroupQuantity();
        }

        if (!$goods->checkStock($quantity)) {
            return $this->message('当前拼单库存不足，请选择其他拼单！', ['site/goods', 'id' => $goods->id]);
        }

        $this->backUrl = Url::to(['site/cart', 'goods_id' => $goods_id, 'quantity' => $quantity]);

        if (Yii::$app->user->getIsGuest()) {
            Yii::$app->user->setReturnUrl(['site/cart', 'goods_id' => $goods_id, 'quantity' => $quantity]);

            return $this->message('请先登录！', ['site/login']);
        }

        if (Yii::$app->user->identity->getIsNeedCheck()) {
            Yii::$app->user->identity->sign_status = (new Ccb())->getSignStatus(Yii::$app->user->identity->mobile);
        }

        if (Yii::$app->user->identity->getIsNeedCheck()) {
            return $this->message('呀！服务器开小差了，请稍后再试！', ['site/goods', 'id' => $goods->id]);
        }

        if (!Yii::$app->user->identity->checkSignStatus()) {
            return $this->message('非苏州建行签约用户，不能参与该活动哦！', ['site/goods', 'id' => $goods->id]);
        }

        if ($msg = $goods->checkEvent($quantity)) {
            return $this->message($msg, ['site/goods', 'id' => $goods->id]);
        }

        if (!$goods->checkDaily($quantity)) {
            return $this->message('今天的' . $quantity . '人拼活动已结束！', ['site/goods', 'id' => $goods->id]);
        }

        if ($msg = Event::checkQuantity(Yii::$app->user->id, true)) {
            return $this->message($msg, ['site/index']);
        }

        $leader = $goods->getLeaderByQuantity($quantity);
        $price = $goods->getPrePriceByQuantity($quantity);
        $cost = $goods->one_delivery ? $goods->getDeliveryQuantityByQuantity($quantity) : $quantity;

        try {
            $transaction = Yii::$app->db->beginTransaction();

            $group = new Group();
            $group->user_id = Yii::$app->user->identity->id;
            $group->goods_id = $goods->id;
            $group->one_delivery = $goods->one_delivery;
            $group->leader = $leader;
            $group->price = $price;
            $group->quantity = $quantity;
            $group->delivery = $cost;
            $group->amount = $price * ($quantity - 1) + $leader;
            $group->expiry = time() + ($goods->group_expiry * 3600);

            $month_end = strtotime(date("Y-m-1", strtotime('+1 month', $group->expiry)));
            if ($group->expiry >= $month_end) {
                $group->expiry = $month_end - 1;
            }

            if ($goods->one_delivery) {
                $group->consignee = $consignee;
                $group->area_id = $area_id;
                $group->address = $address;
                $group->phone = $phone;
            }
            if (!$group->save()) {
                throw new ErrorException('拼单生成失败！');
            }

            $order = new Order();
            $order->id = Order::genId();
            $order->user_id = Yii::$app->user->identity->id;
            $order->goods_id = $goods->id;
            $order->group_id = $group->id;
            $order->price = $leader;
            $order->quantity = 1;
            $order->amount = $goods->getFreightFee() + $leader;
            $order->paid = 0.00;
            $order->consignee = $consignee;
            $order->area_id = $area_id;
            $order->address = $address;
            $order->phone = $phone;
            $order->is_first = Order::IS_FIRST_YES;
            if (!$order->save()) {
                throw new ErrorException('拼单订单生成失败！');
            }

            if ($goods->is_virtual == 1) {
                $datas = GoodsVirtual::find()->where([
                    'goods_id' => $goods->id,
                    'group_id' => 0,
                    'status' => 0,
                ])->limit($cost)->all();

                if (!$datas || count($datas) < $cost) {
                    throw new ErrorException($goods->name . '虚拟卡缺货！！');
                }
                /* @var $data \common\models\GoodsVirtual */
                foreach ($datas as $data) {
                    $data->group_id = $group->id;
                    $data->save();
                }
            }

            if (!$goods->updateStock($group->delivery, $group->quantity)) {
                throw new ErrorException('当前拼单库存不足，请选择其他拼单！');
            }

            $transaction->commit();

            Yii::$app->session->set('goods_id', 0);
            Yii::$app->session->set('quantity', 0);
            Yii::$app->session->set('address_id', 0);

            return $this->redirect(['pay', 'order' => $order->id]);
        } catch (ErrorException $e) {

            $transaction->rollBack();

            return $this->message($e->getMessage());
        }
    }

    /**
     * 订单支付
     *
     * @param $order
     *
     * @return string
     */
    public function actionPay($order)
    {
        if (Yii::$app->user->identity->getIsNeedCheck()) {
            Yii::$app->user->identity->sign_status = (new Ccb())->getSignStatus(Yii::$app->user->identity->mobile);
        }

        if (Yii::$app->user->identity->getIsNeedCheck()) {
            return $this->message('呀！服务器开小差了，请稍后再试！');
        }

        if (!Yii::$app->user->identity->checkSignStatus()) {
            return $this->message('非苏州建行签约用户，不能参与该活动哦！');
        }

        $order = Order::findOne($order);

        if (!$order) {
            return $this->message('订单不存在！');
        }

        if ($order->user_id != Yii::$app->user->id) {
            return $this->message('这不是你的订单！');
        }

        $pay = (new Wap())->getCode($order);

        return $this->render('pay', ['order' => $order, 'pay' => $pay]);
    }

    /**
     * 拼单分享
     *
     * @param $id
     *
     * @return string
     */
    public function actionGroup($id)
    {
        // 不显示头部导航条
        $this->header = false;

        /* @var $model \common\models\Group */
        $model = Group::findOne($id);

        if (!$model) {
            return $this->message('该拼单活动不存在！', ['site/index']);
        }

        if ($model->status == Group::STATUS_CANCEL) {
            return $this->message('该拼单活动已失败！', ['site/index']);
        }

        if ($model->status == Group::STATUS_UNACTIVE) {
            return $this->message('该拼单活动待支付！', ['site/index']);
        }

        if ($model->status == Group::STATUS_OVER) {
            return $this->render('group-detail', ['model' => $model]);
        }

        $joiner = 0;
        foreach ($model->order as $order) {
            if ($order->status != Order::STATUS_CANCEL) {
                $joiner++;
            }
        }

        if ($joiner >= $model->quantity) {
            return $this->message('该拼单活动参与人数已满！', ['site/index']);
        }

        return $this->render('group', ['model' => $model]);
    }

    /**
     * 参与拼单
     *
     * @param $id
     *
     * @return string|\yii\web\Response
     */
    public function actionJoin($id)
    {
        $step = Yii::$app->request->post('step', 1);

        /* @var $model \common\models\Group */
        $model = Group::findOne($id);

        if (!$model) {
            return $this->message('该拼单活动不存在！', ['site/index']);
        }

        if ($model->status == Group::STATUS_CANCEL) {
            return $this->message('该拼单活动已失败！', ['site/index']);
        }

        if ($model->status == Group::STATUS_UNACTIVE) {
            return $this->message('该拼单活动待支付！', ['site/index']);
        }

        if ($model->status == Group::STATUS_OVER) {
            return $this->message('该拼单活动已成功，您可进入活动页面自己发起拼团！', ['site/index']);
        }

        $order = new Order();
        $order->goods_id = $model->goods_id;
        $order->group_id = $model->id;
        $order->price = $model->price;
        $order->quantity = 1;
        if ($model->goods->one_delivery) {
            $order->amount = $model->price;
        } else {
            $order->amount = $model->goods->getFreightFee() + $model->price;
        }
        $order->paid = 0.00;

        if ($order->load(Yii::$app->request->post())) {
            if (!$order->phone) {
                $order->addError('phone', '请填写联系电话！');
            }

            if (!$order->checkMobile()) {
                $order->addError('phone', '已参与该拼单，请到我的订单中查看！');
            }

            if ($msg = SMS::validator($order->vcode)) {
                $order->addError('vcode', $msg);
            }

            if (!$order->getErrors()) {
                if (Yii::$app->user->getIsGuest() || Yii::$app->user->identity->mobile != $order->phone) {
                    $user = User::find()->where(['mobile' => $order->phone])->one();
                    if (!$user) {
                        $user = new User();
                        $user->name = '用户' . $order->phone;
                        $user->mobile = $order->phone;
                        $user->generateAuthKey();
                        $user->sign_status = (new Ccb())->getSignStatus($user->mobile);
                        $user->save();
                    }

                    Yii::$app->user->login($user, 3600 * 2);
                } else {
                    if (Yii::$app->user->identity->getIsNeedCheck()) {
                        Yii::$app->user->identity->sign_status = (new Ccb())->getSignStatus(Yii::$app->user->identity->mobile);
                    }

                    $user = Yii::$app->user->identity;
                }

                if ($user->getIsNeedCheck()) {
                    $order->addError('phone', '接收失败，请稍后重试！');
                }

                if (!$user->checkSignStatus()) {
                    $order->addError('phone', '非苏州建行手机银行签约用户，不能参与该活动哦！');
                }

                $order->user_id = $user->id;

                if ($msg = Event::checkQuantity($order->user_id, false)) {
                    $order->addError('phone', $msg);
                }
            }

            if ($step == 2) {
                if (!$order->consignee) {
                    $order->addError('consignee', '请填写收货人！');
                }

                if (!$order->area_id) {
                    $order->addError('area_id', '请选择所在区域！');
                }

                if (!$order->address) {
                    $order->addError('address', '请填写收货详细地址！');
                }
            }

            if (!$order->getErrors()) {
                // 判断是否完成所有步骤了
                if (
                    // 只发团长就只需要完成第一步
                    $model->one_delivery == 1 ||
                    // 虚拟卡商品也只需要完成第一步
                    (
                        $model->goods->is_virtual == 1 ||
                        $order->address
                    )
                ) {
                    $order->id = Order::genId();
                    $order->save();

                    return $this->redirect(['pay', 'order' => $order->id]);
                }

                // 切换到参与流程第二步
                $step = 2;
            }
        }

        return $this->render('join', ['model' => $model, 'order' => $order, 'step' => $step]);
    }

    /**
     * 返回二维码图片源码
     *
     * @param $url
     *
     * @return string
     */
    public function actionQrcode($url = '')
    {
        return QrCode::png($url, false, Enum::QR_ECLEVEL_H, 3, 2, false);
    }

    /**
     * 登录
     *
     * @param string $backUrl
     *
     * @return string|void|\yii\web\Response
     */
    public function actionLogin($backUrl = '')
    {
        if ($backUrl) {
            Yii::$app->user->setReturnUrl($backUrl);
        }

        $model = new LoginForm();

        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            Yii::$app->user->identity->signup_at = time();
            Yii::$app->user->identity->login_status = User::LOGIN_YES;
            Yii::$app->user->identity->save();

            return $this->goBack();
        }

        return $this->render('login', ['model' => $model]);
    }

    /**
     * 注销
     *
     * @return void|\yii\web\Response
     */
    public function actionLogout()
    {
        Yii::$app->session->set('goods_id', 0);
        Yii::$app->session->set('quantity', 0);
        Yii::$app->session->set('address_id', 0);

        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * 活动介绍
     *
     * @return string
     */
    public function actionAbout()
    {
        $this->header = false;

        return $this->render('about');
    }

    public function actionNotice()
    {
        return $this->render('notice');
    }
}
