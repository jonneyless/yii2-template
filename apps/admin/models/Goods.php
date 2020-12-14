<?php

namespace admin\models;

use ijony\helpers\File;
use ijony\helpers\Image;
use ijony\helpers\Utils;
use Yii;

/**
 * 商品数据模型
 *
 * {@inheritdoc}
 *
 * @property \admin\models\GoodsInfo $goods_info;
 */
class Goods extends \common\models\Goods
{

    /**
     * {@inheritdoc}
     */
    public function beforeSave($insert)
    {
        $datas = Image::recoverImg($this->content);

        $this->content = $datas['content'];

        if($this->preview && substr($this->preview, 0, 6) == BUFFER_FOLDER){
            $oldImg = $this->preview;
            $newImg = Image::copyImg($this->preview);

            if($newImg){
                File::delFile($oldImg, true);
            }

            $this->preview = $newImg;
        }


        if(!$this->product_id && $this->bar_code){
            /* @var Product $product */
            $product = Product::find()->where(['bar_code' => $this->bar_code])->one();
            if($product){
                $this->product_id = $product->id;
            }
        }

        return parent::beforeSave($insert);
    }

    /**
     * {@inheritdoc}
     */
    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);

        if(isset($changedAttributes['preview']) && $changedAttributes['preview']){
            if(!Product::find()->where(['preview' => $changedAttributes['preview']])->exists()){
                File::delFile($changedAttributes['preview'], true);
            }
        }

        if(!$this->product_id && $this->bar_code){
            $product = new Product();
            $product->setAttributes($this->getAttributes());

            if($product->save()){
                /* @var \admin\models\GoodsGallery[] $galleries */
                if($galleries = $this->getGallery()->all()){
                    foreach($galleries as $gallery){
                        $model = new ProductGallery();
                        $model->product_id = $product->id;
                        $model->image = $gallery->image;
                        $model->description = $gallery->description;
                        $model->sort = $gallery->sort;
                        $model->save();
                    }
                }

                $this->product_id = $product->id;
                $this->save();
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function afterDelete()
    {
        parent::afterDelete();

        GoodsInfo::deleteAll(['goods_id' => $this->goods_id]);
        GoodsAttribute::deleteAll(['goods_id' => $this->goods_id]);
        $galleries = GoodsGallery::findAll(['goods_id' => $this->goods_id]);
        foreach($galleries as $gallery){
            $gallery->delete();
        }

        if(!Product::find()->where(['preview' => $this->preview])->exists()){
            File::delFile($this->preview, true);
        }
    }

    /**
     * 分类下拉表单数据
     * @return array
     */
    public function getCategorySelectData()
    {
        return Category::find()->select('name')->where(['status' => Category::STATUS_ACTIVE])->indexBy('category_id')->column();
    }

    /**
     * 店铺分类下拉表单数据
     * @return array
     */
    public function getStoreSelectData()
    {
        return Store::find()->select('name')->where(['store_id' => $this->store_id, 'status' => Store::STATUS_ACTIVE])->indexBy('store_id')->column();
    }

    /**
     * 店铺分类下拉表单数据
     * @return array
     */
    public function getStoreCategorySelectData()
    {
        return StoreCategory::find()->select('name')->where(['store_id' => $this->store_id, 'status' => StoreCategory::STATUS_ACTIVE])->indexBy('category_id')->column();
    }

    /**
     * 店铺运费模板下拉表单数据
     * @return array
     */
    public function getStoreFreightSelectData()
    {
        return StoreFreight::find()->select('name')->where(['store_id' => $this->store_id])->indexBy('freight_id')->column();
    }

    /**
     * 获取主图
     *
     * @param int $width
     * @param int $height
     *
     * @return mixed
     */
    public function getPreview($width = 0, $height =  0)
    {
        return Image::getImg($this->preview, $width, $height);
    }

    /**
     * 输出所属分类名称
     *
     * @return string
     */
    public function getCategoryName()
    {
        return $this->category ? $this->category->name : '';
    }

    /**
     * 输出所属店铺名称
     *
     * @return string
     */
    public function getStoreName()
    {
        return $this->store ? $this->store->name : '';
    }

    /**
     * 输出所属店铺分类名称
     *
     * @return string
     */
    public function getStoreCategoryName()
    {
        return $this->storeCategory ? $this->storeCategory->name : '';
    }

    /**
     * 输出所属店铺运费模板名称
     *
     * @return string
     */
    public function getStoreFreightName()
    {
        return $this->freight ? $this->freight->name : '';
    }

    public function checkStore($store_id)
    {
        if(!$store_id){
            return true;
        }

        return $this->store_id == $store_id;
    }

    /**
     * 获取热销表述
     *
     * @return mixed|string
     */
    public function getIsHot()
    {
        $datas = $this->getIsHotSelectData();

        return isset($datas[$this->is_hot]) ? $datas[$this->is_hot] : '';
    }

    /**
     * 获取热销标签
     *
     * @return mixed|string
     */
    public function getIsHotLabel()
    {
        if($this->is_hot == self::IS_HOT_ACTIVE){
            $class = 'label-primary';
        }else{
            $class = 'label-danger';
        }

        return Utils::label($this->getIsHot(), $class);
    }

    /**
     * 获取完整热销数据
     *
     * @return array
     */
    public function getIsHotSelectData()
    {
        return [
            self::IS_HOT_UNACTIVE => '否',
            self::IS_HOT_ACTIVE => '是',
        ];
    }

    /**
     * 获取推荐表述
     *
     * @return mixed|string
     */
    public function getIsRecommend()
    {
        $datas = $this->getIsRecommendSelectData();

        return isset($datas[$this->is_recommend]) ? $datas[$this->is_recommend] : '';
    }

    /**
     * 获取推荐标签
     *
     * @return mixed|string
     */
    public function getIsRecommendLabel()
    {
        if($this->is_recommend == self::IS_RECOMMEND_ACTIVE){
            $class = 'label-primary';
        }else{
            $class = 'label-danger';
        }

        return Utils::label($this->getIsRecommend(), $class);
    }

    /**
     * 获取完整推荐数据
     *
     * @return array
     */
    public function getIsRecommendSelectData()
    {
        return [
            self::IS_RECOMMEND_UNACTIVE => '否',
            self::IS_RECOMMEND_ACTIVE => '是',
        ];
    }

    /**
     * 获取状态表述
     *
     * @return mixed|string
     */
    public function getStatus()
    {
        $datas = $this->getStatusSelectData();

        return isset($datas[$this->status]) ? $datas[$this->status] : '';
    }

    /**
     * 获取状态标签
     *
     * @return mixed|string
     */
    public function getStatusLabel()
    {
        if($this->status == self::STATUS_ACTIVE){
            $class = 'label-primary';
        }else{
            $class = 'label-danger';
        }

        return Utils::label($this->getStatus(), $class);
    }

    /**
     * 获取完整状态数据
     *
     * @return array
     */
    public function getStatusSelectData()
    {
        return [
            self::STATUS_UNACTIVE => '下架',
            self::STATUS_ACTIVE => '上架',
        ];
    }

    public function getExportData()
    {
        return [
            $this->name,
            $this->category->name,
            $this->bar_code,
            $this->number,
            '',
            $this->info->stock,
            $this->cost_price,
            $this->original_price,
            '',
            $this->member_price,
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            $this->goods_id,
            '',
            '',
            1,
            '',
            ''
        ];
    }

    public function showWeight()
    {

        if($this->weight < 0.1){
            return '';
        }else if($this->weight < 1){
            return ($this->weight * 1000) . '克';
        }else{
            return sprintf('%.01f', round($this->weight, 1)) . '千克';
        }
    }

    public function repair()
    {
        return;
        if($this->status != self::STATUS_ACTIVE){
            return false;
        }

        if($this->original_price == $this->member_price){
            return false;
        }

        if(!$this->bar_code){
            return false;
        }

        if(!$this->store->pospal_app_id || !$this->store->pospal_app_key){
            return false;
        }

        if(!$this->store->pospal_normal_member || !$this->store->pospal_vip_member){
            return false;
        }

        /* @var \libs\pospal\Pospal $pospal */
        $pospal = Yii::$app->pospal->setStore($this->store->pospal_app_id, $this->store->pospal_app_key);

        $result = $pospal->product->queryByBarcode(['barcode' => $this->bar_code]);
        if($result->isSuccess()){
            $this->pospal_id = $result->getData('uid');
            if($this->save()){
                $params = [
                    'productInfo' => [
                        "uid" => $this->pospal_id,
                        "name" => $this->name,
                        "barcode" => $this->bar_code,
                        "buyPrice" => $this->cost_price,
                        "sellPrice" => $this->original_price,
                        "customerPrice" => $this->member_price,
                        "stock" => $this->info ? $this->info->stock : 0,
                        "isCustomerDiscount" => 0,
                        "enable" => 1,
                    ],
                ];

                $result = $pospal->product->update($params);
                if($result->isSuccess()){
                    return true;
                }
            }
        }

        return false;
    }

    public function sync()
    {
        return;
        if($this->status != self::STATUS_ACTIVE){
            return false;
        }

        if($this->original_price == $this->member_price){
            return false;
        }

        if(!$this->bar_code){
            return false;
        }

        if(!$this->store->pospal_app_id || !$this->store->pospal_app_key){
            return false;
        }

        if(!$this->store->pospal_normal_member || !$this->store->pospal_vip_member){
            return false;
        }

        /* @var \libs\pospal\Pospal $pospal */
        $pospal = Yii::$app->pospal->setStore($this->store->pospal_app_id, $this->store->pospal_app_key);

        $info = GoodsInfo::find()->where(['goods_id' => $this->goods_id])->one();

        $params = [
            'productInfo' => [
                "name" => $this->name,
                "barcode" => $this->bar_code,
                "buyPrice" => $this->cost_price,
                "sellPrice" => $this->original_price,
                "customerPrice" => $this->member_price,
                "stock" => $info ? $info->stock : 0,
                "isCustomerDiscount" => 0,
                "enable" => 1,
            ],
        ];

        if($this->pospal_id){
            $params['productInfo']['uid'] = $this->pospal_id;
            $result = $pospal->product->update($params);
            if(!$result->isSuccess()){
                return false;
            }
        }else{
            $result = $pospal->product->create($params);
            if($result->isSuccess()){
                $this->pospal_id = $result->getData('uid');
                if(!$this->save()){
                    Yii::error($this->getErrors());
                }
            }else{
                return $this->repair();
            }
        }

        if($this->pospal_id){
            $result = $pospal->product->member([
                'productUid' => $this->pospal_id,
                'customerPriceInfo' => [
                    [
                        'categoryUid' => $this->store->pospal_normal_member,
                        'price' => $this->original_price,
                        'salable' => 1,
                    ],
                    [
                        'categoryUid' => $this->store->pospal_vip_member,
                        'price' => $this->member_price,
                        'salable' => 1,
                    ]
                ],
            ]);
            if(!$result->isSuccess()){
                return false;
            }
        }

        return true;
    }
}
