<?php

use yii\db\Migration;

class m190904_133532_performance extends Migration
{
    public function up()
    {
        $this->addColumn('{{%performance}}', 'city_id', $this->bigInteger()->unsigned()->notNull()->defaultValue(0)->comment('城市代理 ID')->after('agent_company_id'));
    }

    public function down()
    {
        $this->dropColumn('{{%performance}}', 'city_id');
    }
}
