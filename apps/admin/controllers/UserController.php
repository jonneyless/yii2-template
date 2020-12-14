<?php

namespace admin\controllers;

use libs\Utils;
use moonland\phpexcel\Excel;
use Yii;
use common\models\User;
use common\models\search\User as UserSearch;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;
use yii\filters\AccessControl;

/**
 * UserController implements the CRUD actions for User model.
 */
class UserController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'matchCallback' => function ($rule, $action) {
                            return !Yii::$app->user->getIsGuest() && Yii::$app->user->id == 1;
                        },
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all User models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new UserSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionVip()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => User::find()->where(['is_vip' => User::IS_VIP_YES]),
        ]);

        return $this->render('vip', [
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionImport()
    {
        $model = new User();

        if (Yii::$app->request->getIsPost()) {
            $file = UploadedFile::getInstance($model, 'file');
            if ($file) {
                $newFile = Utils::staticFolder(Utils::newBufferFile($file->getExtension()));
                $file->saveAs($newFile);
                $datas = Excel::import($newFile);
                if ($datas) {
                    foreach ($datas as $data) {
                        if (!isset($data['电话']) || !$data['电话']) {
                            continue;
                        }

                        $model = User::find()->where(['mobile' => (string) $data['电话']])->one();

                        if (!$model) {
                            $model = new User();
                            $model->mobile = (string) $data['电话'];
                            $model->generateAuthKey();
                        }

                        $model->is_vip = User::IS_VIP_YES;
                        if (!$model->save()) {
                            Utils::dump($model->getErrors());
                        }
                    }
                }

                Utils::clearBuffer();

                return $this->redirect(['index']);
            } else {
                $model->addError('file', '请选择要导入的文件！');
            }
        }

        return $this->render('import', [
            'model' => $model,
        ]);
    }
}
