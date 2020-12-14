<?php

use yii\db\Migration;

class m180226_111632_user extends Migration
{
    public function up()
    {
        $this->addColumn('{{%user}}', 'company', $this->bigInteger()->unsigned()->notNull()->defaultValue(0)->comment('所属公司')->after('referee'));
    }

    public function down()
    {
        $this->dropColumn('{{%user}}', 'company');
    }
}
