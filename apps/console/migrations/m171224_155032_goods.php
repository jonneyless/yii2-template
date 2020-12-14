<?php

use yii\db\Migration;

class m171224_155032_goods extends Migration
{
    public function up()
    {
        $this->addColumn('{{%goods}}', 'goods_score', $this->decimal(2, 1)->unsigned()->notNull()->defaultValue(0.0)->comment('商品评分')->after('is_recommend'));
        $this->addColumn('{{%goods}}', 'store_score', $this->decimal(2, 1)->unsigned()->notNull()->defaultValue(0.0)->comment('店铺评分')->after('goods_score'));
        $this->addColumn('{{%goods}}', 'delivery_score', $this->decimal(2, 1)->unsigned()->notNull()->defaultValue(0.0)->comment('物流评分')->after('store_score'));
    }

    public function down()
    {
        $this->dropColumn('{{%goods}}', 'goods_score');
        $this->dropColumn('{{%goods}}', 'store_score');
        $this->dropColumn('{{%goods}}', 'delivery_score');
    }
}
