<?php

use yii\db\Migration;

class m180127_154923_order extends Migration
{
    public function up()
    {
        $this->addColumn('{{%order}}', 'delivery_type', $this->string(64)->notNull()->defaultValue('')->comment('快递类型')->after('memo'));
        $this->addColumn('{{%order}}', 'delivery_number', $this->string(64)->notNull()->defaultValue('')->comment('快递单号')->after('delivery_type'));
    }

    public function down()
    {
        $this->dropColumn('{{%order}}', 'delivery_type');
        $this->dropColumn('{{%order}}', 'delivery_type');
    }
}
