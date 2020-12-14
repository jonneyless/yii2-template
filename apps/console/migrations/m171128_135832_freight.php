<?php

use yii\db\Migration;

class m171128_135832_freight extends Migration
{
    public function up()
    {
        $this->dropColumn('{{%goods}}', 'freight_id');
        $this->addColumn('{{%store_freight}}', 'free', $this->decimal(20, 2)->unsigned()->notNull()->defaultValue(0.00)->comment('包邮额度')->after('fee'));
        $this->renameColumn('{{%store_freight}}', 'area_fee', 'area_config');
    }

    public function down()
    {
        $this->addColumn('{{%goods}}', 'freight_id', $this->bigInteger()->unsigned()->notNull()->comment('运费 ID')->after('store_category_id'));
        $this->dropColumn('{{%store_freight}}', 'free');
        $this->renameColumn('{{%store_freight}}', 'area_config', 'area_fee');
    }
}
