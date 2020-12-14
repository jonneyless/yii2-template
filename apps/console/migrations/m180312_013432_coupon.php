<?php

use yii\db\Migration;

class m180312_013432_coupon extends Migration
{
    public function up()
    {
        $this->alterColumn('{{%coupon}}', 'month', $this->integer()->unsigned()->notNull()->defaultValue(0)->comment('月数'));
        $this->dropIndex('month', '{{%coupon}}');
        $this->addColumn('{{%coupon}}', 'day', $this->integer()->unsigned()->notNull()->defaultValue(0)->comment('天数')->after('month'));
    }

    public function down()
    {
        $this->dropColumn('{{%coupon}}', 'day');
        $this->alterColumn('{{%coupon}}', 'month', $this->integer()->unique()->notNull()->defaultValue(0)->comment('月数'));
    }
}
