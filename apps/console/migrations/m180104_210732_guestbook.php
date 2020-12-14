<?php

use yii\db\Migration;

class m180104_210732_guestbook extends Migration
{
    public function up()
    {
        $this->createTable('{{%guestbook}}', [
            'guestbook_id' => $this->bigPrimaryKey()->unsigned()->notNull()->comment('留言 ID'),
            'user_id' => $this->bigInteger()->unsigned()->notNull()->defaultValue(0)->comment('用户 ID'),
            'type' => $this->string(10)->notNull()->comment('类型'),
            'title' => $this->string()->notNull()->defaultValue('')->comment('标题'),
            'content' => $this->text()->comment('内容'),
            'created_at' => $this->integer(10)->unsigned()->notNull()->defaultValue(0)->comment('生成时间'),
            'updated_at' => $this->integer(10)->unsigned()->notNull()->defaultValue(0)->comment('更新时间'),
        ], "CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB COMMENT='留言'");
    }

    public function down()
    {
        $this->dropTable('{{%guestbook}}');
    }
}
