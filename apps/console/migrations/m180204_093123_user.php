<?php

use yii\db\Migration;

class m180204_093123_user extends Migration
{
    public function up()
    {
        $this->addColumn('{{%user}}', 'device_type', $this->string(64)->notNull()->defaultValue('')->comment('设备类型')->after('device'));
    }

    public function down()
    {
        $this->dropColumn('{{%user}}', 'device_type');
    }
}
