<?php

namespace api\models;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * 地区数据模型
 *
 * {@inheritdoc}
 */
class Area extends \common\models\Area
{

    private static $_areaIds;

    public function fields()
    {
        return [
            'area_id',
            'name',
            'child',
        ];
    }

    public function afterFind()
    {
        parent::afterFind();

        $this->area_id = (int) $this->area_id;
    }

    public static function getNameById($id)
    {
        $area = static::findOne($id);
        return $area ? $area->name : '';
    }

    public static function getParentIds($areaId)
    {
        if(!isset(self::$_areaIds[$areaId])){
            $area = static::findOne($areaId);

            if(!$area){
                return [0];
            }

            $areaIds = explode(",", $area->parent_arr);

            array_push($areaIds, $areaId);

            self::$_areaIds[$areaId] = $areaIds;
        }

        return self::$_areaIds[$areaId];
    }

    public static function getParentLine($areaId)
    {
        $areaIds = self::getParentIds($areaId);

        return array_slice($areaIds, 1);
    }

    public static function getAreaLine($area_id, $space = '')
    {
        $areas = self::getParentLine($area_id);
        foreach($areas as &$area){
            $area = self::getNameById($area);
        }
        return join($space, $areas);
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
