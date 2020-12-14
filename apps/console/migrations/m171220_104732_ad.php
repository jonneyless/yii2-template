<?php

use yii\db\Migration;

class m171220_104732_ad extends Migration
{
    public function up()
    {
        $this->addColumn('{{%ad}}', 'mode', $this->string(60)->notNull()->defaultValue('')->comment('跳转模式')->after('type'));
    }

    public function down()
    {
        $this->dropColumn('{{%ad}}', 'mode');
    }
}
