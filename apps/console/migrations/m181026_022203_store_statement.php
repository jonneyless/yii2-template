<?php

use yii\db\Migration;

class m181026_022203_store_statement extends Migration
{
    public function up()
    {
        $this->createTable('{{%store_statement}}', [
            'store_id' => $this->bigInteger()->notNull()->comment('店铺 ID'),
            'date' => $this->string(10)->notNull()->comment('年月'),
            'offline' => $this->decimal(20, 2)->unsigned()->notNull()->defaultValue(0.00)->comment('线下利润'),
            'online' => $this->decimal(20, 2)->unsigned()->notNull()->defaultValue(0.00)->comment('线上利润'),
            'created_at' => $this->integer(10)->unsigned()->notNull()->defaultValue(0)->comment('生成时间'),
            'updated_at' => $this->integer(10)->unsigned()->notNull()->defaultValue(0)->comment('更新时间'),
            'status' => $this->smallInteger(1)->unsigned()->notNull()->defaultValue(0)->comment('状态'),
        ], "CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB COMMENT='店铺月结对账'");

        $this->addPrimaryKey('Statement Id', '{{%store_statement}}', ['store_id', 'date']);
    }

    public function down()
    {
        $this->dropTable('{{%store_statement}}');
    }
}
