<?php

namespace api\models;

use ijony\helpers\Image;
use libs\Utils;
use Yii;

/**
 * 商品数据模型
 *
 * {@inheritdoc}
 *
 * @property $attr
 * @property $mode
 * @property \api\models\GoodsGallery[]   $gallery
 * @property \api\models\GoodsInfo        $info
 * @property \api\models\Category         $category
 * @property \api\models\Store            $store
 * @property \api\models\StoreCategory    $storeCategory
 */
class Goods extends \common\models\Goods
{

    public static function findByPospalId($pospal_id)
    {
        return self::findOne(['pospal_id' => $pospal_id]);
    }

    /**
     * @param string $pospal_id
     * @param \api\models\Store $store
     *
     * @return \api\models\Goods|array|null|\yii\db\ActiveRecord
     * @throws \yii\base\ErrorException
     * @throws \yii\web\HttpException
     */
    public static function syncByPospalId($pospal_id, $store)
    {
        /* @var \libs\pospal\Pospal $pospal */
        $pospal = Yii::$app->pospal->setStore($store->pospal_app_id, $store->pospal_app_key);

        $result = $pospal->product->queryById($pospal_id);

        if(!$result->isSuccess()){
            throw new ErrorException('Not find Product in Pospal.');
        }

        $goodsModel = Goods::find()->where(['bar_code' => $result->getData('barcode')])->one();
        if(!$goodsModel){
            $goodsModel = new Goods();
            $goodsModel->store_id = $store->store_id;
            $goodsModel->bar_code = $result->getData('barcode');
            $goodsModel->cost_price = $result->getData('buyPrice');
            $goodsModel->original_price = $result->getData('sellPrice');
            $goodsModel->member_price = $result->getData('sellPrice');
            $goodsModel->status = Goods::STATUS_OFFLINE;
        }

        $goodsModel->pospal_id = $result->getData('uid');
        $goodsModel->name = $result->getData('name');
        $goodsModel->save(false);

        $goodsInfo = GoodsInfo::find()->where(['goods_id' => $goodsModel->goods_id])->one();
        if(!$goodsInfo){
            $goodsInfo = new GoodsInfo();
            $goodsInfo->goods_id = $goodsModel->goods_id;
        }

        $goodsInfo->stock = $result->getData('stock');
        $goodsInfo->save();

        return $goodsModel;
    }

    public function getPrice()
    {
        if(!Yii::$app->user->getIsGuest() && Yii::$app->user->identity->checkExpire()){
            return $this->member_price;
        }

        return $this->original_price;
    }

    /**
     * 商品属性
     * @return array
     */
    public function getAttr()
    {
        $attrs = GoodsAttribute::find()->where(['goods_id' => $this->goods_id])->all();

        $return = [];
        $attrIndex = [];
        $index = 0;
        foreach($attrs as $attr){
            if(!isset($attrIndex[$attr->name])){
                $return[$index] = [
                    'name'   => $attr->name,
                    'values' => [
                        $attr->value,
                    ],
                ];

                $attrIndex[$attr->name] = $index;

                $index++;
            }else{
                $return[$attrIndex[$attr->name]]['values'][] = $attr->value;
            }
        }

        return $return;
    }

    public function getMode()
    {
        /* @var \api\models\GoodsMode[] $modes */
        $modes = GoodsMode::find()->where(['goods_id' => $this->goods_id])->all();

        $return  = [];

        foreach($modes as $mode){
            $member_price = $mode->price > 0 ? $mode->price : $this->member_price;
            $original_price = $mode->original_price > 0 ? $mode->original_price : $this->original_price;

            $price = $member_price;
            if(!Yii::$app->user->getIsGuest()){
                $price = Yii::$app->user->identity->checkExpire() ? $price : $original_price;
            }
            $return['name'] = $mode->name;
            $return['values'][] = [
                'value' => $mode->value,
                'price' => $price,
                'cost' => $mode->cost_price,
                'stock' => $mode->stock > 0 ? $mode->stock : $this->info->stock,
                'image' => Image::getImg($mode->image ? $mode->image : $this->preview, 760, 760, 'default.jpg', Image::THUMB_MODE_ADAPT),
            ];
        }

        return $return;
    }

    public function checkMode($mode)
    {
        return GoodsMode::find()->where(['goods_id' => $this->goods_id, 'value' => $mode])->exists();
    }

    /**
     * 商品组图
     * @return \yii\db\ActiveQuery
     */
    public function getGallery()
    {
        $galleries = GoodsGallery::find()->where(['goods_id' => $this->goods_id])->all();

        return array_map(function ($item){
            return [
                'image' => Image::getImg($item->image, 760, 760, 'default.jpg', Image::THUMB_MODE_ADAPT),
            ];
        }, $galleries);
    }

    /**
     * 商品附加信息
     * @return \yii\db\ActiveQuery
     */
    public function getInfo()
    {
        return $this->hasOne(GoodsInfo::className(), ['goods_id' => 'goods_id']);
    }

    /**
     * 商品分类信息
     * @return \yii\db\ActiveQuery
     */
    public function getCategory()
    {
        return $this->hasOne(Category::className(), ['category_id' => 'category_id']);
    }

    /**
     * 店铺信息
     * @return \yii\db\ActiveQuery
     */
    public function getStore()
    {
        return $this->hasOne(Store::className(), ['store_id' => 'store_id']);
    }

    /**
     * 店铺商品分类信息
     * @return \yii\db\ActiveQuery
     */
    public function getStoreCategory()
    {
        return $this->hasOne(StoreCategory::className(), ['category_id' => 'store_category_id']);
    }

    public function showContent()
    {
        $content = preg_replace('/width=".*?"/', '', $this->content);
        $content = preg_replace('/height=".*?"/', '', $content);
        $content = preg_replace('/style=".*?"/', '', $content);

        return $content;
    }

    public function buildListData()
    {
        $sell = $this->info->sell;

        if($sell < 100 && in_array($this->store_id, Yii::$app->params['self_store'])){
            $md5 = md5($this->goods_id);
            $md5 = preg_replace('/[a-z0]/', '', $md5);
            $first = substr($md5, 0, 1);
            $first = ($first % 3) + 1;
            $sell = substr($md5, 1, $first);
            $sell += $this->info->sell;
        }

        return [
            'goods_id'          => (int)$this->goods_id,
            'category_id'       => (int)$this->category_id,
            'store_id'          => (int)$this->store_id,
            'store_category_id' => (int)$this->store_category_id,
            'name'              => $this->name,
            'preview'           => Image::getImg($this->preview, 340, 340, 'default.jpg'),
            'number'            => $this->number,
            'original_price'    => $this->original_price,
            'member_price'      => $this->member_price,
            'created_at'        => date("Y-m-d H:i:s", $this->created_at),
            'updated_at'        => date("Y-m-d H:i:s", $this->updated_at),
            'shelves_at'        => date("Y-m-d", $this->shelves_at),
            'info'              => [
                'stock' => (int)$this->info->stock,
                'sell'  => (int)$sell,
            ],
        ];
    }

    public function buildViewData()
    {

        $sell = $this->info->sell;

        if($sell < 100 && in_array($this->store_id, Yii::$app->params['self_store'])){
            $md5 = md5($this->goods_id);
            $md5 = preg_replace('/[a-z0]/', '', $md5);
            $first = substr($md5, 0, 1);
            $first = ($first % 3) + 1;
            $sell = substr($md5, 1, $first);
            $sell += $this->info->sell;
        }

        return [
            'goods_id'          => (int)$this->goods_id,
            'category_id'       => (int)$this->category_id,
            'store_id'          => (int)$this->store_id,
            'store_category_id' => (int)$this->store_category_id,
            'name'              => $this->name,
            'preview'           => Image::getImg($this->preview, 760, 760, 'default.jpg', Image::THUMB_MODE_ADAPT),
            'number'            => $this->number,
            'original_price'    => $this->original_price,
            'member_price'      => $this->member_price,
            'weight'            => "" . 1 * $this->weight,
            'express_notice'    => $this->free_express > 0 ? $this->free_express . '件包邮' : $this->store->getFreeExpress(),
            'saving'            => sprintf("%.02f", $this->original_price - $this->member_price),
            'content'           => $this->showContent(),
            'created_at'        => date("Y-m-d H:i:s", $this->created_at),
            'updated_at'        => date("Y-m-d H:i:s", $this->updated_at),
            'shelves_at'        => date("Y-m-d", $this->shelves_at),
            'goods_score'       => $this->goods_score,
            'store_score'       => $this->store_score,
            'delivery_score'    => $this->delivery_score,
            'info'              => [
                'stock' => (int)$this->info->stock,
                'sell'  => (int)$sell,
            ],
            'gallery'           => $this->gallery,
            'attr'              => $this->attr,
            'mode'              => $this->mode ? $this->mode : new \stdClass(),
            'store'             => $this->store->buildViewData(),
            'storeCategory'     => $this->storeCategory->buildViewData(),
            'detail'            => Utils::getWapUrl(['goods/view', 'id' => $this->goods_id, 'in-app' => 1]),
        ];
    }

    public function updateScore()
    {
        $goods_score_total = Comment::find()->where(['goods_id' => $this->goods_id])->sum('goods_score');
        $store_score_total = Comment::find()->where(['goods_id' => $this->goods_id])->sum('store_score');
        $delivery_score_total = Comment::find()->where(['goods_id' => $this->goods_id])->sum('delivery_score');
        $count = Comment::find()->where(['goods_id' => $this->goods_id])->count();

        $this->goods_score = round($goods_score_total / $count, 1);
        $this->store_score = round($store_score_total / $count, 1);
        $this->delivery_score = round($delivery_score_total / $count, 1);

        return $this->save();
    }

    public function updateStock($quantity)
    {
        $this->info->stock -= $quantity;

        if($this->info->stock < 0){
            $this->info->stock = 0;
        }

        return $this->info->save();
    }

    public function updateSell($quantity)
    {
        $this->info->sell += $quantity;
        $this->info->save();
    }
}
