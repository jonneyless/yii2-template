<?php

use yii\db\Migration;

class m171226_112832_goods extends Migration
{
    public function up()
    {
        $this->addColumn('{{%goods}}', 'weight', $this->decimal(20, 5)->unsigned()->notNull()->defaultValue(0.00000)->comment('重量')->after('content'));
        $this->addColumn('{{%goods}}', 'free_express', $this->smallInteger()->unsigned()->notNull()->defaultValue(0)->comment('包邮件数')->after('shelves_at'));
    }

    public function down()
    {
        $this->dropColumn('{{%goods}}', 'weight');
        $this->dropColumn('{{%goods}}', 'free_express');
    }
}
