<?php

namespace admin\controllers;

use ijony\helpers\File;
use ijony\helpers\Folder;
use Yii;
use admin\models\Teacher;
use yii\data\ActiveDataProvider;
use yii\web\NotFoundHttpException;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;

/**
 * 老师管理类
 *
 * @auth_key  teacher
 * @auth_name 老师管理
 */
class TeacherController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => $this->getRules('teacher'),
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
     * 老师列表
     *
     * @auth_key    *
     * @auth_parent teacher
     *
     * @return string
     */
    public function actionIndex()
    {
        $query = Teacher::find()->where(['status' => Teacher::STATUS_ACTIVE]);

        if ($this->store_id) {
            $query->andWhere(['store_id' => $this->store_id]);
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * 老师详情
     *
     * @auth_key    teacher_view
     * @auth_name   查看老师
     * @auth_parent teacher
     *
     * @param $id
     *
     * @return string|\yii\web\Response
     * @throws \yii\web\NotFoundHttpException
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * 添加老师
     *
     * @auth_key    teacher_create
     * @auth_name   添加老师
     * @auth_parent teacher
     *
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new Teacher();
        $model->status = Teacher::STATUS_ACTIVE;

        if ($model->load(Yii::$app->request->post())) {
            $avatar = UploadedFile::getInstance($model, 'avatar');
            if ($avatar) {
                $model->avatar = File::newFile($avatar->getExtension());
            }

            if ($model->validate() && $model->save()) {
                if ($avatar) {
                    $avatar->saveAs(Folder::getStatic($model->avatar));
                }

                return $this->redirect(['view', 'id' => $model->teacher_id]);
            }
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * 编辑老师
     *
     * @auth_key    teacher_update
     * @auth_name   编辑老师
     * @auth_parent teacher
     *
     * @param $id
     *
     * @return string|\yii\web\Response
     * @throws \yii\web\NotFoundHttpException
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post())) {
            $avatar = UploadedFile::getInstance($model, 'avatar');
            if ($avatar) {
                $model->avatar = File::newFile($avatar->getExtension());
            }

            if ($model->validate() && $model->save()) {
                if ($avatar) {
                    $avatar->saveAs(Folder::getStatic($model->avatar));
                }

                return $this->redirect(['view', 'id' => $model->teacher_id]);
            }
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * 移除老师
     *
     * @auth_key    teacher_remove
     * @auth_name   移除老师
     * @auth_parent teacher
     *
     * @param $id
     *
     * @return \yii\web\Response
     * @throws \yii\web\NotFoundHttpException
     */
    public function actionRemove($id)
    {
        $model = $this->findModel($id);
        $model->status = $model::STATUS_DELETE;
        $model->save();

        return $this->redirect(['index']);
    }

    /**
     * 老师回收站
     *
     * @auth_key    teacher_recycle
     * @auth_name   老师回收站
     * @auth_parent teacher
     *
     * @return string
     */
    public function actionRecycle()
    {
        $query = Teacher::find()->where(['status' => Teacher::STATUS_DELETE]);

        if ($this->store_id) {
            $query->andWhere(['store_id' => $this->store_id]);
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        return $this->render('recycle', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * 恢复老师
     *
     * @auth_key    teacher_recycle
     * @auth_name   老师回收站
     * @auth_parent teacher
     *
     * @param $id
     *
     * @return \yii\web\Response
     * @throws \yii\web\NotFoundHttpException
     */
    public function actionRestore($id)
    {
        $model = $this->findModel($id);
        $model->status = $model::STATUS_ACTIVE;
        $model->save();

        return $this->redirect(['recycle']);
    }

    /**
     * 删除老师
     *
     * @auth_key    teacher_recycle
     * @auth_name   老师回收站
     * @auth_parent teacher
     *
     * @param $id
     *
     * @return \yii\web\Response
     * @throws \Exception
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     * @throws \yii\web\NotFoundHttpException
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['recycle']);
    }

    /**
     * 通用模型查询
     *
     * @param $id
     *
     * @return \admin\models\Teacher
     * @throws NotFoundHttpException
     */
    protected function findModel($id)
    {
        if (($model = Teacher::findOne($id)) !== null) {
            if (!$this->store_id || $model->store_id == $this->store_id) {
                return $model;
            }

            throw new NotFoundHttpException('Trequested page does not exist.');
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
