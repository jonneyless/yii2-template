<?php

namespace api\modules\v1\controllers\user;

use api\controllers\ApiController;
use api\models\Performance;
use api\models\User;
use api\models\UserIncome;
use ijony\helpers\Image;
use Yii;
use yii\data\ActiveDataProvider;
use api\filters\QueryParamAuth;

class PerformanceController extends ApiController
{

    public $modelClass = 'api\models\Performance';

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
        $year = Yii::$app->request->get('year', date("Y"));
        $month = Yii::$app->request->get('month', date('m'));

        $person = Performance::getRewardForPerson(Yii::$app->user->id, $year, $month);
        $difference = Performance::getRewardForDifference(Yii::$app->user->id, $person, $year, $month);
        $group = Performance::getRewardForGroup(Yii::$app->user->id, $year, $month);

        $company = Performance::getRewardForCompany(Yii::$app->user->id, $year, $month);
        $companyDifference = Performance::getRewardForCompanyDifference(Yii::$app->user->id, $year, $month);

        $recommendCompany = Performance::getRewardForDirectCompany(Yii::$app->user->id, $year, $month);
        $recommendCity = Performance::getRewardForDirectCity(Yii::$app->user->id, $year, $month);

        $total = $person + $difference + $group + $company + $companyDifference;
        $personTotal = $person + $difference + $group;
        $companyTotal = $company + $companyDifference;
        $recommend = $recommendCompany + $recommendCity;

        return [
            'date' => $year . '-' . $month,
            'commission' => sprintf('%0.2f', $total),
            'detail' => [
                'person' => sprintf('%0.2f', $personTotal),
                'company' => sprintf('%0.2f', $companyTotal),
                'city' => '0.00',
                'recommend' => sprintf('%0.2f', $recommend),
            ],
        ];
    }

    public function actionPerson()
    {
        $year = Yii::$app->request->get('year', date("Y"));
        $month = Yii::$app->request->get('month', date('m'));

        $person = Performance::getRewardForPerson(Yii::$app->user->id, $year, $month);
        $difference = Performance::getRewardForDifference(Yii::$app->user->id, $year, $month);
        $group = Performance::getRewardForGroup(Yii::$app->user->id, $year, $month);

        return [
            'person' => sprintf('%0.2f', $person),
            'group' => sprintf('%0.2f', $group),
            'different' => sprintf('%0.2f', $difference),
            'list' => [
//                [
//                    'name' => '何小姐',
//                    'amount' => '2100.00',
//                    'rate' => '0.20',
//                    'different' => '0.00',
//                ],
//                [
//                    'name' => '邓大富',
//                    'amount' => '2100.00',
//                    'rate' => '0.2',
//                    'different' => '0.00',
//                ],
            ],
        ];
    }

    public function actionCompany()
    {
        $year = Yii::$app->request->get('year', date("Y"));
        $month = Yii::$app->request->get('month', date('m'));

        $company = Performance::getRewardForCompany(Yii::$app->user->id, $year, $month);
        $difference = Performance::getRewardForCompanyDifference(Yii::$app->user->id, $year, $month);

        return [
            'company' => sprintf('%0.2f', $company),
            'different' => sprintf('%0.2f', $difference),
            'list' => [
//                [
//                    'name' => '何小姐',
//                    'amount' => '2100.00',
//                    'rate' => '0.2',
//                    'different' => '0.00',
//                ],
//                [
//                    'name' => '邓大富',
//                    'amount' => '2100.00',
//                    'rate' => '0.2',
//                    'different' => '0.00',
//                ],
            ],
        ];
    }

    public function actionPersonList()
    {
        $year = Yii::$app->request->get('year', date("Y"));
        $month = Yii::$app->request->get('month', date('m'));

        return [
            'total' => Performance::getRewardForAll($year, $month),
            'list' => [
//                [
//                    'rank' => 1,
//                    'name' => '何小姐',
//                    'amount' => '2100.00',
//                    'level' => 0,
//                ],
//                [
//                    'rank' => 2,
//                    'name' => '邓大富',
//                    'amount' => '2100.00',
//                    'level' => 0,
//                ],
            ],
        ];
    }

    public function actionCompanyList()
    {
        $year = Yii::$app->request->get('year', date("Y"));
        $month = Yii::$app->request->get('month', date('m'));

        return [
            'total' => Performance::getRewardForAll($year, $month),
            'list' => [
//                [
//                    'rank' => 1,
//                    'name' => '团队1',
//                    'amount' => '2100.00',
//                ],
//                [
//                    'rank' => 2,
//                    'name' => '团队2',
//                    'amount' => '2100.00',
//                ],
            ],
        ];
    }

    public function actionChild()
    {
        $year = Yii::$app->request->get('year', date("Y"));
        $month = Yii::$app->request->get('month', date('m'));

        return [
            'company' => Performance::getRewardForDirectCompany(Yii::$app->user->id, $year, $month),
            'city' => Performance::getRewardForDirectCity(Yii::$app->user->id, $year, $month),
            'list' => [
//                [
//                    'name' => '何小姐',
//                    'type' => '公司',
//                    'offline_amount' => '2100.00',
//                    'online_amount' => '2100.00',
//                ],
//                [
//                    'name' => '邓大富',
//                    'type' => '公司',
//                    'offline_amount' => '2100.00',
//                    'online_amount' => '2100.00',
//                ],
            ],
        ];
    }

    public function actionChildCity()
    {
        return [
            'total' => '0.00',
            'list' => [
//                [
//                    'order_id' => 'ABC123456',
//                    'quantity' => 1,
//                    'amount' => '210.00',
//                ],
//                [
//                    'order_id' => 'ABC245457',
//                    'quantity' => 1,
//                    'amount' => '210.00',
//                ],
            ],
        ];
    }

    public function actionInvitation()
    {
        $users = User::find()->where(['referee' => Yii::$app->user->id])->all();

        $list = array_map(function ($user) {
            /* @var \api\models\User $user */
            return [
                'date' => date("Y-m-d", $user->created_at),
                'name' => $user->username,
            ];
        }, $users);

        return [
            'list' => $list,
        ];
    }
}