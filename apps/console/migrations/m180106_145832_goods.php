<?php

use yii\db\Migration;

class m180106_145832_goods extends Migration
{
    public function up()
    {
        $this->createTable('{{%goods_mode}}', [
            'goods_id' => $this->bigInteger()->unsigned()->notNull()->comment('商品 ID'),
            'name' => $this->string()->notNull()->comment('名称'),
            'value' => $this->string()->notNull()->comment('货品'),
            'price' => $this->decimal(20, 2)->unsigned()->notNull()->defaultValue(0.00)->comment('会员价'),
            'stock' => $this->integer()->unsigned()->notNull()->defaultValue(0)->comment('库存'),
            'image' => $this->string()->notNull()->defaultValue('')->comment('图片')
        ], "CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB COMMENT='货品'");

        $this->addPrimaryKey('Goods Mode Id', '{{%goods_mode}}', ['goods_id', 'value']);
        $this->createIndex('Goods Id', '{{%goods_mode}}', ['goods_id']);
    }

    public function down()
    {
        $this->dropTable('{{%goods_mode}}');
    }
}
