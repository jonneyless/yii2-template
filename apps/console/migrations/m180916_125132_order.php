<?php

use yii\db\Migration;

class m180916_125132_order extends Migration
{
    public function up()
    {
        $this->addColumn('{{%order}}', 'pospal_id', $this->string(60)->notNull()->defaultValue('')->comment('银豹订单 ID')->after('order_id'));
        $this->addColumn('{{%payment}}', 'pay_type', $this->string(60)->notNull()->defaultValue('')->comment('支付方式')->after('type'));
    }

    public function down()
    {
        $this->dropColumn('{{%order}}', 'pospal_id');
        $this->dropColumn('{{%payment}}', 'pay_type');
    }
}
