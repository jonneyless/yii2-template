<?php

namespace api\modules\v1\controllers\user;

use api\controllers\ApiController;
use api\models\UserIncome;
use ijony\helpers\Image;
use Yii;
use yii\data\ActiveDataProvider;
use api\filters\QueryParamAuth;

class RewardController extends ApiController
{

    public $modelClass = 'api\models\Service';

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

    public function actionIndex()
    {
        /* @var $user \api\models\User */
        $user = Yii::$app->user->identity;

        $total = $user->getIncome()->sum('amount');
        $today = $user->getIncome()->where(['between', 'created_at', strtotime('today'), strtotime('tomorrow')])->sum('amount');
        $month = $user->getIncome()->where(['between', 'created_at', strtotime(date("Y-m-1")), strtotime(date("Y-m-1") . '+1 month')])->sum('amount');

        return [
            'username' => $user->username,
            'avatar' => Image::getImg($user->avatar, 0, 0, 'default-avatar.gif'),
            'created_at' => date("Y-m-d", $user->created_at),
            'total' => $total ? sprintf('%.02f', $total) : '0.00',
            'today' => $today ? sprintf('%.02f', $today) : '0.00',
            'month' => $month ? sprintf('%.02f', $month) : '0.00',
        ];
    }

    public function actionMonth()
    {
        /* @var $user \api\models\User */
        $user = Yii::$app->user->identity;

        $year = Yii::$app->request->get('year', date('Y'));
        $month = Yii::$app->request->get('month', date('m'));
        $params = Yii::$app->getRequest()->getQueryParams();

        $begin = sprintf('%d-%d-1', $year, $month);

        $total_reward = $user->getIncome()->where(['between', 'created_at', strtotime($begin), strtotime($begin . '+1 month')])->sum('amount');
        $date_group = $user->getIncome()->select('date')->groupBy('date')->column();

        sort($date_group);

        $options = [];
        foreach($date_group as $data){
            list($year, $month) = explode("-", $data);
            if(!isset($options[$year])){
                $options[$year] = [
                    'name' => $year . '年',
                    'value' => (string) $year,
                ];
            }

            $options[$year]['childs'][] = [
                'name' => $month . '月',
                'value' => (string) $month,
            ];
        }

        sort($options);

        $dataProvider = new ActiveDataProvider([
            'query' => $user->getIncome()->where(['between', 'created_at', strtotime($begin), strtotime($begin . '+1 month')]),
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

        $list = array_map(function($data){
            return $data->buildListData();
        }, $dataProvider->getModels());

        return [
            'total_reward' => $total_reward ? sprintf('%.02f', $total_reward) : '0.00',
            'option' => $options,
            'list' => $list,
        ];
    }

    public function actionDetail()
    {
        /* @var $user \api\models\User */
        $user = Yii::$app->user->identity;

        $params = Yii::$app->getRequest()->getQueryParams();

        $dataProvider = new ActiveDataProvider([
            'query' => $user->getIncome(),
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

        $list = array_map(function($data){
            return $data->buildListData();
        }, $dataProvider->getModels());

        $total_reward = $user->getIncome()->sum('amount');

        return [
            'total_reward' => $total_reward ? sprintf('%.02f', $total_reward) : '0.00',
            'list' => $list
        ];
    }

    public function actionMember()
    {
        /* @var $user \api\models\User */
        $user = Yii::$app->user->identity;

        $params = Yii::$app->getRequest()->getQueryParams();

        $dataProvider = new ActiveDataProvider([
            'query' => $user->getMember(),
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

        $user_id = $user->user_id;

        $list = array_map(function($data)use($user_id){
            return $data->buildRewardData($user_id);
        }, $dataProvider->getModels());

        $total_reward = $user->getIncome()->where(['type' => UserIncome::TYPE_DIRECT])->sum('amount');

        return [
            'total_person' => intval($user->getMember()->count()),
            'total_reward' => $total_reward ? sprintf('%.02f', $total_reward) : '0.00',
            'list' => $list
        ];
    }

    public function actionAccount()
    {
        if(Yii::$app->user->getIsGuest()){
            return null;
        }

        $info = Yii::$app->user->identity->info;

        return [
            'truename' => $info->truename,
            'idcard' => $info->idcard,
            'mobile' => $info->mobile,
            'bankno' => $info->bankno,
            'bankname' => $info->bankname,
            'can_modify' => $info->can_modify == 1,
        ];
    }

    public function actionUpdateAccount()
    {
        if(Yii::$app->user->getIsGuest()){
            return null;
        }

        $info = Yii::$app->user->identity->info;

        $truename = Yii::$app->request->getBodyParam('truename');
        $idcard = Yii::$app->request->getBodyParam('idcard');
        $mobile = Yii::$app->request->getBodyParam('mobile');
        $bankno = Yii::$app->request->getBodyParam('bankno');
        $bankname = Yii::$app->request->getBodyParam('bankname');
        $vcode = Yii::$app->request->getBodyParam('vcode');

        if(!$vcode){
            return [
                'error' => '请填写验证码！',
            ];
        }

        $code = Yii::$app->cache->get('api_vcode_' . $mobile);

        if(!$code || $code != $vcode){
            return [
                'error' => '验证码错误！',
            ];
        }

        if($info->can_modify == 0){
            return [
                'error' => '不允许修改！',
            ];
        }

        $info->truename = $truename;
        $info->idcard = $idcard;
        $info->mobile = $mobile;
        $info->bankno = $bankno;
        $info->bankname = $bankname;
        $info->can_modify = 0;
        $info->save();

        return [
            'truename' => $info->truename,
            'idcard' => $info->idcard,
            'mobile' => $info->mobile,
            'bankno' => $info->bankno,
            'bankname' => $info->bankname,
            'can_modify' => $info->can_modify == 1,
        ];
    }
}