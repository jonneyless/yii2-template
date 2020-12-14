<?php

use yii\db\Migration;

class m180517_004923_store extends Migration
{
    public function up()
    {
        $this->addColumn('{{%store}}', 'referee', $this->bigInteger()->unsigned()->notNull()->defaultValue(0)->comment('推广人')->after('store_id'));
    }

    public function down()
    {
        $this->dropColumn('{{%store}}', 'referee');
    }
}
