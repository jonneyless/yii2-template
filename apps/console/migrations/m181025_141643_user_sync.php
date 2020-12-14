<?php

use yii\db\Migration;

class m181025_141643_user_sync extends Migration
{
    public function up()
    {
        $this->addColumn('{{%user_income}}', 'id', $this->bigPrimaryKey()->notNull()->notNull()->defaultValue(0)->comment('id')->first());
        $this->addColumn('{{%user_income}}', 'synced', $this->tinyInteger(1)->unsigned()->notNull()->defaultValue(0)->comment('是否同步'));
    }

    public function down()
    {
        $this->dropColumn('{{%user_income}}', 'id');
        $this->dropColumn('{{%user_income}}', 'synced');
    }
}
