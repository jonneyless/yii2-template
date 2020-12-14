<?php

use yii\db\Migration;

class m171224_142632_user_info extends Migration
{
    public function up()
    {

        $this->createTable('{{%user_info}}', [
            'user_id' => $this->bigInteger()->notNull()->comment('用户 ID'),
            'birthday' => $this->integer(10)->unsigned()->notNull()->defaultValue(0)->comment('生日'),
            'gander' => $this->string(1)->notNull()->defaultValue('n')->comment('性别'),
        ], "CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB COMMENT='用户附加信息'");

        $this->addPrimaryKey('User Id', '{{%user_info}}', 'user_id');

    }

    public function down()
    {
        $this->dropTable('{{%user_info}}');
    }
}
