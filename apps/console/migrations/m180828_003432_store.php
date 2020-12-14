<?php

use yii\db\Migration;

class m180828_003432_store extends Migration
{
    public function up()
    {
        $this->addColumn('{{%store}}', 'pospal_normal_member', $this->string(60)->notNull()->defaultValue('')->comment('银豹标准会员 ID')->after('pospal_app_key'));
        $this->addColumn('{{%store}}', 'pospal_vip_member', $this->string(60)->notNull()->defaultValue('')->comment('银豹VIP会员 ID')->after('pospal_normal_member'));
    }

    public function down()
    {
        $this->dropColumn('{{%store}}', 'pospal_normal_member');
        $this->dropColumn('{{%store}}', 'pospal_vip_member');
    }
}
