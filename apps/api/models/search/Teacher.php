<?php

namespace api\models\search;

use libs\Utils;
use Yii;
use yii\base\Model;
use api\models\Store;
use api\models\Teacher as TeacherModel;
use yii\data\ActiveDataProvider;

/**
 * 老师搜索模型
 *
 * {@inheritdoc}
 *
 * @property $lng
 * @property $lat
 * @property $keyword
 */
class Teacher extends Model
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
        $query = TeacherModel::find()->joinWith('store as store');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'validatePage' => false,
                'params' => $params,
            ],
        ]);

        $this->load($params, '');

        $query->andFilterWhere([
            'and',
            ['=', 'store.is_offline', Store::IS_OFFLINE_YES],
            ['<>', 'store.latitude', ''],
            ['<>', 'store.longitude', ''],
            ['like', 'name', $this->keyword],
        ]);

        if ($this->lng > 0 && $this->lat > 0) {
            $point = Utils::squarePoint($this->lng, $this->lat, 20);
            $query->andFilterWhere([
                'and',
                ['>', 'store.latitude', $point['rb']['lat']],
                ['<', 'store.latitude', $point['lt']['lat']],
                ['>', 'store.longitude', $point['lt']['lng']],
                ['<', 'store.longitude', $point['rb']['lng']],
            ]);

            $query->orderBy('ACOS(SIN((' . $this->lat . ' * 3.1415) / 180 ) * SIN((store.latitude * 3.1415) / 180 ) + COS((' . $this->lat . ' * 3.1415) / 180 ) * COS((store.latitude * 3.1415) / 180 ) *COS((' . $this->lng . ' * 3.1415) / 180 - (store.longitude * 3.1415) / 180 ) ) * 6380 asc');
        } else {
            $query->orderBy('created_at desc');
        }

        return $dataProvider;
    }
}
