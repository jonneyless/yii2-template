<?php

namespace admin\models\form;

use admin\models\GoodsAttribute;
use admin\models\GoodsGallery;
use admin\models\GoodsInfo;
use admin\models\Product;
use admin\models\ProductGallery;
use ijony\helpers\File;
use ijony\helpers\Folder;
use ijony\helpers\Image;
use Yii;
use yii\base\ErrorException;
use yii\base\Model;
use admin\models\Goods as GoodsModel;
use yii\web\NotFoundHttpException;
use yii\web\UploadedFile;

/**
 * 商品搜索模型
 *
 * {@inheritdoc}
 *
 * @property string $goods_id ID
 * @property string $product_id 产品 ID
 * @property string $category_id 分类 ID
 * @property string $store_id 店铺 ID
 * @property string $store_category_id 店铺分类 ID
 * @property string $name 名称
 * @property string $preview 主图
 * @property string $number 编号
 * @property string $bar_code 条形码
 * @property string $original_price 商超价
 * @property string $cost_price 成本价
 * @property string $member_price 会员价
 * @property string $content 详情
 * @property string $weight 重量
 * @property int $shelves_at 上架时间
 * @property int $free_express 包邮件数
 * @property int $is_hot 热销
 * @property int $is_recommend 推荐
 * @property int $status 状态
 *
 * @property int $stock 库存
 * @property int $sell 销量
 *
 * @property array $galleries 组图
 * @property array $attrs 属性
 * @property array $mode 货品
 *
 * @property \admin\models\Goods $goods
 *
 */
class Goods extends Model
{

    public $goods_id = 0;
    public $product_id = 0;
    public $category_id = 0;
    public $store_id = 0;
    public $store_category_id = 0;
    public $name = '';
    public $preview = '';
    public $number = '';
    public $bar_code = '';
    public $original_price = 0.00;
    public $cost_price = 0.00;
    public $member_price = 0.00;
    public $content = '';
    public $weight = 0;
    public $shelves_at;
    public $free_express = 0;
    public $is_hot = GoodsModel::IS_HOT_UNACTIVE;
    public $is_recommend = GoodsModel::IS_RECOMMEND_UNACTIVE;
    public $status = GoodsModel::STATUS_ACTIVE;

    public $stock = 0;
    public $sell = 0;

    public $goods;

    public $galleries = [];
    public $attrs = [];
    public $mode = [
        'name' => '',
        'value' => [],
        'price' => [],
        'stock' => [],
        'image' => [],
    ];

    /**
     * @param int   $id
     * @param array $config
     *
     * @throws \yii\web\NotFoundHttpException
     */
    public function __construct($id = 0, array $config = [])
    {
        if($id){
            $model = GoodsModel::findOne($id);
            if(!$model){
                throw new NotFoundHttpException('商品不存在！');
            }
        }else{
            $model = new GoodsModel();
        }

        $this->goods = $model;

        parent::__construct($config);
    }

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        parent::init();

        if(!$this->goods->getIsNewRecord()){
            $this->setAttributes($this->goods->getAttributes());
            $this->setAttributes($this->goods->info->getAttributes());

            if($this->goods->attr){
                $attrs = [];
                $index = 0;
                $nameIndex = [];
                foreach($this->goods->attr as $attr){
                    if(!isset($nameIndex[$attr->name])){
                        $attrs['name'][$index] = $attr->name;
                        $nameIndex[$attr->name] = $index;
                        $index++;
                    }

                    $attrs['value'][$nameIndex[$attr->name]][] = $attr->value;
                }

                $attrs['value'] = array_map(function($value){
                    return join("\n", $value);
                }, $attrs['value']);

                $this->attrs = $attrs;
            }

            if($this->goods->gallery){
                $galleries = [];
                foreach($this->goods->gallery as $gallery){
                    $galleries['thumb'][] = Image::getImg($gallery->image, 340, 340);
                    $galleries['image'][] = $gallery->image;
                    $galleries['description'][] = $gallery->description;
                }

                $this->galleries = $galleries;
            }
        }

        $this->shelves_at = date('Y-m-d', $this->shelves_at ? $this->shelves_at : time());
    }

    /**
     * {@inheritdoc}
     */
    public function __call($name, $params)
    {
        if($this->goods->hasMethod($name)){
            return call_user_func_array([$this->goods, $name], $params);
        }

        return parent::__call($name, $params);
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['goods_id', 'product_id', 'category_id', 'store_id', 'store_category_id', 'free_express', 'is_hot', 'is_recommend', 'status'], 'integer'],
            [['original_price', 'cost_price', 'member_price', 'weight', 'bar_code', 'stock', 'sell'], 'number'],
            [['content', 'shelves_at'], 'string'],
            [['name'], 'string', 'max' => 100],
            [['preview'], 'string', 'max' => 150],
            [['number'], 'string', 'max' => 30],
            [['bar_code'], 'string', 'max' => 255],
            [['bar_code'], 'checkUniqueInStore'],
            [['galleries', 'attrs', 'mode'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'goods_id' => '商品 ID',
            'product_id' => '产品 ID',
            'category_id' => '分类',
            'store_id' => '店铺',
            'store_category_id' => '店铺分类',
            'name' => '名称',
            'preview' => '主图',
            'number' => '编号',
            'bar_code' => '条形码',
            'original_price' => '商超价',
            'cost_price' => '成本价',
            'member_price' => '会员价',
            'content' => '详情',
            'weight' => '重量',
            'shelves_at' => '上架时间',
            'free_express' => '包邮件数',
            'is_hot' => '热销',
            'is_recommend' => '推荐',
            'status' => '状态',

            'stock' => '库存',
            'sell' => '销量',

            'galleries' => '组图',
            'attrs' => '属性',
            'mode' => '货品',
        ];
    }

    /**
     * @param $attribute
     *
     * @return bool
     */
    public function checkUniqueInStore($attribute)
    {
        if($this->$attribute){
            $where[] = 'and';
            $where[] = ['<>', 'goods_id', $this->goods_id ? $this->goods_id : 0];
            $where[] = ['=', 'bar_code', $this->$attribute];
            $where[] = ['=', 'store_id', $this->store_id ? $this->store_id : 0];

            if(GoodsModel::find()->where($where)->exists()){
                if($this->store_id){
                    $this->addError('bar_code', '这个店铺已经有该条形码商品！');
                }else{
                    $this->addError('bar_code', '已经有该条形码商品！');
                }

                return false;
            }
        }

        return true;
    }

    /**
     * @param $barcode
     */
    public function setDataByProduct($barcode)
    {
        /* @var Product $product */
        $product = Product::find()->where(['bar_code' => $barcode])->one();
        if($product){
            $this->setAttributes($product->getAttributes());
            $this->product_id = $product->id;

            if($product->gallery){
                $galleries = [];
                foreach($product->gallery as $gallery){
                    $galleries['thumb'][] = Image::getImg($gallery->image, 340, 340);
                    $galleries['image'][] = $gallery->image;
                    $galleries['description'][] = $gallery->description;
                }

                $this->galleries = $galleries;
            }
        }
    }

    public function save()
    {
        $preview = UploadedFile::getInstance($this, 'preview');
        if($preview){
            $this->preview = File::newFile($preview->getExtension());
        }

        $transaction = Yii::$app->db->beginTransaction();

        try{
            $goods = $this->goods;
            $goods->setAttributes($this->getAttributes());

            $goods->shelves_at = strtotime($this->shelves_at);
            if(!$goods->save()){
                $this->addErrors($goods->getErrors());
            }

            $this->goods_id = $goods->goods_id;

            $info = $goods->info ? $goods->info : new GoodsInfo();
            $info->setAttributes($this->getAttributes());
            if(!$info->save()){
                $this->addErrors($info->getErrors());
            }

            if($this->hasErrors()){
                throw new ErrorException('字段校验失败！');
            }

            GoodsAttribute::deleteAll(['goods_id' => $this->goods_id]);

            if(isset($this->attrs['name'])){
                foreach($this->attrs['name'] as $key => $name){
                    if(!isset($this->attrs['value'][$key]) && $this->attrs['value'][$key]) continue;

                    $attr = new GoodsAttribute();
                    $attr->goods_id = $this->goods_id;
                    $attr->name = $name;

                    $values = explode("\n", $this->attrs['value'][$key]);
                    foreach($values as $value){
                        $attr->setIsNewRecord(true);
                        $attr->value = $value;
                        $attr->save();
                    }
                }
            }

            $galleries = GoodsGallery::findAll(['goods_id' => $this->goods_id]);
            foreach($galleries as $index => $gallery){
                if(!isset($this->galleries['image']) ||
                    !in_array($gallery->image, $this->galleries['image'])){
                    $gallery->delete();
                }
            }

            if(isset($this->galleries['image']) && $this->galleries['image']){
                foreach($this->galleries['image'] as $index => $image){
                    /* @var $gallery \admin\models\GoodsGallery */
                    $gallery = GoodsGallery::find()->where(['goods_id' => $this->goods_id, 'image' => $image])->one();
                    if(!$gallery){
                        $gallery = new GoodsGallery();
                        $gallery->goods_id = $this->goods_id;
                        $gallery->image = $image;
                    }
                    $gallery->description = $this->galleries['description'][$index];
                    $gallery->sort = $index;
                    $gallery->save();
                }
            }

            $transaction->commit();

            if($preview){
                $preview->saveAs(Folder::getStatic($this->preview));
            }
        }catch(ErrorException $e){
            $transaction->rollBack();

            return false;
        }

        try{
            $goods->sync();
        }catch(ErrorException $e){
            Yii::error($e->getMessage() ."\n" . $e->getFile() ."\n" . $e->getLine() ."\n" . $e->getTraceAsString(), 'sync');
        }

        return true;
    }
}
