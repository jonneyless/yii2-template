<?php

use yii\db\Migration;

class m190804_100232_teacher extends Migration
{
    public function up()
    {
        $this->createTable('{{%teacher}}', [
            'teacher_id' => $this->bigPrimaryKey()->unsigned()->notNull()->comment('老师 ID'),
            'store_id' => $this->bigInteger()->unsigned()->notNull()->comment('所属店铺 ID'),
            'name' => $this->string(30)->notNull()->comment('名称'),
            'title' => $this->string(30)->notNull()->defaultValue('')->comment('头衔'),
            'intro' => $this->string()->notNull()->defaultValue('')->comment('介绍'),
            'avatar' => $this->string()->notNull()->defaultValue('')->comment('头像'),
            'created_at' => $this->integer(10)->unsigned()->notNull()->defaultValue(0)->comment('注册时间'),
            'updated_at' => $this->integer(10)->unsigned()->notNull()->defaultValue(0)->comment('更新时间'),
            'status' => $this->smallInteger(1)->unsigned()->notNull()->defaultValue(0)->comment('状态'),
        ], 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB COMMENT="老师"');

        $this->createIndex('Store Id', '{{%teacher}}', 'store_id');

        $this->createTable('{{%teacher_subscribe}}', [
            'teacher_id' => $this->bigInteger()->unsigned()->notNull()->comment('老师 ID'),
            'user_id' => $this->bigInteger()->unsigned()->notNull()->comment('用户 ID'),
            'created_at' => $this->integer(10)->unsigned()->notNull()->defaultValue(0)->comment('预约时间'),
            'updated_at' => $this->integer(10)->unsigned()->notNull()->defaultValue(0)->comment('更新时间'),
            'status' => $this->smallInteger(1)->unsigned()->notNull()->defaultValue(0)->comment('状态'),
        ], 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB COMMENT="老师预约"');

        $this->addPrimaryKey('Teacher User Id', '{{%teacher_subscribe}}', ['teacher_id', 'user_id']);
        $this->createIndex('Teacher Id', '{{%teacher_subscribe}}', 'teacher_id');
        $this->createIndex('User Id', '{{%teacher_subscribe}}', 'user_id');

        $menu = new \admin\models\Menu();
        $menu->parent_id = 3;
        $menu->name = '老师管理';
        $menu->controller = 'teacher';
        $menu->sort = 0;
        $menu->status = 9;
        $menu->save();
    }

    public function down()
    {
        $this->dropTable('{{%teacher}}');
        $this->dropTable('{{%teacher_subscribe}}');
    }
}
