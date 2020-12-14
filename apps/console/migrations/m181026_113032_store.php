<?php

use yii\db\Migration;

class m181026_113032_store extends Migration
{
    public function up()
    {
        $this->alterColumn('{{%store}}', 'preview', $this->string(150)->notNull()->defaultValue('')->comment('主图'));
        $this->addColumn('{{%store}}', 'owner', $this->bigInteger()->unsigned()->notNull()->defaultValue(0)->comment('店主 ID')->after('referee'));
    }

    public function down()
    {
        $this->dropColumn('{{%store}}', 'owner');
    }
}
