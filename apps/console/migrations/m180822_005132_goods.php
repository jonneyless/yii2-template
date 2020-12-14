<?php

use yii\db\Migration;

class m180822_005132_goods extends Migration
{
    public function up()
    {
        $this->addColumn('{{%goods}}', 'product_id', $this->bigInteger()->unsigned()->notNull()->defaultValue(0)->comment('产品 ID')->after('goods_id'));
    }

    public function down()
    {
        $this->dropColumn('{{%goods}}', 'product_id');
    }
}
