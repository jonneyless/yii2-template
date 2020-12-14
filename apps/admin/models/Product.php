<?php

namespace admin\models;

use ijony\helpers\File;
use ijony\helpers\Folder;
use ijony\helpers\Image;
use ijony\helpers\Utils;
use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "{{%product}}".
 *
 * {@inheritdoc}
 *
 * @property string $id 产品 ID
 * @property string $category_id 分类 ID
 * @property string $name 名称
 * @property string $preview 主图
 * @property string $bar_code 条形码
 * @property string $content 详情
 * @property string $weight 重量
 * @property int $created_at 添加时间
 * @property int $updated_at 修改时间
 * @property int $status 状态
 *
 * @property array $galleries 组图数据
 */
class Product extends \common\models\Product
{

    public $galleries = [
        'image' => [],
        'thumb' => [],
        'description' => [],
    ];

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return ArrayHelper::merge(parent::rules(), [
            [['weight'], 'default', 'value' => 0],
            [['galleries'], 'safe'],
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function afterFind()
    {
        parent::afterFind();

        if($this->gallery){
            foreach($this->gallery as $gallery){
                $this->galleries['image'][] = $gallery->image;
                $this->galleries['thumb'][] = Image::getImg($gallery->image, 170, 170);
                $this->galleries['description'][] = $gallery->description;
            }
        }
    }

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

        return parent::beforeSave($insert);
    }

    /**
     * {@inheritdoc}
     */
    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);

        if(isset($changedAttributes['preview']) && $changedAttributes['preview']){
            File::delFile($changedAttributes['preview'], true);
        }

        if($this->gallery){
            foreach($this->gallery as $gallery){
                if(!in_array($gallery->image, $this->galleries['image'])){
                    $gallery->delete();
                }
            }
        }

        ProductGallery::deleteAll(['product_id' => $this->id]);

        if($this->galleries['image']){
            foreach($this->galleries['image'] as $index => $gallery){
                (new ProductGallery([
                    'product_id' => $this->id,
                    'image' => $gallery,
                    'description' => $this->galleries['description'][$index],
                    'sort' => $index
                ]))->save();
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function afterDelete()
    {
        parent::afterDelete();

        $galleries = ProductGallery::findAll(['product_id' => $this->id]);
        foreach($galleries as $gallery){
            $gallery->delete();
        }

        File::delFile($this->preview, true);
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
            self::STATUS_UNACTIVE => '禁用',
            self::STATUS_ACTIVE => '启用',
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
}
