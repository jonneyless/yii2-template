<?php

use yii\db\Migration;

class m180122_224813_order extends Migration
{
    public function up()
    {
        $this->dropPrimaryKey('Order Goods Id', '{{%order_goods}}');
        $this->addPrimaryKey('Order Goods Id', '{{%order_goods}}', ['order_id', 'goods_id', 'mode']);
    }

    public function down()
    {
        $this->dropPrimaryKey('Order Goods Id', '{{%order_goods}}');
        $this->addPrimaryKey('Order Goods Id', '{{%order_goods}}', ['order_id', 'goods_id']);
    }
}
