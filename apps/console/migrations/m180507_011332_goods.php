<?php

use yii\db\Migration;

class m180507_011332_goods extends Migration
{
    public function up()
    {
        $this->addColumn('{{%goods_mode}}', 'original_price', $this->decimal(20, 2)->unsigned()->notNull()->defaultValue(0.00)->comment('商超价')->after('price'));
        $this->addColumn('{{%goods_mode}}', 'cost_price', $this->decimal(20, 2)->unsigned()->notNull()->defaultValue(0.00)->comment('成本价')->after('original_price'));
    }

    public function down()
    {
        $this->dropColumn('{{%goods_mode}}', 'original_price');
        $this->dropColumn('{{%goods_mode}}', 'cost_price');
    }
}
