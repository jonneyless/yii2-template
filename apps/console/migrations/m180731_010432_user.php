<?php

use yii\db\Migration;

class m180731_010432_user extends Migration
{
    public function up()
    {
        $this->addColumn('{{%user}}', 'amount', $this->decimal(20, 2)->unsigned()->notNull()->defaultValue(0.00)->comment('账户金额')->after('mobile'));
    }

    public function down()
    {
        $this->dropColumn('{{%user}}', 'amount');
    }
}
