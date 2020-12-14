<?php

use yii\db\Migration;

class m180725_231932_order extends Migration
{
    public function up()
    {
        $this->addColumn('{{%order}}', 'is_offline', $this->tinyInteger(1)->unsigned()->notNull()->defaultValue(0)->comment('线下订单')->after('order_id'));
    }

    public function down()
    {
        $this->dropColumn('{{%order}}', 'is_offline');
    }
}
