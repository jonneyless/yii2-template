<?php

use yii\db\Migration;

class m180729_020632_goods extends Migration
{
    public function up()
    {
        $this->addColumn('{{%goods}}', 'pospal_id', $this->string(60)->notNull()->defaultValue('')->comment('银豹 ID')->after('goods_id'));
    }

    public function down()
    {
        $this->dropColumn('{{%goods}}', 'pospal_id');
    }
}
