<?php

use yii\db\Migration;

class m171130_104732_store extends Migration
{
    public function up()
    {
        $this->addColumn('{{%store}}', 'service_phone', $this->string()->notNull()->defaultValue('')->comment('客服电话')->after('preview'));
    }

    public function down()
    {
        $this->dropColumn('{{%store}}', 'service_phone');
    }
}
