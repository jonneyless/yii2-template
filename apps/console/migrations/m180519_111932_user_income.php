<?php

use yii\db\Migration;

class m180519_111932_user_income extends Migration
{
    public function up()
    {
        $this->addColumn('{{%user_income}}', 'relation_type', $this->string(20)->notNull()->defaultValue('User')->comment('关联类型')->after('relation_id'));
    }

    public function down()
    {
        $this->dropColumn('{{%user_income}}', 'relation_type');
    }
}
