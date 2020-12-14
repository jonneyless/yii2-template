<?php

use yii\db\Migration;

class m180724_000403_goods extends Migration
{
    public function up()
    {
        $this->addColumn('{{%goods}}', 'bar_code', $this->string()->notNull()->defaultValue('')->comment('条形码')->after('number'));
    }

    public function down()
    {
        $this->dropColumn('{{%goods}}', 'bar_code');
    }
}
