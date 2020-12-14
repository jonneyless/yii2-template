<?php

use yii\db\Migration;

class m180806_234023_user extends Migration
{
    public function up()
    {
        $this->addColumn('{{%user}}', 'open_id', $this->string(64)->notNull()->defaultValue('')->comment('外部平台 ID')->after('user_id'));
        $this->addColumn('{{%user}}', 'tradepass_hash', $this->string()->notNull()->defaultValue('')->comment('交易密码')->after('password_hash'));
    }

    public function down()
    {
        $this->dropColumn('{{%user}}', 'open_id');
        $this->dropColumn('{{%user}}', 'tradepass_hash');
    }
}
