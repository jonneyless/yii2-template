<?php

namespace common\models;

use PDO;
use Yii;
use libs\Utils;
use yii\base\ErrorException;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;

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
 * @property string $price
 * @property string $description
 * @property string $content
 * @property integer $status
 *
 * @property \common\models\GoodsGallery $gallery
 * @property \common\models\GoodsAttribute $attrs
 */
class Goods extends namespace\base\Goods
{

    private $_group_by_quantity;

    /**
     * @var 删除
     */
    const STATUS_DELETED = 0;
    /**
     * @var 下架
     */
    const STATUS_UNSHELVE = 1;
    /**
     * @var 上架
     */
    const STATUS_SHELVE = 9;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return ArrayHelper::merge(parent::rules(), [
            ['status', 'default', 'value' => self::STATUS_SHELVE],
            ['status', 'in', 'range' => [self::STATUS_DELETED, self::STATUS_UNSHELVE, self::STATUS_SHELVE]],
        ]);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => '商品 ID',
            'category_id' => '商品分类',
            'name' => '商品名称',
            'preview' => '预览图',
            'stock' => '库存',
            'sales' => '销量',
            'price' => '单价',
            'description' => '商品简介',
            'content' => '商品详情',
            'status' => '状态',
        ];
    }

    public function beforeSave($insert)
    {
        if ($this->preview) {
            $this->preview = Utils::coverBufferImage($this->preview);
        }

        return parent::beforeSave($insert);
    }

    public function getGallery()
    {
        return $this->hasMany(GoodsGallery::className(), ['goods_id' => 'id'])->orderBy(['sort' => SORT_ASC]);
    }

    public function getAttrs()
    {
        return $this->hasMany(GoodsAttribute::className(), ['goods_id' => 'id']);
    }

    public function getViewUrl()
    {
        return Url::to(['site/goods', 'id' => $this->id]);
    }

    public function getCartUrl($quantity)
    {
        return Url::to(['site/cart', 'goods_id' => $this->id, 'quantity' => $quantity]);
    }

    public function getPreview($width = 0, $height = 0)
    {
        return Utils::getImg($this->preview, $width, $height);
    }

    public function checkStock($quantity)
    {
        return $quantity <= $this->stock;
    }

    public function updateStock($cost, $quantity, $fix = 1)
    {
        $count = $cost * $fix;
        $stock = $count * -1;
        $sales = $count;

        Yii::$app->db->pdo->setAttribute(PDO::ATTR_AUTOCOMMIT, false);
        try {
            $transaction = Yii::$app->db->beginTransaction();

            /* @var $goods \common\models\Goods */
            $goods = Goods::findBySql('select * from {{%goods}} where id = :id for update', ['id' => $this->id])->one();

            if ($fix > 0 && $goods->stock < 1) {
                throw new ErrorException('库存不足！');
            }

            $sql = 'update {{%goods}} set stock = ' . ($goods->stock + $stock) . ', sales = ' . ($goods->sales + $sales) . ' where id = :id';
            $result = Yii::$app->db->createCommand($sql, ['id' => $this->id])->execute();

            if (!$result) {
                throw new ErrorException('库存更新失败！');
            }

            $transaction->commit();

            Yii::$app->db->pdo->setAttribute(PDO::ATTR_AUTOCOMMIT, true);

            return true;
        } catch (ErrorException $e) {
            $transaction->rollBack();

            Yii::$app->db->pdo->setAttribute(PDO::ATTR_AUTOCOMMIT, true);

            return false;
        }
    }

    public static function getShelveStatus()
    {
        return [
            self::STATUS_UNSHELVE => '下架',
            self::STATUS_SHELVE => '上架',
        ];
    }
}
