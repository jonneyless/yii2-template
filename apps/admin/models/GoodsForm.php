<?php

namespace admin\models;

use common\models\Area;
use common\models\GoodsGroup;
use common\models\Goods;
use common\models\GoodsAttribute;
use common\models\GoodsGallery;
use common\models\GoodsOutlet;
use common\models\GoodsVirtual;
use libs\Utils;
use moonland\phpexcel\Excel;
use Yii;
use yii\base\ErrorException;
use yii\base\Model;
use yii\web\UploadedFile;

/**
 * This is the model class for table "{{%goods}}".
 *
 * @property string $id
 * @property string $category_id
 * @property string $name
 * @property string $sub_name
 * @property string $preview
 * @property string $stock
 * @property string $sales
 * @property string $cost
 * @property string $price
 * @property string $description
 * @property string $content
 * @property integer $status
 *
 * @property \common\models\Goods $goods
 * @property \common\models\GoodsGallery[] $galleries
 */
class GoodsForm extends Model
{
    public $id;
    public $category_id;
    public $name;
    public $sub_name;
    public $preview;
    public $stock = 1;
    public $sales = 0;
    public $cost = 0.00;
    public $price = 0.00;
    public $description;
    public $content;
    public $status = Goods::STATUS_SHELVE;
    /* 阶梯 */
    public $group = [];
    /* 组图 */
    public $gallery = [];
    /* 属性 */
    public $attrs = [];
    public $goods;
    public $galleries;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['category_id', 'name', 'preview', 'price'], 'required'],
            [['category_id', 'stock', 'sales', 'status'], 'integer'],
            [['cost', 'price'], 'number'],
            [['content'], 'string'],
            [['name'], 'string', 'max' => 150],
            [['preview'], 'string', 'max' => 100],
            [['description', 'sub_name'], 'string', 'max' => 255],
            [['group', 'gallery', 'attrs'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => '商品 ID',
            'category_id' => '分类 ID',
            'name' => '商品名称',
            'sub_name' => '商品提示',
            'preview' => '预览图',
            'stock' => '库存',
            'sales' => '销量',
            'cost' => '成本',
            'price' => '单价',
            'description' => '商品简介',
            'content' => '商品详情',
            'status' => '状态',
        ];
    }

    public function setDatas($model)
    {
        $this->setAttributes($model->getAttributes());

        $this->goods = $model;
        $this->galleries = $model->gallery;

        /* @var $attrs \common\models\GoodsAttribute[] */
        $attrs = GoodsAttribute::find()->where(['goods_id' => $model->id])->all();
        $this->attrs = [];
        foreach ($attrs as $attr) {
            $this->attrs['name'][] = $attr->name;
            $this->attrs['value'][] = $attr->value;
        }

        /* @var $groups \common\models\GoodsGroup[] */
        $groups = GoodsGroup::find()->where(['goods_id' => $model->id])->all();
        $this->group = [];
        foreach ($groups as $group) {
            $this->group['quantity'][] = $group->quantity;
            $this->group['daily'][] = $group->daily;
            $this->group['delivery'][] = $group->delivery;
            $this->group['cost'][] = $group->cost;
            $this->group['leader'][] = $group->leader;
            $this->group['price'][] = $group->price;
            $this->group['stock'][] = $group->stock;
        }
    }

    public function create()
    {
        try {
            $transaction = Yii::$app->db->beginTransaction();

            $goods = new Goods();
            $goods->setAttributes($this->getAttributes());
            if (!$goods->save()) {
                $this->addErrors($goods->getErrors());
                throw new ErrorException('商品保存失败！');
            }

            if (isset($this->gallery['image']) && $this->gallery['image']) {
                foreach ($this->gallery['image'] as $index => $image) {
                    $gallery = new GoodsGallery();
                    $gallery->goods_id = $goods->id;
                    $gallery->image = $image;
                    $gallery->sort = $index;
                    $gallery->save();
                }
            }

            if (isset($this->attrs['name'])) {
                foreach ($this->attrs['name'] as $index => $name) {
                    if (!$name) {
                        continue;
                    }
                    if (!isset($this->attrs['value'][$index])) {
                        continue;
                    }

                    $attr = new GoodsAttribute();
                    $attr->goods_id = $goods->id;
                    $attr->name = $name;
                    $attr->value = $this->attrs['value'][$index];
                    $attr->save();
                }
            }

            $goods->save();

            $transaction->commit();

            Utils::clearBuffer();

            return true;
        } catch (ErrorException $e) {
            $transaction->rollBack();

            $this->addError('name', $e->getMessage());

            return false;
        }
    }

    public function update()
    {
        try {
            $transaction = Yii::$app->db->beginTransaction();

            $goods = $this->goods;
            $goods->setAttributes($this->getAttributes());
            if (!$goods->save()) {
                $this->addErrors($goods->getErrors());
                throw new ErrorException('商品保存失败！');
            }

            foreach ($this->galleries as $index => $gallery) {
                if (!isset($this->gallery['image']) || !in_array($gallery->image, $this->gallery['image'])) {
                    $gallery->delete();
                    unset($this->galleries[$index]);
                }
            }

            if (isset($this->gallery['image']) && $this->gallery['image']) {
                foreach ($this->gallery['image'] as $index => $image) {
                    /* @var $gallery \common\models\GoodsGallery */
                    $gallery = GoodsGallery::find()->where(['goods_id' => $goods->id, 'image' => $image])->one();
                    if (!$gallery) {
                        $gallery = new GoodsGallery();
                        $gallery->goods_id = $goods->id;
                        $gallery->image = $image;
                    }
                    $gallery->sort = $index;
                    $gallery->save();
                }
            }

            GoodsAttribute::deleteAll(['goods_id' => $goods->id]);

            if (isset($this->attrs['name'])) {
                foreach ($this->attrs['name'] as $index => $name) {
                    if (!$name) {
                        continue;
                    }
                    if (!isset($this->attrs['value'][$index])) {
                        continue;
                    }

                    $attr = new GoodsAttribute();
                    $attr->goods_id = $goods->id;
                    $attr->name = $name;
                    $attr->value = $this->attrs['value'][$index];
                    $attr->save();
                }
            }

            $goods->save();

            $transaction->commit();

            Utils::clearBuffer();

            return true;
        } catch (ErrorException $e) {
            $transaction->rollBack();

            $this->addError('name', $e->getMessage());

            return false;
        }
    }

    public static function getShelveStatus()
    {
        return [
            Goods::STATUS_UNSHELVE => '下架',
            Goods::STATUS_SHELVE => '上架',
        ];
    }
}
