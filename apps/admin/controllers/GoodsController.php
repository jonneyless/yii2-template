<?php

namespace admin\controllers;

use admin\models\OrderGoods;
use Yii;
use admin\models\Goods;
use admin\models\form\Goods as GoodsForm;
use admin\models\search\Goods as GoodsSeach;
use yii\web\NotFoundHttpException;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;

/**
 * 商品管理类
 *
 * @auth_key    goods
 * @auth_name   商品管理
 */
class GoodsController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => $this->getRules('goods'),
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
     * 商品列表
     *
     * @auth_key    *
     * @auth_parent goods
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new GoodsSeach([
            'status' => [
                Goods::STATUS_ACTIVE,
                Goods::STATUS_UNACTIVE,
            ]
        ]);
        $dataProvider = $searchModel->search(Yii::$app->request->getQueryParams(), $this->store_id);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * 商品详情
     *
     * @auth_key    goods_view
     * @auth_name   商品详情
     * @auth_parent goods
     *
     * @param $id
     *
     * @return string
     * @throws \yii\web\NotFoundHttpException
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);

        if(!$model->checkStore($this->store_id)){
            return $this->message('对不起，你没有操作权限！');
        }

        return $this->render('view', [
            'model' => $model,
        ]);
    }

    /**
     * 添加商品
     *
     * @auth_key    goods_ceate
     * @auth_name   添加商品
     * @auth_parent goods
     *
     * @param string $barcode
     *
     * @return string|\yii\web\Response
     * @throws \yii\web\NotFoundHttpException
     */
    public function actionCreate($barcode = '')
    {
        $model = new GoodsForm();

        if($this->store_id){
            $model->store_id = $this->store_id;
            $model->goods->store_id = $this->store_id;
        }

        if($model->load(Yii::$app->request->post())){
            if($model->validate() && $model->save()){
                return $this->redirect(['view', 'id' => $model->goods_id]);
            }
        }

        $model->setDataByProduct($barcode);

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * 编辑商品
     *
     * @auth_key    goods_update
     * @auth_name   编辑商品
     * @auth_parent goods
     *
     * @param $id
     *
     * @return string|\yii\web\Response
     * @throws \yii\web\NotFoundHttpException
     */
    public function actionUpdate($id, $barcode = '')
    {
        $model = new GoodsForm($id);

        if(!$model->checkStore($this->store_id)){
            return $this->message('对不起，你没有操作权限！');
        }

        if($model->load(Yii::$app->request->post())){
            if($model->validate() && $model->save()){
                return $this->redirect(['view', 'id' => $model->goods_id]);
            }
        }

        $model->setDataByProduct($barcode);

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * 导出商品
     *
     * @auth_key    goods_export
     * @auth_name   导出商品
     * @auth_parent goods
     *
     * @return string|\yii\web\Response
     * @throws \yii\base\ExitException
     */
    public function actionExport()
    {
        set_time_limit(0);

        $query = Goods::find()->where(['status' => Goods::STATUS_ACTIVE]);

        if($this->store_id){
            $query->andFilterWhere(['store_id' => $this->store_id]);
        }

        $data = $query->all();

        header('Content-Type: application/vnd.ms-excel' );
        header('Content-Disposition: attachment;filename="laijiusheng_goods.csv"' );
        header('Cache-Control: max-age=0' );

        $fp = fopen ('php://output', 'a');

        fwrite($fp, chr(0xEF).chr(0xBB).chr(0xBF));

        $head = [
            '名称（必填）',
            '分类（必填）',
            '条码',
            '规格',
            '主单位',
            '库存量',
            '进货价（必填）',
            '销售价（必填）',
            '批发价',
            '会员价',
            '会员折扣',
            '积分商品',
            '库存上限',
            '库存下限',
            '品牌',
            '供货商',
            '生产日期',
            '保质期',
            '拼音码',
            '测试',
            '自定义1',
            '自定义3',
            '自定义4',
            '商品状态',
            '商品描述',
            '服务类商品'
        ];
        fputcsv($fp, $head);

        $index = 0;
        $limit = 5000;

        foreach ($data as $datum){
            $index++;

            if($limit == $index){
                ob_flush ();
                flush ();
                $index = 0;
            }

            fputcsv($fp, $datum->getExportData());
        }

        fclose($fp);

        Yii::$app->end();
    }

    /**
     * 移除商品
     *
     * @auth_key    goods_remove
     * @auth_name   移除商品
     * @auth_parent goods
     *
     * @param $id
     *
     * @return \yii\web\Response
     * @throws \yii\web\NotFoundHttpException
     */
    public function actionRemove($id)
    {
        $model = $this->findModel($id);

        if(!$model->checkStore($this->store_id)){
            return $this->message('对不起，你没有操作权限！');
        }

        $model->status = $model::STATUS_DELETE;
        $model->save();

        return $this->redirect(['index']);
    }

    /**
     * 商品回收站
     *
     * @auth_key    goods_recycle
     * @auth_name   商品回收站
     * @auth_parent goods
     *
     * @return string
     */
    public function actionRecycle()
    {
        $searchModel = new GoodsSeach([
            'status' => Goods::STATUS_DELETE
        ]);
        $dataProvider = $searchModel->search(Yii::$app->request->getQueryParams(), $this->store_id);

        return $this->render('recycle', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * 恢复商品
     *
     * @auth_key    goods_recycle
     * @auth_name   商品回收站
     * @auth_parent goods
     *
     * @param $id
     *
     * @return \yii\web\Response
     * @throws \yii\web\NotFoundHttpException
     */
    public function actionRestore($id)
    {
        $model = $this->findModel($id);

        if(!$model->checkStore($this->store_id)){
            return $this->message('对不起，你没有操作权限！');
        }

        $model->status = $model::STATUS_ACTIVE;
        $model->save();

        return $this->redirect(['recycle']);
    }

    /**
     * 删除商品
     *
     * @auth_key    goods_recycle
     * @auth_name   商品回收站
     * @auth_parent goods
     *
     * @param $id
     *
     * @return string
     * @throws \Exception
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     * @throws \yii\web\NotFoundHttpException
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);

        if(!$model->checkStore($this->store_id)){
            return $this->message('对不起，你没有操作权限！');
        }

        $model->delete();

        return $this->redirect(['recycle']);
    }

    /**
     * 商品审核
     *
     * @auth_key    goods_approval
     * @auth_name   商品审核
     * @auth_parent goods
     *
     * @return string
     */
    public function actionApproval()
    {
        $searchModel = new GoodsSeach([
            'status' => Goods::STATUS_OFFLINE
        ]);
        $dataProvider = $searchModel->search(Yii::$app->request->getQueryParams(), $this->store_id);

        return $this->render('approval', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }


    /**
     * 商品列表
     *
     * @auth_key    *
     * @auth_parent goods
     *
     * @return string
     */
    public function actionClear()
    {
        set_time_limit(-1);

        $goodsIds = OrderGoods::find()->select('goods_id')->column();
        $goodses = Goods::find()->where([
            'and',
            ['=', 'status', Goods::STATUS_DELETE],
            ['not in', 'goods_id', $goodsIds],
        ])->all();

        foreach($goodses as $goods){
            $goods->delete();
        }
    }

    /**
     * 通用模型查询
     *
     * @param $id
     *
     * @return \admin\models\Goods
     * @throws NotFoundHttpException
     */
    protected function findModel($id)
    {
        if(($model = Goods::findOne($id)) !== NULL){
            return $model;
        }else{
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
