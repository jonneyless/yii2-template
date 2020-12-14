<?php

use yii\db\Migration;

class m171222_140723_comment extends Migration
{
    public function up()
    {
        $this->addColumn('{{%order_goods}}', 'created_at', $this->integer(10)->unsigned()->notNull()->defaultValue(0)->comment('生成时间')->after('amount'));
        $this->addColumn('{{%order_goods}}', 'updated_at', $this->integer(10)->unsigned()->notNull()->defaultValue(0)->comment('更新时间')->after('created_at'));

        $this->createTable('{{%comment}}', [
            'comment_id' => $this->bigPrimaryKey()->unsigned()->notNull()->comment('评论 ID'),
            'order_id' => $this->bigInteger()->unsigned()->notNull()->comment('订单号'),
            'goods_id' => $this->bigInteger()->unsigned()->notNull()->comment('商品 ID'),
            'user_id' => $this->bigInteger()->unsigned()->notNull()->comment('用户 ID'),
            'goods_score' => $this->smallInteger(1)->unsigned()->notNull()->defaultValue(0)->comment('商品评分'),
            'store_score' => $this->smallInteger(1)->unsigned()->notNull()->defaultValue(0)->comment('店铺评分'),
            'delivery_score' => $this->smallInteger(1)->unsigned()->notNull()->defaultValue(0)->comment('物流评分'),
            'content' => $this->string()->notNull()->defaultValue('')->comment('描述'),
            'created_at' => $this->integer(10)->unsigned()->notNull()->defaultValue(0)->comment('生成时间'),
            'updated_at' => $this->integer(10)->unsigned()->notNull()->defaultValue(0)->comment('更新时间'),
            'status' => $this->smallInteger(1)->unsigned()->notNull()->defaultValue(0)->comment('状态'),
        ], "CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB COMMENT='评论'");

        $this->createTable('{{%comment_image}}', [
            'comment_id' => $this->bigInteger()->unsigned()->notNull()->comment('商品 ID'),
            'image' => $this->string(150)->notNull()->comment('图片'),
        ], 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB COMMENT="评论图片"');
    }

    public function down()
    {
        $this->dropColumn('{{%order_goods}}', 'created_at');
        $this->dropColumn('{{%order_goods}}', 'updated_at');
        $this->dropTable('{{%comment}}');
        $this->dropTable('{{%comment_image}}');
    }
}
