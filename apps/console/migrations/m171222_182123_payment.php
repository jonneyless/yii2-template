<?php

use yii\db\Migration;

class m171222_182123_payment extends Migration
{
    public function up()
    {
        $this->alterColumn('{{%order}}', 'payment_id', $this->string(20)->notNull()->defaultValue('')->comment('支付单号'));
    }

    public function down()
    {
        $this->alterColumn('{{%order}}', 'payment_id', $this->bigInteger()->unsigned()->notNull()->comment('支付单号'));
    }
}
