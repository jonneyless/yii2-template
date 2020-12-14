<?php

namespace api\modules\v1\controllers;

use api\controllers\ApiController;
use api\models\Area;
use Yii;

class AreaController extends ApiController
{

    public $modelClass = 'api\models\Area';

    public function actionIndex()
    {
        $areas = Yii::$app->cache->get('api_areas');

        if(!$areas){
            $areas = $this->getChilds();
            Yii::$app->cache->set('api_areas', $areas);
        }

        return $areas;
    }

    private function getChilds($parent_id = 0)
    {
        $return = [];
        $areas = Area::find()->where(['parent_id' => $parent_id, 'status' => Area::STATUS_ACTIVE])->asArray()->all();
        foreach($areas as $area){
            $return[] = [
                'area_id' => (int) $area['area_id'],
                'name' => $area['name'],
                'child' => (int) $area['child'],
                'items' => $this->getChilds($area['area_id']),
            ];
        }

        return $return;
    }
}