<?php

namespace api\models\search;

use libs\Utils;
use Yii;
use yii\base\Model;
use api\models\Store as StoreModel;
use yii\data\ActiveDataProvider;

/**
 * 店铺搜索模型
 *
 * {@inheritdoc}
 *
 * @property $lng
 * @property $lat
 * @property $keyword
 */
class Store extends Model
{

    public $lng;
    public $lat;
    public $keyword;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['lng', 'lat', 'keyword'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        return Model::scenarios();
    }

    public function search($params)
    {
        $query = StoreModel::find();

        $dataProvider = new ActiveDataProvider([
            'query'      => $query,
            'pagination' => [
                'validatePage' => false,
                'params'       => $params,
            ],
        ]);

        $this->load($params, '');

        $query->andFilterWhere([
            'and',
            ['=', 'is_offline', StoreModel::IS_OFFLINE_YES],
            ['<>', 'latitude', ''],
            ['<>', 'longitude', ''],
            ['like', 'name', $this->keyword],
        ]);

        if($this->lng > 0 && $this->lat > 0){
            $point = Utils::squarePoint($this->lng, $this->lat);
            $query->andFilterWhere([
                'and',
                ['>', 'latitude', $point['rb']['lat']],
                ['<', 'latitude', $point['lt']['lat']],
                ['>', 'longitude', $point['lt']['lng']],
                ['<', 'longitude', $point['rb']['lng']],
            ]);

            $query->orderBy('ACOS(SIN((' . $this->lat . ' * 3.1415) / 180 ) * SIN((latitude * 3.1415) / 180 ) + COS((' . $this->lat.' * 3.1415) / 180 ) * COS((latitude * 3.1415) / 180 ) *COS((' . $this->lng . ' * 3.1415) / 180 - (longitude * 3.1415) / 180 ) ) * 6380 asc');
        }else{
            $query->orderBy('created_at desc');
        }

        return $dataProvider;
    }
}
