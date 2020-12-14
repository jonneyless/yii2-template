<?php

use yii\db\Migration;

class m180123_111123_page extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%page}}', [
            'id' => $this->bigPrimaryKey()->unsigned()->comment('页面 ID'),
            'title' => $this->string(150)->notNull()->comment('标题'),
            'content' => $this->text()->comment('内容'),
            'status' => $this->smallInteger(1)->unsigned()->notNull()->defaultValue(0)->comment('状态'),
        ], $tableOptions . " COMMENT='页面'");

        $menu = new \admin\models\Menu();
        $menu->parent_id = 2;
        $menu->name = '单页管理';
        $menu->controller = 'page';
        $menu->sort = 0;
        $menu->status = 9;
        $menu->save();
    }

    public function down()
    {
        $this->dropTable('{{%page}}');
    }
}
