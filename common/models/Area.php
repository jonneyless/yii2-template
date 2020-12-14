<?php

namespace common\models;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * 地区数据模型
 *
 * {@inheritdoc}
 */
class Area extends namespace\base\Area
{

    const STATUS_DELETE = 0;    // 禁用
    const STATUS_ACTIVE = 9;    // 启用

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return ArrayHelper::merge(parent::rules(), [
            ['status', 'default', 'value' => self::STATUS_DELETE],
            ['status', 'in', 'range' => [self::STATUS_DELETE, self::STATUS_ACTIVE]],
        ]);
    }


    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);

        if($insert){
            $this->child_arr = (string) $this->id;

            if($this->parent_id){
                $parent = Area::findOne($this->parent_id);
                $parent->child = 1;
                $parent->child_arr = $parent->child_arr . ',' . $this->id;
                $parent->save();

                $this->parent_arr = $parent->parent_arr . ',' . $this->parent_id;

                $parents = explode(",", $parent->parent_arr);
                foreach($parents as $parent_id){
                    if(!$parent_id) continue;

                    $parent = Area::findOne($parent_id);
                    $parent->child_arr = $parent->child_arr . ',' . $this->id;
                    $parent->save();
                }
            }

            $this->save();
        }

        if($this->child != 0){
            $childIds = explode(',', $this->child_arr);
            array_shift($childIds);
            Area::updateAll(['status' => $this->status], ['id' => $childIds]);
        }
    }

    public function getChildren()
    {
        return $this->hasMany(Area::className(), ['parent_id' => 'id']);
    }

    public function getLevel()
    {
        switch($this->depth){
            case 1:
                return '省';
                break;
            case 2:
                return '市';
                break;
            case 3:
                return '区县';
                break;
        }
    }

    public static function getNameById($id)
    {
        $area = static::findOne($id);
        return $area ? $area->name : '';
    }

    public static function getTopId($areaId)
    {
        $area = static::findOne($areaId);

        if(!$area){
            $areaIds = [0];
        }else{
            $areaIds = explode(",", $area->parent_arr);
        }

        array_push($areaIds, $areaId);

        return $areaIds[1];
    }

    public static function getParentIds($areaId)
    {
        $area = static::findOne($areaId);

        if(!$area){
            return [0];
        }

        $areaIds = explode(",", $area->parent_arr);

        array_push($areaIds, $areaId);

        return $areaIds;
    }

    public static function getParentLine($areaId)
    {
        $areaIds = self::getParentIds($areaId);

        return array_slice($areaIds, 1);
    }

    public static function getAreaLine($area_id)
    {
        $areas = self::getParentLine($area_id);
        foreach($areas as &$area){
            $area = self::getNameById($area);
        }
        return join("", $areas);
    }

    public static function getSelectData($parentId = 0, $exclude = '')
    {
        if($parentId === ''){
            return [];
        }

        $query = static::find()->where(['parent_id' => $parentId, 'status' => self::STATUS_ACTIVE]);

        if($range = Yii::$app->session->get('area_range', [])){
            $query->andWhere(['id' => $range]);
        }

        if($exclude){
            if(is_array($exclude)){
                $exclude = explode(",", $exclude);
            }
            $query->andFilterWhere(['not in', 'id', (array) $exclude]);
        }

        $areas = $query->all();

        if(!$areas){
            return [];
        }

        $return = [$parentId => '选择' . $areas[0]->getLevel()];
        foreach($areas as $area){
            $return[$area->id] = $area->name;
        }

        return $return;
    }

    public static function getSelectDataByCity()
    {
        $areas = static::find()->where(['depth' => 2, 'status' => self::STATUS_ACTIVE])->all();
        $return = [];
        foreach($areas as $area){
            $return[$area->id] = self::getSelectData($area->id);
        }
        return $return;
    }
}
