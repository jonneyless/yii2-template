<?php

use yii\db\Migration;

class m181025_112232_goods extends Migration
{
    public function up()
    {
        $this->alterColumn('{{%goods_info}}', 'stock', $this->string(20)->notNull()->defaultValue('0')->comment('库存'));
        $this->alterColumn('{{%goods_info}}', 'sell', $this->string(20)->notNull()->defaultValue('0')->comment('销量'));
        $this->alterColumn('{{%order_goods}}', 'quantity', $this->string(20)->notNull()->defaultValue('0')->comment('数量'));
    }

    public function down()
    {
    }
}
