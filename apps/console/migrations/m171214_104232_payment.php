<?php

use yii\db\Migration;

class m171214_104232_payment extends Migration
{
    public function up()
    {
        $this->alterColumn('{{%payment}}', 'payment_id', $this->string(20)->notNull()->comment('支付单号'));
        $this->alterColumn('{{%order}}', 'payment_id', $this->string(20)->notNull()->comment('支付单号'));
    }

    public function down()
    {
        $this->alterColumn('{{%payment}}', 'payment_id', $this->bigInteger()->unsigned()->notNull()->comment('支付单号'));
        $this->alterColumn('{{%order}}', 'payment_id', $this->bigInteger()->unsigned()->notNull()->comment('支付单号'));
    }
}
