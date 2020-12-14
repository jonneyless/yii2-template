<?php

use yii\db\Migration;

class m190811_164907_teacher_subscribe extends Migration
{
    public function up()
    {
        $this->addColumn('{{%teacher_subscribe}}', 'name', $this->string(20)->notNull()->comment('姓名')->after('user_id'));
        $this->addColumn('{{%teacher_subscribe}}', 'phone', $this->string(30)->notNull()->comment('电话')->after('name'));
        $this->addColumn('{{%teacher_subscribe}}', 'subscribe_at', $this->dateTime()->notNull()->comment('预约时间')->after('phone'));

        $this->dropPrimaryKey('Teacher User Id', '{{%teacher_subscribe}}');
    }

    public function down()
    {
        $this->dropColumn('{{%teacher_subscribe}}', 'name');
        $this->dropColumn('{{%teacher_subscribe}}', 'phone');
        $this->dropColumn('{{%teacher_subscribe}}', 'subscribe_at');
    }
}
