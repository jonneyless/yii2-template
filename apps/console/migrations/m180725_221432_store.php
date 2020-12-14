<?php

use yii\db\Migration;

class m180725_221432_store extends Migration
{
    public function up()
    {
        $this->addColumn('{{%store}}', 'is_offline', $this->tinyInteger(1)->unsigned()->notNull()->defaultValue(0)->comment('线下店铺')->after('store_id'));
        $this->addColumn('{{%store}}', 'longitude', $this->string(30)->notNull()->defaultValue('')->comment('经度')->after('content'));
        $this->addColumn('{{%store}}', 'latitude', $this->string(30)->notNull()->defaultValue('')->comment('纬度')->after('longitude'));
    }

    public function down()
    {
        $this->dropColumn('{{%store}}', 'is_offline');
        $this->dropColumn('{{%store}}', 'longitude');
        $this->dropColumn('{{%store}}', 'latitude');
    }
}
