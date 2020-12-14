<?php

use yii\db\Migration;

class m190811_173843_performance extends Migration
{
    public function up()
    {
        $this->createTable('{{%performance}}', [
            'user_id' => $this->bigInteger()->unsigned()->notNull()->comment('用户 ID'),
            'company_id' => $this->bigInteger()->unsigned()->notNull()->comment('公司 ID'),
            'agent_id' => $this->bigInteger()->unsigned()->notNull()->comment('代理 ID'),
            'agent_company_id' => $this->bigInteger()->unsigned()->notNull()->comment('代理公司 ID'),
            'order_id' => $this->bigInteger()->unsigned()->notNull()->comment('订单号'),
            'amount' => $this->decimal(20, 2)->unsigned()->notNull()->comment('业绩'),
            'is_offline' => $this->tinyInteger(1)->unsigned()->notNull()->defaultValue(0)->comment('线下业绩'),
            'year' => $this->integer(4)->unsigned()->notNull()->comment('年份'),
            'month' => $this->integer(2)->unsigned()->notNull()->comment('月份'),
        ], 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB COMMENT="业绩"');
    }

    public function down()
    {
        $this->dropTable('{{%performance}}');
    }
}
