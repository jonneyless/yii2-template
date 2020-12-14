<?php

use yii\db\Migration;

class m190901_104832_teacher extends Migration
{
    public function up()
    {
        $this->addColumn('{{%teacher}}', 'user_id', $this->bigInteger()->unsigned()->notNull()->comment('关联用户 ID')->after('teacher_id'));
    }

    public function down()
    {
        $this->dropColumn('{{%teacher}}', 'user_id');
    }
}
