<?php

namespace admin\models;

use common\models\Area;
use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Json;

/**
 * 店铺运费数据模型
 *
 * {@inheritdoc}
 */
class StoreFreight extends \common\models\StoreFreight
{

    public $format_area_config = [];

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return ArrayHelper::merge(parent::rules(), [
            ['format_area_config', 'safe'],
        ]);
    }

    public function beforeSave($insert)
    {
        $area_config = [];

        if(isset($this->format_area_config['area_id'])){
            foreach($this->format_area_config['area_id'] as $index => $area_id){
                $area_config[$area_id] = [
                    'fee' => $this->format_area_config['fee'][$index],
                    'free' => $this->format_area_config['free'][$index],
                ];
            }
        }
        $this->area_config = Json::encode($area_config);

        return parent::beforeSave($insert);
    }

    public function afterFind()
    {
        parent::afterFind();

        $area_config = Json::decode($this->area_config);

        if($area_config){
            foreach($area_config as $area_id => $config){
                $this->format_area_config['area_id'][] = $area_id;
                $this->format_area_config['fee'][] = $config['fee'];
                $this->format_area_config['free'][] = $config['free'];
            }
        }
    }

    /**
     * 分类下拉表单数据
     * @return array
     */
    public function getStoreSelectData()
    {
        return Store::find()->select('name')->indexBy('store_id')->column();
    }

    public function checkStore($store_id)
    {
        if(!$store_id){
            return true;
        }

        return $this->store_id == $store_id;
    }

    /**
     * 获取表单项
     *
     * @param null $where
     *
     * @return string
     */
    public static function getOptions($where = null)
    {
        $query = StoreFreight::find()->select('name');

        if($where){
            $query->where($where);
        }

        $datas = $query->indexBy('freight_id')->column();
        $params = ['prompt' => '请选择'];

        return Html::renderSelectOptions(null, $datas, $params);
    }

    public function getProvinceSelectData()
    {
        return Area::find()->select('name')->where(['parent_id' => 0])->indexBy('area_id')->column();
    }
}
