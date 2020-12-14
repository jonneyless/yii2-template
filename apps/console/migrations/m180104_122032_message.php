<?php

use yii\db\Migration;

class m180104_122032_message extends Migration
{
    public function up()
    {
        $this->createTable('{{%message}}', [
            'message_id' => $this->bigPrimaryKey()->unsigned()->notNull()->comment('消息 ID'),
            'admin_id' => $this->bigInteger()->unsigned()->notNull()->defaultValue(0)->comment('管理员 ID'),
            'type' => $this->string('10')->notNull()->comment('类型'),
            'title' => $this->string()->notNull()->comment('标题'),
            'content' => $this->text()->comment('内容'),
            'is_all' => $this->smallInteger(1)->unsigned()->notNull()->defaultValue(0)->comment('全局发送'),
            'created_at' => $this->integer(10)->unsigned()->notNull()->defaultValue(0)->comment('生成时间'),
            'updated_at' => $this->integer(10)->unsigned()->notNull()->defaultValue(0)->comment('更新时间'),
            'status' => $this->smallInteger(1)->unsigned()->notNull()->defaultValue(0)->comment('状态'),
        ], "CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB COMMENT='消息'");

        $this->createTable('{{%user_message}}', [
            'message_id' => $this->bigInteger()->unsigned()->notNull()->comment('消息 ID'),
            'user_id' => $this->bigInteger()->unsigned()->notNull()->comment('用户 ID'),
            'is_read' => $this->smallInteger(1)->unsigned()->notNull()->defaultValue(0)->comment('已读'),
        ], 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB COMMENT="用户消息状态"');

        $this->addPrimaryKey('User Message Id', '{{%user_message}}', ['message_id', 'user_id']);
    }

    public function down()
    {
        $this->dropTable('{{%message}}');
        $this->dropTable('{{%user_message}}');
    }
}
