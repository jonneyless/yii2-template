<?php
namespace wap\controllers;

use wap\models\Page;
use wap\models\SignupForm;
use wap\models\User;
use Yii;

/**
 * System controller
 */
class SystemController extends Controller
{

    public function actionFaq()
    {
        $this->bottomBar = false;
        $this->topBar = false;

        return $this->render('/site/page', Page::getPageById(2));
    }

    public function actionDownload()
    {
        $this->topBar = false;
        $this->bottomBar = false;
        
//        if(Yii::$app->user->getIsGuest()){
//            return $this->goBack();
//        }

        return $this->render('download');
    }

    public function actionShare($id)
    {
        $this->topBar = false;
        $this->bottomBar = false;

        return $this->render('share', [
            'id' => $id,
        ]);
    }

    public function actionPromotion($id)
    {
        $this->bottomBar = false;

        $referee = User::findOne($id);

        if(!$referee){
            return $this->message('推荐人不存在！', ['site/signup']);
        }

        $model = new SignupForm(['referee' => $referee->mobile]);

        if($model->load(Yii::$app->request->post()) && $model->signup()){
            Yii::$app->user->loginByAccessToken($model->user->access_token);

            return $this->redirect(['system/download']);
        }

        return $this->render('promotion', ['model' => $model]);
    }
}
