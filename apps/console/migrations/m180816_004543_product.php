<?php

use admin\models\Menu;
use yii\db\Migration;

class m180816_004543_product extends Migration
{
    public function up()
    {
        $this->createTable('{{%product}}', [
            'id' => $this->bigPrimaryKey()->unsigned()->notNull()->comment('产品 ID'),
            'category_id' => $this->bigInteger()->unsigned()->notNull()->comment('分类 ID'),
            'name' => $this->string(100)->notNull()->comment('名称'),
            'preview' => $this->string(150)->notNull()->defaultValue('')->comment('主图'),
            'bar_code' => $this->string()->notNull()->comment('条形码'),
            'content' => $this->text()->comment('详情'),
            'weight' => $this->decimal(20, 5)->unsigned()->notNull()->defaultValue(0.00000)->comment('重量'),
            'created_at' => $this->integer(10)->unsigned()->notNull()->defaultValue(0)->comment('添加时间'),
            'updated_at' => $this->integer(10)->unsigned()->notNull()->defaultValue(0)->comment('修改时间'),
            'status' => $this->smallInteger(1)->unsigned()->notNull()->defaultValue(0)->comment('状态'),
        ], 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB COMMENT="产品库"');

        $this->createTable('{{%product_gallery}}', [
            'product_id' => $this->bigInteger()->unsigned()->notNull()->comment('商品 ID'),
            'image' => $this->string(150)->notNull()->comment('组图'),
            'description' => $this->string()->notNull()->defaultValue('')->comment('说明'),
            'sort' => $this->integer()->unsigned()->notNull()->defaultValue(0)->comment('排序'),
        ], 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB COMMENT="产品组图"');

        $this->addPrimaryKey('Product Gallery Id', '{{%product_gallery}}', ['product_id', 'image']);

        $params = [
            'parent_id' => 4,
            'name' => '产品库',
            'controller' => 'product',
            'action' => '',
        ];

        if(!Menu::find()->where($params)->exists()){
            (new Menu($params))->save();
        }
    }

    public function down()
    {
        $this->dropTable('{{%product}}');
        $this->dropTable('{{%product_gallery}}');
    }
}
