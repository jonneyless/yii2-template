<?php

use yii\db\Migration;

class m180101_112532_goods extends Migration
{
    public function up()
    {
        $this->addColumn('{{%order_goods}}', 'attrs', $this->text()->comment('商品属性')->after('amount'));
        $this->addColumn('{{%order_goods}}', 'name', $this->string(100)->notNull()->comment('商品名称')->after('service_id'));
        $this->addColumn('{{%order_goods}}', 'preview', $this->string(150)->notNull()->comment('商品主图')->after('name'));

        $items = \api\models\OrderGoods::find()->all();
        foreach($items as $item){
            $item->name = $item->goods->name;
            $item->preview = $item->goods->preview;
            $item->save();
        }
    }

    public function down()
    {
        $this->dropColumn('{{%order_goods}}', 'attrs');
        $this->dropColumn('{{%order_goods}}', 'name');
        $this->dropColumn('{{%order_goods}}', 'preview');
    }
}
