<?php

use yii\db\Migration;

class m171219_001023_goods extends Migration
{
    public function up()
    {
        $this->addColumn('{{%goods}}', 'is_hot', $this->smallInteger(1)->unsigned()->notNull()->defaultValue(0)->comment('热销')->after('shelves_at'));
        $this->addColumn('{{%goods}}', 'is_recommend', $this->smallInteger(1)->unsigned()->notNull()->defaultValue(0)->comment('推荐')->after('is_hot'));
    }

    public function down()
    {
        $this->dropColumn('{{%goods}}', 'is_hot');
        $this->dropColumn('{{%goods}}', 'is_recommend');
    }
}
