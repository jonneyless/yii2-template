<?php

use yii\db\Migration;

class m190901_110832_teacher_auth extends Migration
{
    public function up()
    {
        $this->createTable('{{%teacher_auth}}', [
            'teacher_id' => $this->bigInteger()->unsigned()->notNull()->comment('老师 ID'),
            'user_id' => $this->bigInteger()->unsigned()->notNull()->comment('用户 ID'),
            'code' => $this->string()->unique()->notNull()->comment('验证码'),
            'created_at' => $this->integer(10)->unsigned()->notNull()->defaultValue(0)->comment('预约时间'),
            'updated_at' => $this->integer(10)->unsigned()->notNull()->defaultValue(0)->comment('更新时间'),
        ], 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB COMMENT="老师验证"');
    }

    public function down()
    {
        $this->dropColumn('{{%teacher}}', 'user_id');
    }
}
