<?php

use yii\db\Migration;

class m161026_094000_init extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%admin}}', [
            'id' => $this->bigPrimaryKey()->unsigned()->comment('用户 ID'),
            'username' => $this->string(24)->notNull()->comment('用户名'),
            'password_hash' => $this->string(64)->notNull()->defaultValue('')->comment('登录密码'),
            'auth_key' => $this->string(32)->notNull()->comment('登录保持密钥'),
            'created_at' => $this->integer(10)->notNull()->unsigned()->defaultValue(0)->comment('注册时间'),
            'updated_at' => $this->integer(10)->notNull()->unsigned()->defaultValue(0)->comment('更新时间'),
            'signup_at' => $this->integer(10)->notNull()->unsigned()->defaultValue(0)->comment('登录时间'),
            'status' => $this->smallInteger(1)->notNull()->unsigned()->defaultValue(0)->comment('状态'),
        ], $tableOptions . " COMMENT='管理'");

        $this->createIndex('Status', '{{%admin}}', 'status');

        $this->createTable('{{%area}}', [
            'id' => $this->bigPrimaryKey()->unsigned()->comment('地区 ID'),
            'parent_id' => $this->bigInteger()->notNull()->unsigned()->defaultValue(0)->comment('父级 ID'),
            'parent_arr' => $this->string()->notNull()->defaultValue(0)->comment('父级链'),
            'child' => $this->smallInteger(1)->notNull()->unsigned()->defaultValue(0)->comment('是否有子级'),
            'child_arr' => $this->text()->comment('子集合集'),
            'name' => $this->string(30)->notNull()->comment('地区名称'),
            'depth' => $this->smallInteger(1)->notNull()->unsigned()->defaultValue(1)->comment('层级'),
            'initial' => $this->string(1)->notNull()->defaultValue('')->comment('首字母'),
            'longitude' => $this->string(30)->notNull()->defaultValue('')->comment('经度'),
            'latitude' => $this->string(30)->notNull()->defaultValue('')->comment('纬度'),
            'status' => $this->smallInteger(1)->notNull()->unsigned()->defaultValue(0)->comment('状态'),
        ], $tableOptions . " COMMENT='地区'");

        $this->createIndex('Parent Id', '{{%area}}', 'parent_id');
        $this->createIndex('Depth Id', '{{%area}}', 'depth');
        $this->createIndex('Status', '{{%area}}', 'status');

        $this->createTable('{{%category}}', [
            'id' => $this->bigPrimaryKey()->unsigned()->comment('分类 ID'),
            'parent_id' => $this->bigInteger()->notNull()->unsigned()->defaultValue(0)->comment('父级 ID'),
            'name' => $this->string(10)->notNull()->comment('分类名称'),
            'status' => $this->smallInteger(1)->notNull()->unsigned()->defaultValue(0)->comment('状态'),
        ], $tableOptions . " COMMENT='分类'");

        $this->createIndex('Status', '{{%category}}', 'status');

        $this->createTable('{{%goods}}', [
            'id' => $this->bigPrimaryKey()->unsigned()->comment('商品 ID'),
            'category_id' => $this->bigInteger()->notNull()->unsigned()->comment('分类 ID'),
            'name' => $this->string(150)->notNull()->comment('商品名称'),
            'sub_name' => $this->string()->notNull()->defaultValue('')->comment('商品提示'),
            'preview' => $this->string(100)->notNull()->comment('预览图'),
            'stock' => $this->bigInteger()->notNull()->unsigned()->defaultValue(0)->comment('库存'),
            'sales' => $this->bigInteger()->notNull()->unsigned()->defaultValue(0)->comment('销量'),
            'price' => $this->decimal(20, 2)->notNull()->unsigned()->defaultValue(0.00)->comment('单价'),
            'description' => $this->string()->comment('商品简介'),
            'content' => $this->text()->comment('商品详情'),
            'status' => $this->smallInteger(1)->notNull()->unsigned()->defaultValue(0)->comment('状态'),
        ], $tableOptions . " COMMENT='商品'");

        $this->createIndex('Category Id', '{{%goods}}', 'category_id');
        $this->createIndex('Status', '{{%goods}}', 'status');

        $this->createTable('{{%goods_attribute}}', [
            'goods_id' => $this->bigInteger()->notNull()->unsigned()->comment('商品 ID'),
            'name' => $this->string(30)->notNull()->comment('属性名'),
            'value' => $this->string(150)->notNull()->comment('属性值'),
        ], $tableOptions . " COMMENT='商品属性'");

        $this->createIndex('Goods Id', '{{%goods_attribute}}', 'goods_id');

        $this->createTable('{{%goods_gallery}}', [
            'goods_id' => $this->bigInteger()->notNull()->unsigned()->comment('商品 ID'),
            'image' => $this->string(100)->notNull()->comment('图片地址'),
            'sort' => $this->smallInteger()->notNull()->unsigned()->defaultValue(0)->comment('排序'),
        ], $tableOptions . " COMMENT='商品组图'");

        $this->addPrimaryKey('Goods Gallery Id', '{{%goods_gallery}}', ['goods_id', 'image']);
        $this->createIndex('Goods Id', '{{%goods_gallery}}', 'goods_id');

        $this->createTable('{{%order}}', [
            'id' => $this->string(16)->notNull()->comment('订单 ID'),
            'user_id' => $this->bigInteger()->notNull()->unsigned()->comment('用户 ID'),
            'goods_id' => $this->bigInteger()->notNull()->unsigned()->comment('商品 ID'),
            'price' => $this->decimal(20, 2)->notNull()->unsigned()->defaultValue(0.00)->comment('单价'),
            'quantity' => $this->integer()->notNull()->unsigned()->comment('数量'),
            'amount' => $this->decimal(20, 2)->notNull()->unsigned()->defaultValue(0.00)->comment('总金额'),
            'paid' => $this->decimal(20, 2)->notNull()->unsigned()->defaultValue(0.00)->comment('已付金额'),
            'consignee' => $this->string()->notNull()->defaultValue('')->comment('收货人'),
            'area_id' => $this->bigInteger()->notNull()->unsigned()->defaultValue(0)->comment('地址区域'),
            'address' => $this->string()->notNull()->defaultValue('')->comment('收货地址'),
            'phone' => $this->string()->notNull()->defaultValue('')->comment('联系电话'),
            'delivery_name' => $this->string(30)->notNull()->defaultValue('')->comment('物流名称'),
            'delivery_number' => $this->string(60)->notNull()->defaultValue('')->comment('物流单号'),
            'pay_card' => $this->string(60)->notNull()->defaultValue('')->comment('支付卡号'),
            'created_at' => $this->integer(10)->notNull()->unsigned()->defaultValue(0)->comment('下单时间'),
            'updated_at' => $this->integer(10)->notNull()->unsigned()->defaultValue(0)->comment('更新时间'),
            'payment_status' => $this->smallInteger(1)->notNull()->unsigned()->defaultValue(0)->comment('支付状态'),
            'delivery_status' => $this->smallInteger(1)->notNull()->unsigned()->defaultValue(0)->comment('物流状态'),
            'status' => $this->smallInteger(1)->notNull()->unsigned()->defaultValue(0)->comment('状态'),
        ], $tableOptions . " COMMENT='订单'");

        $this->addPrimaryKey('ID', '{{%order}}', 'id');
        $this->createIndex('User Id', '{{%order}}', 'user_id');
        $this->createIndex('Goods Id', '{{%order}}', 'goods_id');
        $this->createIndex('Payment Status', '{{%order}}', 'payment_status');
        $this->createIndex('Delivery Status', '{{%order}}', 'delivery_status');
        $this->createIndex('Status', '{{%order}}', 'status');
        $this->createIndex('User Payment Status', '{{%order}}', ['user_id', 'payment_status']);
        $this->createIndex('User Delivery Status', '{{%order}}', ['user_id', 'delivery_status']);
        $this->createIndex('User Status', '{{%order}}', ['user_id', 'status']);

        $this->createTable('{{%user}}', [
            'id' => $this->bigPrimaryKey()->unsigned()->comment('用户 ID'),
            'auth_key' => $this->string(32)->notNull()->comment('登录保持密钥'),
            'name' => $this->string(60)->notNull()->defaultValue('')->comment('姓名'),
            'mobile' => $this->string(13)->notNull()->defaultValue('')->comment('手机号码'),
            'created_at' => $this->integer(10)->notNull()->unsigned()->defaultValue(0)->comment('注册时间'),
            'updated_at' => $this->integer(10)->notNull()->unsigned()->defaultValue(0)->comment('更新时间'),
            'signup_at' => $this->integer(10)->notNull()->unsigned()->defaultValue(0)->comment('登录时间'),
            'first_pay' => $this->integer(10)->notNull()->unsigned()->defaultValue(0)->comment('首次支付'),
            'login_status' => $this->smallInteger(1)->notNull()->unsigned()->defaultValue(0)->comment('登录状态'),
            'status' => $this->smallInteger(1)->notNull()->unsigned()->defaultValue(0)->comment('状态'),
        ], $tableOptions . " COMMENT='用户'");

        $this->createIndex('Mobile', '{{%user}}', 'mobile');
        $this->createIndex('Status', '{{%user}}', 'status');
    }

    public function down()
    {
        $this->dropTable('{{%admin}}');
        $this->dropTable('{{%area}}');
        $this->dropTable('{{%category}}');
        $this->dropTable('{{%goods}}');
        $this->dropTable('{{%goods_attribute}}');
        $this->dropTable('{{%goods_gallery}}');
        $this->dropTable('{{%order}}');
        $this->dropTable('{{%user}}');
    }
}
