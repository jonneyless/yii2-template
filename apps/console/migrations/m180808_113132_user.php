<?php

use yii\db\Migration;

class m180808_113132_user extends Migration
{
    public function up()
    {
        $this->addColumn('{{%user}}', 'debt', $this->decimal(20, 2)->unsigned()->notNull()->defaultValue(0.00)->comment('欠款')->after('amount'));
    }

    public function down()
    {
        $this->dropColumn('{{%user}}', 'debt');
    }
}
