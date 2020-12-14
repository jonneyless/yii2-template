<?php

use yii\db\Migration;

class m171224_143523_payment extends Migration
{
    public function up()
    {
        $this->addColumn('{{%payment}}', 'type', $this->string(20)->notNull()->defaultValue('order')->comment('支付类型')->after('payment_id'));
    }

    public function down()
    {
        $this->dropColumn('{{%payment}}', 'type');
    }
}
