<?php

namespace api\modules\v1\controllers\user;

use api\controllers\ApiController;
use api\models\UserAddress;
use Yii;
use api\filters\QueryParamAuth;
use yii\web\UnauthorizedHttpException;

class AddressController extends ApiController
{

    public $modelClass = 'api\models\UserAddress';

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

        unset($actions['delete']);

        return $actions;
    }

    public function checkAccess($action, $model = NULL, $params = [])
    {
        parent::checkAccess($action, $model, $params);

        if($model !== NULL && $model->user_id !== Yii::$app->user->id){
            switch($action){
                case 'update':

                    throw new UnauthorizedHttpException('你不能修改别人的收货地址！');

                    break;
                case 'view':

                    throw new UnauthorizedHttpException('你不能查看别人的收货地址！');

                    break;
                case 'delete':

                    throw new UnauthorizedHttpException('你不能删除别人的收货地址！');

                    break;
            }
        }
    }

    public function actionDelete($id)
    {
        $address = UserAddress::find()->where(['address_id' => $id, 'user_id' => Yii::$app->user->id])->one();

        if(!$address){
            throw new BadRequestHttpException('收货地址不存在！');
        }

        $address->delete();

        return [
            'message' => '收货地址删除成功。',
        ];
    }
}