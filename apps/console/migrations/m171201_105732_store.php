<?php

use yii\db\Migration;

class m171201_105732_store extends Migration
{
    public function up()
    {
        $this->addColumn('{{%store}}', 'service_qq', $this->string()->notNull()->defaultValue('')->comment('客服QQ')->after('service_phone'));
    }

    public function down()
    {
        $this->dropColumn('{{%store}}', 'service_qq');
    }
}
