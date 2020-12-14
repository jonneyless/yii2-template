<?php

use yii\db\Migration;

class m171227_152932_order extends Migration
{
    public function up()
    {
        $this->dropTable('{{%goods_comment}}');
        $this->alterColumn('{{%order}}', 'order_id', $this->string(20)->notNull()->comment('订单号'));
        $this->alterColumn('{{%order_goods}}', 'order_id', $this->string(20)->notNull()->comment('订单号'));
        $this->addColumn('{{%order_goods}}', 'user_id', $this->bigInteger()->unsigned()->notNull()->comment('用户 ID')->after('order_id'));
        $this->alterColumn('{{%service}}', 'order_id', $this->string(20)->notNull()->comment('订单号'));
        $this->alterColumn('{{%comment}}', 'order_id', $this->string(20)->notNull()->comment('订单号'));
    }

    public function down()
    {
        $this->createTable('{{%goods_comment}}', [
            'comment_id' => $this->bigPrimaryKey()->unsigned()->notNull()->comment('评论 ID'),
            'order_id' => $this->bigInteger()->unsigned()->notNull()->comment('订单号'),
            'user_id' => $this->bigInteger()->unsigned()->notNull()->comment('用户 ID'),
            'goods_score' => $this->smallInteger(1)->unsigned()->notNull()->defaultValue(0)->comment('商品评分'),
            'store_score' => $this->smallInteger(1)->unsigned()->notNull()->defaultValue(0)->comment('店铺评分'),
            'delivery_score' => $this->smallInteger(1)->unsigned()->notNull()->defaultValue(0)->comment('物流评分'),
            'content' => $this->text()->comment('评论内容'),
            'created_at' => $this->integer(10)->unsigned()->notNull()->defaultValue(0)->comment('注册时间'),
            'updated_at' => $this->integer(10)->unsigned()->notNull()->defaultValue(0)->comment('更新时间'),
        ], 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB COMMENT="商品评价"');
    }
}
