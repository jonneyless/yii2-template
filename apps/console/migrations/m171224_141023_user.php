<?php

use yii\db\Migration;

class m171224_141023_user extends Migration
{
    public function up()
    {
        $this->addColumn('{{%user}}', 'avatar', $this->string(150)->notNull()->defaultValue('')->comment('头像')->after('password_hash'));
    }

    public function down()
    {
        $this->dropColumn('{{%user}}', 'avatar');
    }
}
