<?php

namespace wap\models;

use Yii;

/**
 * 地区数据模型
 *
 * {@inheritdoc}
 */
class Area extends \common\models\Area
{

    public static function getData()
    {
        return self::getChilds();
    }

    private static function getChilds($parent_id = 0)
    {
        $return = [];
        $areas = Area::find()->where(['parent_id' => $parent_id, 'status' => Area::STATUS_ACTIVE])->asArray()->all();
        foreach($areas as $area){
            $return[] = [
                'id' => (int) $area['area_id'],
                'name' => $area['name'],
                'child' => self::getChilds($area['area_id']),
            ];
        }

        return $return;
    }
}
