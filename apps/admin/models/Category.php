<?php

namespace admin\models;

use ijony\helpers\Utils;
use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

/**
 * 商品分类数据模型
 *
 * {@inheritdoc}
 */
class Category extends \common\models\Category
{

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return ArrayHelper::merge(parent::rules(), [
            [['child', 'parent_id', 'parent_arr', 'sort'], 'default', 'value' => 0],
            ['status', 'default', 'value' => self::STATUS_ACTIVE],
            ['status', 'in', 'range' => [self::STATUS_UNACTIVE, self::STATUS_ACTIVE]],
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return ArrayHelper::merge(parent::attributeLabels(), [
            'status' => '启用',
            'child' => '子分类'
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function beforeSave($insert)
    {
        if(!$insert){
            if(count(explode(",", $this->child_arr)) > 1){
                $this->child = 1;
            }else{
                $this->child = 0;
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

        if($insert){ // 如果是插入操作，只更新父级分类的子集
            if($this->parent_id){
                $parent = static::findOne($this->parent_id);

                $parent->child_arr = $parent->child_arr . ',' . $this->category_id;
                $parent->save();

                $this->parent_arr = $parent->parent_arr . ',' . $this->parent_id;

                $parents = explode(",", $parent->parent_arr);
                foreach($parents as $parent_id){
                    if(!$parent_id) continue;

                    $parent = static::findOne($parent_id);
                    $parent->child_arr = $parent->child_arr . ',' . $this->category_id;
                    $parent->save();
                }
            }

            $this->child_arr = (string) $this->category_id;
            $this->parent_arr = (string) $this->parent_arr;
            $this->save();
        }else{ // 如果是更新操作，判断是否修改了父级分类，如果是就从原来的所有父级的子集中移除，并添加到新的所有父级的子集中
            if(isset($changedAttributes['parent_id'])){
                $child_arr = explode(",", $this->child_arr);
                $old_parent_arr = $this->parent_arr;

                if($changedAttributes['parent_id']){
                    $parent_arr = explode(",", $this->parent_arr);
                    array_shift($parent_arr);

                    foreach($parent_arr as $parent_id){
                        $parent = static::findOne($parent_id);

                        $parent_child_arr = explode(",", $parent->child_arr);
                        $parent_child_arr = array_diff($parent_child_arr, $child_arr);
                        $parent_child_arr = join(",", $parent_child_arr);

                        $parent->child_arr = $parent_child_arr;
                        $parent->save();
                    }
                }

                if($this->parent_id){
                    $parent = static::findOne($this->parent_id);

                    $parent_child_arr = explode(",", $parent->child_arr);
                    $parent_child_arr = array_merge($parent_child_arr, $child_arr);
                    $parent_child_arr = join(",", $parent_child_arr);

                    $parent->child_arr = $parent_child_arr;
                    $parent->save();

                    $this->parent_arr = $parent->parent_arr . ',' . $this->parent_id;

                    $parents = explode(",", $parent->parent_arr);
                    foreach($parents as $parent_id){
                        if(!$parent_id) continue;

                        $parent = static::findOne($parent_id);

                        $parent_child_arr = explode(",", $parent->child_arr);
                        $parent_child_arr = array_merge($parent_child_arr, $child_arr);
                        $parent_child_arr = join(",", $parent_child_arr);

                        $parent->child_arr = $parent_child_arr;
                        $parent->save();
                    }
                }else{
                    $this->parent_arr = '0';
                }

                foreach($child_arr as $id){
                    if($id == $this->category_id) continue;

                    $child = static::findOne($id);
                    $child->parent_arr = str_replace($old_parent_arr, $this->parent_arr, $child->parent_arr);
                    $child->save();
                }

                $this->save();
            }

            // 如果更新了状态，同步到所有子级
            if(isset($changedAttributes['status'])){
                if($this->child != 0){
                    static::updateAll(['status' => $this->status], ['parent_id' => $this->category_id]);
                }
            }
        }

        // 如果更新了排序，更新所有父级的子集数据的排序
        if(isset($changedAttributes['sort'])){
            if($this->parent_id){
                $parent = static::findOne($this->parent_id);
                $child_arr = $parent->category_id;
                $childs = static::find()->where(['parent_id' => $this->parent_id])->orderBy(['sort' => SORT_ASC])->all();
                foreach($childs as $child){
                    $child_arr .= "," . $child->child_arr;
                }
                $parent->child_arr = $child_arr;
                $parent->save();
            }
        }

        Yii::$app->cache->delete('api_categories_0');
    }

    /**
     * {@inheritdoc}
     */
    public function afterDelete()
    {
        parent::afterDelete();

        $parent_arr = explode(",", $this->parent_arr);
        $child_arr = explode(",", $this->child_arr);

        array_shift($parent_arr);

        foreach($parent_arr as $parent_id){
            $parent = static::findOne($parent_id);

            $parent_child_arr = explode(",", $parent->child_arr);
            $parent_child_arr = array_diff($parent_child_arr, $child_arr);
            $parent_child_arr = join(",", $parent_child_arr);

            $parent->child_arr = $parent_child_arr;
            $parent->save();
        }

        static::deleteAll(['category_id' => $child_arr]);

        Yii::$app->cache->delete('api_categories_0');
    }

    public function getParentIds()
    {
        if(!$this->parent_arr){
            return [0];
        }

        $parent_arr = explode(",", $this->parent_arr);

        return $parent_arr;
    }

    public function getChildrenIds()
    {
        $child_arr = explode(",", $this->child_arr);

        return $child_arr;
    }

    public static function getSelectData($parent_id, $exclude = [])
    {
        $query = static::find()->where([
            'parent_id' => $parent_id,
            'status' => static::STATUS_ACTIVE,
        ]);

        if($exclude){
            if(is_array($exclude)){
                $exclude = explode(",", $exclude);
            }
            $query->andFilterWhere(['not in', 'category_id', (array) $exclude]);
        }

        $items = $query->all();

        if(!$items){
            return [];
        }

        $datas = [];

        if($parent_id){
            $datas[$parent_id] = '请选择';
        }

        foreach($items as $item){
            $datas[$item->category_id] = $item->name;
        }

        return $datas;
    }

    public function getParentButton()
    {
        if($this->parent){
            return Html::a($this->parent->name, ['category/index', 'Category[parent_id]' => $this->parent->parent_id]);
        }

        return '';
    }

    public function getChildButton()
    {
        if($this->child){
            return Html::a($this->name, ['category/index', 'Category[parent_id]' => $this->category_id]);
        }

        return $this->name;
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
}
