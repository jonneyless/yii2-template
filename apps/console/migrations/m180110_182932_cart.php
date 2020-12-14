<?php

use yii\db\Migration;

class m180110_182932_cart extends Migration
{
    public function up()
    {
        $this->addColumn('{{%cart}}', 'mode', $this->string()->notNull()->defaultValue('')->comment('商品规格')->after('attrs'));
    }

    public function down()
    {
        $this->dropColumn('{{%cart}}', 'mode');
    }
}
