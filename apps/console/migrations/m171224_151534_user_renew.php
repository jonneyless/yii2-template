<?php

use yii\db\Migration;

class m171224_151534_user_renew extends Migration
{
    public function up()
    {

        $this->createTable('{{%user_renew}}', [
            'renew_id' => $this->string(20)->notNull()->comment('续费 ID'),
            'payment_id' => $this->string(20)->notNull()->defaultValue('')->comment('支付单号'),
            'user_id' => $this->bigInteger()->notNull()->comment('用户 ID'),
            'month' => $this->integer(10)->unsigned()->unsigned()->notNull()->defaultValue(0)->comment('时长'),
            'amount' => $this->decimal(20, 2)->unsigned()->notNull()->defaultValue(0.00)->comment('费用'),
            'created_at' => $this->integer(10)->unsigned()->notNull()->defaultValue(0)->comment('生成时间'),
            'updated_at' => $this->integer(10)->unsigned()->notNull()->defaultValue(0)->comment('更新时间'),
            'status' => $this->smallInteger(1)->unsigned()->notNull()->defaultValue(0)->comment('状态'),
        ], "CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB COMMENT='用户续费记录'");

        $this->createIndex('User Vip', '{{%user_renew}}', 'user_id');

    }

    public function down()
    {
        $this->dropTable('{{%user_renew}}');
    }
}
