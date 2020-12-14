<?php

use yii\db\Migration;

class m180112_110823_order extends Migration
{
    public function up()
    {
        $this->addColumn('{{%order}}', 'saving', $this->decimal(20, 2)->unsigned()->notNull()->defaultValue(0.00)->comment('节省金额')->after('amount'));
        $this->addColumn('{{%order_goods}}', 'mode', $this->string()->notNull()->defaultValue('')->comment('商品规格')->after('attrs'));
    }

    public function down()
    {
        $this->dropColumn('{{%order}}', 'saving');
        $this->dropColumn('{{%order_goods}}', 'mode');
    }
}
