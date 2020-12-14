<?php

use yii\db\Migration;

class m171221_145323_service extends Migration
{
    public function up()
    {
        $this->addColumn('{{%order_goods}}', 'service_id', $this->string(20)->notNull()->comment('售后编号')->after('goods_id'));

        $this->createTable('{{%service}}', [
            'service_id' => $this->string(20)->notNull()->comment('售后编号'),
            'order_id' => $this->bigInteger()->unsigned()->notNull()->comment('订单号'),
            'goods_id' => $this->bigInteger()->unsigned()->notNull()->comment('商品 ID'),
            'user_id' => $this->bigInteger()->unsigned()->notNull()->comment('用户 ID'),
            'type' => $this->integer()->unsigned()->notNull()->defaultValue(0)->comment('类型'),
            'quantity' => $this->smallInteger()->unsigned()->notNull()->defaultValue(0)->comment('数量'),
            'memo' => $this->string()->notNull()->defaultValue('')->comment('描述'),
            'created_at' => $this->integer(10)->unsigned()->notNull()->defaultValue(0)->comment('生成时间'),
            'updated_at' => $this->integer(10)->unsigned()->notNull()->defaultValue(0)->comment('更新时间'),
            'status' => $this->smallInteger(1)->unsigned()->notNull()->defaultValue(0)->comment('状态'),
        ], "CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB COMMENT='售后'");

        $this->addPrimaryKey('Service Id', '{{%service}}', 'service_id');

        $this->createTable('{{%service_attachment}}', [
            'service_id' => $this->string(20)->notNull()->comment('售后编号'),
            'type' => $this->string(20)->notNull()->defaultValue('image')->comment('类型'),
            'file' => $this->string(150)->notNull()->comment('文件'),
        ], 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB COMMENT="售后附件"');

        $this->createIndex('Service Id', '{{%service_attachment}}', 'service_id');
    }

    public function down()
    {
        $this->dropColumn('{{%order_goods}}', 'service_id');
        $this->dropTable('{{%service}}');
        $this->dropTable('{{%service_attachment}}');
    }
}
