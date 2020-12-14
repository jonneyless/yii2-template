<?php

use yii\db\Migration;

class m180801_021432_user_address extends Migration
{
    public function up()
    {
        $this->alterColumn('{{%user_address}}', 'area_id', $this->bigInteger()->unsigned()->notNull()->defaultValue(0)->comment('地区 ID'));
        $this->addColumn('{{%user_address}}', 'longitude', $this->string(30)->notNull()->defaultValue('')->comment('经度')->after('address'));
        $this->addColumn('{{%user_address}}', 'latitude', $this->string(30)->notNull()->defaultValue('')->comment('纬度')->after('longitude'));
    }

    public function down()
    {
        $this->dropColumn('{{%user_address}}', 'longitude');
        $this->dropColumn('{{%user_address}}', 'latitude');
    }
}
