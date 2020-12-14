<?php

use yii\db\Migration;

class m171224_123223_service extends Migration
{
    public function up()
    {
        $this->alterColumn('{{%order_goods}}', 'service_id', $this->string(20)->notNull()->defaultValue('')->comment('售后编号')->after('goods_id'));
    }

    public function down()
    {
    }
}
