<?php

use yii\db\Migration;

class m180417_012632_goods extends Migration
{
    public function up()
    {
        $this->addColumn('{{%goods}}', 'cost_price', $this->decimal(20, 2)->unsigned()->notNull()->defaultValue(0.00)->comment('成本价')->after('member_price'));
        $this->addColumn('{{%order}}', 'cost', $this->decimal(20, 2)->unsigned()->notNull()->defaultValue(0.00)->comment('成本')->after('amount'));
        $this->addColumn('{{%order_goods}}', 'cost', $this->decimal(20, 2)->unsigned()->notNull()->defaultValue(0.00)->comment('成本')->after('amount'));
    }

    public function down()
    {
        $this->dropColumn('{{%goods}}', 'current_price');
    }
}
