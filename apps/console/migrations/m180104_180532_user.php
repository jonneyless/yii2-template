<?php

use yii\db\Migration;

class m180104_180532_user extends Migration
{
    public function up()
    {
        $this->addColumn('{{%user}}', 'device', $this->string()->notNull()->defaultValue('')->comment('设备 Token')->after('access_token'));
    }

    public function down()
    {
        $this->dropColumn('{{%user}}', 'device');
    }
}
