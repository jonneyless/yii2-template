<?php

use yii\db\Migration;

class m171214_123932_order extends Migration
{
    public function up()
    {
        $this->addColumn('{{%order}}', 'memo', $this->string()->notNull()->defaultValue('')->comment('买家备注')->after('phone'));
    }

    public function down()
    {
        $this->dropColumn('{{%order}}', 'memo');
    }
}
