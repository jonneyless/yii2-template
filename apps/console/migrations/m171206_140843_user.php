<?php

use yii\db\Migration;

class m171206_140843_user extends Migration
{
    public function up()
    {
        $this->addColumn('{{%user}}', 'area_id', $this->bigInteger()->unsigned()->defaultValue(0)->comment('地区 ID')->after('user_id'));
    }

    public function down()
    {
        $this->dropColumn('{{%user}}', 'area_id');
    }
}
