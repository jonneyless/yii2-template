<?php

use yii\db\Migration;

class m171205_112432_cart extends Migration
{
    public function up()
    {
        $this->truncateTable('{{%cart}}');
        $this->addColumn('{{%cart}}', 'cart_id', $this->string(20)->comment('购物车 ID')->after('user_id'));
        $this->addColumn('{{%cart}}', 'attrs', $this->text()->comment('商品属性')->after('quantity'));
        $this->addColumn('{{%cart}}', 'quick', $this->smallInteger(1)->unsigned()->defaultValue(0)->comment('快速购买'));
        $this->alterColumn('{{%cart}}', 'user_id', $this->bigInteger()->unsigned()->notNull()->defaultValue(0)->comment('用户 ID')->after('cart_id'));
        $this->dropPrimaryKey('Cart Goods', '{{%cart}}');
        $this->addPrimaryKey('Cart Goods', '{{%cart}}', 'cart_id');
    }

    public function down()
    {
        $this->dropPrimaryKey('Cart Goods', '{{%cart}}');
        $this->addPrimaryKey('Cart Goods', '{{%cart}}', ['user_id', 'goods_id']);
        $this->dropColumn('{{%cart}}', 'attrs');
        $this->dropColumn('{{%cart}}', 'cart_id');
        $this->dropColumn('{{%cart}}', 'quick');
    }
}
