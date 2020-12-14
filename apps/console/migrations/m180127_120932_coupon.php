<?php

use yii\db\Migration;

class m180127_120932_coupon extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%coupon}}', [
            'id' => $this->bigPrimaryKey()->unsigned()->comment('页面 ID'),
            'user_id' => $this->bigInteger()->unsigned()->notNull()->defaultValue(0)->comment('用户 ID'),
            'code' => $this->string(150)->notNull()->comment('标题'),
            'month' => $this->integer()->unique()->notNull()->defaultValue(0)->comment('月数'),
            'status' => $this->smallInteger(1)->unsigned()->notNull()->defaultValue(0)->comment('状态'),
        ], $tableOptions . " COMMENT='券码'");

        $menu = new \admin\models\Menu();
        $menu->parent_id = 6;
        $menu->name = '优惠券';
        $menu->controller = 'coupon';
        $menu->sort = 0;
        $menu->status = 9;
        $menu->save();
    }

    public function down()
    {
        $this->dropTable('{{%coupon}}');
    }
}
