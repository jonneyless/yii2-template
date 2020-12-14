<?php

use yii\db\Migration;

class m180729_100232_store extends Migration
{
    public function up()
    {
        $this->addColumn('{{%store}}', 'address', $this->string()->notNull()->defaultValue('')->comment('店铺地址')->after('content'));
        $this->addColumn('{{%store}}', 'pospal_app_id', $this->string(60)->notNull()->defaultValue('')->comment('银豹 App Id')->after('content'));
        $this->addColumn('{{%store}}', 'pospal_app_key', $this->string(60)->notNull()->defaultValue('')->comment('银豹 App Key')->after('pospal_app_id'));
    }

    public function down()
    {
        $this->dropColumn('{{%store}}', 'address');
        $this->dropColumn('{{%store}}', 'pospal_app_id');
        $this->dropColumn('{{%store}}', 'pospal_app_key');
    }
}
