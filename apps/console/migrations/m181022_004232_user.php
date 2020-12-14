<?php

use yii\db\Migration;

class m181022_004232_user extends Migration
{
    public function up()
    {
        $this->addColumn('{{%user}}', 'store', $this->bigInteger()->unsigned()->notNull()->defaultValue(0)->comment('所属店铺')->after('company'));
    }

    public function down()
    {
        $this->dropColumn('{{%user}}', 'store');
    }
}
