<?php

use yii\db\Migration;

class m180920_150932_order_goods extends Migration
{
    public function up()
    {
        $this->addColumn('{{%order_goods}}', 'original_price', $this->decimal(20, 2)->unsigned()->notNull()->defaultValue(0.00)->comment('原价')->after('preview'));
        $this->addColumn('{{%order_goods}}', 'member_price', $this->decimal(20, 2)->unsigned()->notNull()->defaultValue(0.00)->comment('会员价')->after('original_price'));
    }

    public function down()
    {
        $this->dropColumn('{{%order_goods}}', 'original_price');
        $this->dropColumn('{{%order_goods}}', 'member_price');
    }
}
