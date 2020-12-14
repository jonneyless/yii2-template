<?php

namespace common\models;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "{{%area}}".
 *
 * @property string $id
 * @property string $parent_id
 * @property string $parent_arr
 * @property integer $child
 * @property string $child_arr
 * @property string $name
 * @property integer $depth
 * @property string $initial
 * @property string $longitude
 * @property string $latitude
 * @property integer $status
 */
class Area extends namespace\base\Area
{

    const STATUS_DELETE = 0;
    const STATUS_ACTIVE = 9;

    /**
     * @inheritdoc
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

    public static function getNameById($id)
    {
        $area = static::findOne($id);

        return $area ? $area->name : '';
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
                return '区';
                break;
        }
    }

    public static function getSelectData($parentId = 0)
    {
        if ($parentId === '') {
            return [];
        }
        $areas = static::find()->where(['parent_id' => $parentId, 'status' => self::STATUS_ACTIVE])->all();
        if (!$areas) {
            return [];
        }
        $return = [$parentId => '选择' . $areas[0]->getLevel()];
        foreach ($areas as $area) {
            $return[$area->id] = $area->name;
        }

        return $return;
    }

    public static function getParentLine($areaId)
    {
        $area = static::findOne($areaId);

        if (!$area) {
            return [0];
        }

        $parentArr = explode(",", $area->parent_arr);

        array_push($parentArr, $areaId);

        return array_slice($parentArr, 1);
    }

    public static function getAreaLine($area_id)
    {
        $areas = self::getParentLine($area_id);
        foreach($areas as &$area){
            $area = self::getNameById($area);
        }
        return join("", $areas);
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
