<?php

namespace api\models;

use Yii;
use yii\helpers\Json;

/**
 * 店铺运费数据模型
 *
 * {@inheritdoc}
 */
class StoreFreight extends \common\models\StoreFreight
{

    public function afterFind()
    {
        parent::afterFind();

        $this->freight_id = (int) $this->freight_id;
        $this->area_config = Json::decode($this->area_config);
    }

    public function getAreaData($area_id)
    {
        $topId = \common\models\Area::getTopId($area_id);

        if(isset($this->area_config[$topId])){
            return $this->area_config[$topId];
        }

        return false;
    }

    public function getFeeData($area_id)
    {
        if($area_data = $this->getAreaData($area_id)){
            return sprintf('%.2f', $area_data['fee']);
        }

        return $this->fee;
    }

    public function getFreeData($area_id)
    {
        if($area_data = $this->getAreaData($area_id)){
            return sprintf('%.2f', $area_data['free']);
        }

        return $this->free;
    }

    public function getDeliveryData($area_id, $feeAmount, $freeAmount)
    {
        $fee = $this->getFeeData($area_id);
        $free = $this->getFreeData($area_id);

        $freight = 0.00;
        if($feeAmount > 0 && $feeAmount < $free){
            $feeAmount += $fee;
            $freight = $fee;
        }

        $amount = $feeAmount + $freeAmount;

        return [
            'freight_id' => $this->freight_id,
            'name' => $this->name,
            'fee' => $fee,
            'free' => $free,
            'freight' => $freight,
            'amount' => sprintf('%.2f', $amount),
        ];
    }

    public function getFee($area_id, $amount)
    {
        $fee = $this->getFeeData($area_id);
        $free = $this->getFreeData($area_id);

        if($amount < $free){
            return $fee;
        }

        return 0.00;
    }
}
