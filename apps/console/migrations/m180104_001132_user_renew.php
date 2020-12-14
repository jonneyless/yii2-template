<?php

use yii\db\Migration;

class m180104_001132_user_renew extends Migration
{
    public function up()
    {
        $this->addPrimaryKey('Renew Id', '{{%user_renew}}', 'renew_id');

    }

    public function down()
    {
        $this->dropPrimaryKey('Renew Id','{{%user_renew}}');
    }
}
