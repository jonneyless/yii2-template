<?php

use yii\db\Migration;

class m171210_210532_order extends Migration
{
    public function up()
    {
        $this->alterColumn('{{%order}}', 'payment_id', $this->bigInteger()->unsigned()->notNull()->defaultValue(0)->comment('支付单号'));
        $this->addColumn('{{%order}}', 'fee', $this->decimal(20, 2)->unsigned()->notNull()->defaultValue(0.00)->comment('运费金额')->after('amount'));
        $this->addColumn('{{%order}}', 'freight_id', $this->bigInteger()->unsigned()->notNull()->defaultValue(0)->comment('快递模板 ID')->after('store_id'));
        $this->addColumn('{{%order}}', 'consignee', $this->string(30)->notNull()->comment('收货人')->after('fee'));
        $this->addColumn('{{%order}}', 'area_id', $this->bigInteger()->unsigned()->notNull()->comment('地区 ID')->after('consignee'));
        $this->addColumn('{{%order}}', 'address', $this->string()->notNull()->comment('详细地址')->after('area_id'));
        $this->addColumn('{{%order}}', 'phone', $this->string(60)->notNull()->comment('联系电话')->after('address'));
    }

    public function down()
    {
        $this->alterColumn('{{%order}}', 'payment_id', $this->bigInteger()->unsigned()->notNull()->comment('支付单号'));
        $this->dropColumn('{{%order}}', 'fee');
        $this->dropColumn('{{%order}}', 'freight_id');
        $this->dropColumn('{{%order}}', 'consignee');
        $this->dropColumn('{{%order}}', 'area_id');
        $this->dropColumn('{{%order}}', 'address');
        $this->dropColumn('{{%order}}', 'phone');
    }
}
