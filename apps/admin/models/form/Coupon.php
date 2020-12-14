<?php

namespace admin\models\form;

use Yii;
use yii\base\ErrorException;
use yii\base\Model;

/**
 * 优惠券生成模型
 *
 * {@inheritdoc}
 *
 * @property string $type 时间类型
 * @property string $count 时间数量
 * @property string $quantity 张数
 * @property string $code 编码
 */
class Coupon extends Model
{

    public $type = 0;
    public $count = 0;
    public $quantity = 0;

    private  $code = [];

    public static $_type = [
        0 => '月卡',
        1 => '天卡',
    ];

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['type', 'count', 'quantity'], 'integer'],
            [['code'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'type' => '时间类型',
            'count' => '时间数量',
            'quantity' => '张数',
        ];
    }

    public function save()
    {
        if(!$this->quantity){
            $this->addError('quantity', '请设置张数！');

            return false;
        }

        if(!$this->count){
            $this->addError('count', '请设置时间数量！');

            return false;
        }

        $code = [];

        for($i = 0; $i < $this->quantity; $i++){
            $model = new \admin\models\Coupon();
            $model->code = \admin\models\Coupon::genCode();

            if($this->type == 0){
                $model->month = $this->count;
            }else{
                $model->day = $this->count;
            }

            $model->status = \admin\models\Coupon::STATUS_ACTIVE;
            if(!$model->save()){
                print_r($model->getErrors());
                die();
            }

            $code[] = $model->code;
        }

        $this->code = $code;

        return true;
    }

    public function export()
    {
        header('Content-Type: application/vnd.ms-excel' );
        header('Content-Disposition: attachment;filename="优惠券.csv"' );
        header('Cache-Control: max-age=0' );

        $fp = fopen ('php://output', 'a');

        foreach($this->code as $code){
            fputcsv($fp, [$code]);
        }

        fclose($fp);
    }
}
