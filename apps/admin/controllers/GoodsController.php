<?php

namespace admin\controllers;

use common\models\GoodsOutlet;
use common\models\GoodsVirtual;
use common\models\search\GoodsVirtual as GoodsVirtualSearch;
use libs\Utils;
use moonland\phpexcel\Excel;
use admin\models\GoodsForm;
use Yii;
use common\models\Goods;
use yii\base\ErrorException;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;

/**
 * GoodsController implements the CRUD actions for Goods model.
 */
class GoodsController extends Controller
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
     * Lists all Goods models.
     * @return mixed
     */
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => Goods::find()->where([
                'and',
                ['<>', 'status', Goods::STATUS_DELETED],
            ]),
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Goods model.
     *
     * @param string $id
     *
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Goods model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        /* @var \admin\models\GoodsForm */
        $model = new GoodsForm();

        if (Yii::$app->request->getIsPost()) {
            if ($model->load(Yii::$app->request->post()) && $model->validate() && $model->create()) {
                return $this->goBack();
            }
        } else {
            if (Yii::$app->request->getReferrer()) {
                Yii::$app->user->setReturnUrl(Yii::$app->request->getReferrer());
            }
        }

        return $this->render('create', ['model' => $model]);
    }

    /**
     * Updates an existing Goods model.
     * If update is successful, the browser will be redirected to the 'view' page.
     *
     * @param string $id
     *
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = new GoodsForm();
        $model->setDatas($this->findModel($id));

        if (Yii::$app->request->getIsPost()) {
            if ($model->load(Yii::$app->request->post()) && $model->validate() && $model->update()) {
                return $this->goBack();
            }
        } else {
            if (Yii::$app->request->getReferrer()) {
                Yii::$app->user->setReturnUrl(Yii::$app->request->getReferrer());
            }
        }

        return $this->render('update', ['model' => $model]);
    }

    /**
     * Deletes an existing Goods model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     *
     * @param string $id
     *
     * @return mixed
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        $model->status = Goods::STATUS_DELETED;
        $model->save();

        return $this->redirect(['index']);
    }

    public function actionRecycle()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => Goods::find()->where(['status' => Goods::STATUS_DELETED]),
        ]);

        return $this->render('recycle', [
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionVirtual($id)
    {
        $model = Goods::findOne($id);

        $searchModel = new GoodsVirtualSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, $id);

        return $this->render('virtual', [
            'model' => $model,
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionVirtualCreate($id)
    {
        $goods = Goods::findOne($id);

        /* @var \common\models\GoodsVirtual */
        $model = new GoodsVirtual();
        $model->goods_id = $id;

        if (Yii::$app->request->getIsPost()) {
            $model->id = GoodsVirtual::genId();

            if ($model->load(Yii::$app->request->post()) && $model->validate() && $model->save()) {
                $goods->stock = intval(GoodsVirtual::find()->where(['goods_id' => $goods->id, 'status' => GoodsVirtual::STATUS_UNUSE])->count());
                $goods->save();

                return $this->goBack();
            }
        } else {
            if (Yii::$app->request->getReferrer()) {
                Yii::$app->user->setReturnUrl(Yii::$app->request->getReferrer());
            }
        }

        return $this->render('virtual-create', ['model' => $model, 'goods' => $goods]);
    }

    public function actionVirtualImport($id)
    {
        $goods = Goods::findOne($id);

        /* @var \common\models\GoodsVirtual */
        $model = new GoodsVirtual();
        $model->goods_id = $id;

        if (Yii::$app->request->getIsPost()) {
            $file = UploadedFile::getInstance($model, 'file');
            if ($file) {
                $newFile = Utils::staticFolder(Utils::newBufferFile($file->getExtension()));
                $file->saveAs($newFile);
                $datas = Excel::import($newFile, ['getOnlySheet' => 'Sheet1']);
                if ($datas) {
                    foreach ($datas as $data) {
                        if (!isset($data['卡密']) || !$data['卡密']) {
                            continue;
                        }

                        $code = trim($data['卡密']);

                        $number = '';
                        if (isset($data['卡号']) && $data['卡号']) {
                            $number = trim($data['卡号']);
                        }

                        if (GoodsVirtual::find()->where(['goods_id' => $goods->id, 'number' => $number, 'code' => $code])->exists()) {
                            continue;
                        }

                        $virtual = new GoodsVirtual();
                        $virtual->id = GoodsVirtual::genId();
                        $virtual->goods_id = $goods->id;
                        $virtual->number = $number;
                        $virtual->code = $code;

                        if (isset($data['生效日期']) && $data['生效日期']) {
                            $virtual->begin_time = trim($data['生效日期']);
                        }

                        if (isset($data['失效日期']) && $data['失效日期']) {
                            $virtual->end_time = trim($data['失效日期']);
                        }

                        if (isset($data['状态']) && $data['状态']) {
                            $virtual->status = trim($data['状态']) == '未使用' ? 0 : 1;
                        }

                        if (!$virtual->save()) {
                            throw new ErrorException('虚拟卡号保存失败！');
                        }
                    }
                }

                $goods->stock = intval(GoodsVirtual::find()->where(['goods_id' => $goods->id, 'status' => GoodsVirtual::STATUS_UNUSE])->count());
                $goods->save();

                Utils::clearBuffer();

                return $this->goBack();
            } else {
                $model->addError('file', '请选择导入文件！');
            }
        } else {
            if (Yii::$app->request->getReferrer()) {
                Yii::$app->user->setReturnUrl(Yii::$app->request->getReferrer());
            }
        }

        return $this->render('virtual-import', ['model' => $model, 'goods' => $goods]);
    }

    public function actionVirtualExport($id)
    {
        $model = Goods::findOne($id);
        $datas = GoodsVirtual::find()->where(['goods_id' => $id])->orderBy(['status' => SORT_DESC])->all();

        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . $model->name . '_虚拟卡.csv"');
        header('Cache-Control: max-age=0');

        $fp = fopen('php://output', 'a');

        $head = ['卡号', '卡密', '拼单 ID', '拼单类型', '生效日期', '失效日期', '状态'];
        foreach ($head as $i => $v) {
            $head[$i] = iconv('utf-8', 'gbk', $v);
        }
        fputcsv($fp, $head);

        $index = 0;
        $limit = 5000;

        /* @var $data \common\models\GoodsVirtual */
        foreach ($datas as $data) {
            $index++;

            if ($limit == $index) {
                ob_flush();
                flush();
                $index = 0;
            }

            $csv = [];
            $csv[] = (string) "\t" . iconv('utf-8', 'gbk', $data->number);
            $csv[] = (string) "\t" . iconv('utf-8', 'gbk', $data->code);
            $csv[] = (string) "\t" . iconv('utf-8', 'gbk', $data->group_id ? '拼单 #' . $data->group_id : '');
            $csv[] = (string) "\t" . iconv('utf-8', 'gbk', $data->group_id ? $data->group->quantity . '人拼' : '');
            $csv[] = (string) "\t" . $data->begin_time;
            $csv[] = (string) "\t" . $data->end_time;
            $csv[] = (string) "\t" . iconv('utf-8', 'gbk', $data->status == GoodsVirtual::STATUS_USED ? '已使用' : '未使用');

            fputcsv($fp, $csv);
        }

        fclose($fp);
        die();
    }

    public function actionVirtualDeleteAll($id)
    {
        GoodsVirtual::deleteAll(['goods_id' => $id]);

        $this->redirect(['virtual', 'id' => $id]);
    }

    public function actionVirtualDelete($id)
    {
        $virtual = GoodsVirtual::findOne($id);

        if (!$virtual) {
            return $this->goBack();
        }

        $virtual->delete();

        $this->redirect(['virtual', 'id' => $virtual->goods_id]);
    }

    public function actionOutlet($id)
    {
        $model = Goods::findOne($id);

        if (!$model) {
            return $this->goBack();
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $model->getOutlet(),
        ]);

        return $this->render('outlet', [
            'model' => $model,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionOutletImport($id)
    {
        $goods = Goods::findOne($id);

        /* @var \common\models\GoodsOutlet */
        $model = new GoodsOutlet();
        $model->goods_id = $id;

        if (Yii::$app->request->getIsPost()) {
            $file = UploadedFile::getInstance($model, 'file');
            if ($file) {
                GoodsOutlet::deleteAll(['goods_id' => $goods->id]);
                $newFile = Utils::staticFolder(Utils::newBufferFile($file->getExtension()));
                $file->saveAs($newFile);
                $datas = Excel::import($newFile, ['getOnlySheet' => 'Sheet1']);
                if ($datas) {
                    foreach ($datas as $data) {
                        if (!isset($data['门店名称']) || !isset($data['地址'])) {
                            $model->addError('file', '数据格式错误，确定是“城市”,“区域”,“门店名称”,“地址”的结构嘛！');
                        }

                        $model = new GoodsOutlet();
                        $model->goods_id = $goods->id;
                        $model->city = isset($data['城市']) ? $data['城市'] : '';
                        $model->district = isset($data['区域']) ? $data['区域'] : '';
                        $model->name = $data['门店名称'];
                        $model->address = $data['地址'];
                        $model->save();
                    }
                } else {
                    $model->addError('file', '确定存在名为 Sheet1 的表嘛？');
                }

                Utils::clearBuffer();

                if (!$model->hasErrors()) {
                    return $this->goBack();
                }
            } else {
                $model->addError('file', '请选择导入文件！');
            }
        } else {
            if (Yii::$app->request->getReferrer()) {
                Yii::$app->user->setReturnUrl(Yii::$app->request->getReferrer());
            }
        }

        return $this->render('outlet-import', ['model' => $model, 'goods' => $goods]);
    }

    /**
     * Finds the Goods model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return Goods the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Goods::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
