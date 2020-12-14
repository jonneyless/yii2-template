<?php

use yii\db\Migration;

class m180809_115132_user_withdraw extends Migration
{
    public function up()
    {
        $this->createTable('{{%user_withdraw}}', [
            'withdraw_id' => $this->bigPrimaryKey()->unsigned()->notNull()->comment('提现 ID'),
            'user_id' => $this->bigInteger()->unsigned()->notNull()->comment('用户 ID'),
            'amount' => $this->decimal(20, 2)->unsigned()->notNull()->defaultValue(0.00)->comment('金额'),
            'type' => $this->string(30)->unsigned()->notNull()->defaultValue('')->comment('提现方式'),
            'account' => $this->text()->comment('提现账号'),
            'created_at' => $this->integer(10)->unsigned()->notNull()->defaultValue(0)->comment('申请时间'),
            'updated_at' => $this->integer(10)->unsigned()->notNull()->defaultValue(0)->comment('更新时间'),
            'status' => $this->smallInteger(1)->unsigned()->notNull()->defaultValue(0)->comment('状态'),
        ], 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB COMMENT="用户提现"');

        $this->createIndex('User Id', '{{%user_withdraw}}', 'user_id');
    }

    public function down()
    {
        $this->dropTable('{{%user_withdraw}}');
    }
}
