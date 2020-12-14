<?php

use yii\db\Migration;

class m180225_223832_user_info extends Migration
{
    public function up()
    {
        $this->addColumn('{{%user_info}}', 'truename', $this->string(60)->notNull()->defaultValue('')->comment('真实姓名'));
        $this->addColumn('{{%user_info}}', 'idcard', $this->string(120)->notNull()->defaultValue('')->comment('身份证号'));
        $this->addColumn('{{%user_info}}', 'mobile', $this->string(60)->notNull()->defaultValue('')->comment('手机号码'));
        $this->addColumn('{{%user_info}}', 'bankno', $this->string(120)->notNull()->defaultValue('')->comment('银行卡号'));
        $this->addColumn('{{%user_info}}', 'bankname', $this->string(120)->notNull()->defaultValue('')->comment('支行名称'));
        $this->addColumn('{{%user_info}}', 'can_modify', $this->smallInteger(1)->unsigned()->notNull()->defaultValue(1)->comment('可以修改'));
    }

    public function down()
    {
        $this->dropColumn('{{%user_info}}', 'truename');
        $this->dropColumn('{{%user_info}}', 'idcard');
        $this->dropColumn('{{%user_info}}', 'mobile');
        $this->dropColumn('{{%user_info}}', 'bankno');
        $this->dropColumn('{{%user_info}}', 'bankname');
        $this->dropColumn('{{%user_info}}', 'can_modify');
    }
}
