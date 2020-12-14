<?php

use yii\db\Migration;

class m180409_230000_user_settle extends Migration
{
    public function up()
    {
        $this->createTable('{{%user_settle}}', [
            'user_id' => $this->bigInteger()->unsigned()->notNull()->defaultValue(0)->comment('用户 ID'),
            'year' => $this->string(4)->notNull()->comment('年份'),
            'month' => $this->string(2)->notNull()->comment('月份'),
            'amount' => $this->decimal(20, 2)->unsigned()->notNull()->comment('金额'),
            'status' => $this->smallInteger(1)->unsigned()->notNull()->defaultValue(0)->comment('状态'),
        ], "CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB COMMENT='用户结算'");

        $this->addPrimaryKey('User Settle Id', '{{%user_settle}}', ['user_id', 'year', 'month']);

        $menu = new \admin\models\Menu();
        $menu->parent_id = 6;
        $menu->name = '奖励结算';
        $menu->controller = 'settle';
        $menu->sort = 0;
        $menu->status = 9;
        $menu->save();
    }

    public function down()
    {
        $this->dropTable('{{%user_settle}}');
    }
}
