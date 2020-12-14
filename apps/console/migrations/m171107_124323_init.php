<?php

use yii\db\Migration;

class m171107_124323_init extends Migration
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
            'role_id' => $this->bigInteger()->unsigned()->notNull()->defaultValue(0)->comment('角色 ID'),
            'store_id' => $this->bigInteger()->unsigned()->notNull()->defaultValue(0)->comment('店铺 ID'),
            'username' => $this->string(24)->notNull()->comment('用户名'),
            'password_hash' => $this->string(64)->notNull()->defaultValue('')->comment('登录密码'),
            'auth_key' => $this->string(32)->notNull()->defaultValue('')->comment('登录保持密钥'),
            'created_at' => $this->integer(10)->unsigned()->notNull()->defaultValue(0)->comment('注册时间'),
            'updated_at' => $this->integer(10)->unsigned()->notNull()->defaultValue(0)->comment('更新时间'),
            'signup_at' => $this->integer(10)->unsigned()->notNull()->defaultValue(0)->comment('登录时间'),
            'status' => $this->smallInteger(1)->unsigned()->notNull()->defaultValue(0)->comment('状态'),
        ], $tableOptions . " COMMENT='管理员'");

        $this->createTable('{{%admin_auth}}', [
            'key' => $this->string(30)->notNull()->comment('标识'),
            'name' => $this->string(60)->notNull()->comment('名称'),
            'parent' => $this->string(30)->notNull()->defaultValue('')->comment('父级'),
            'description' => $this->string()->notNull()->defaultValue('')->comment('说明'),
            'route' => $this->text()->notNull()->comment('路由'),
        ], $tableOptions . " COMMENT='管理角色'");

        $this->addPrimaryKey('Auth Key', '{{%admin_auth}}', 'key');

        $this->createTable('{{%admin_role}}', [
            'id' => $this->bigPrimaryKey()->unsigned()->comment('角色 ID'),
            'name' => $this->string(255)->notNull()->comment('名称'),
            'description' => $this->string(255)->notNull()->defaultValue('')->comment('说明'),
            'auth' => $this->text()->comment('权限'),
            'route' => $this->text()->comment('路由'),
            'status' => $this->smallInteger(1)->unsigned()->notNull()->defaultValue(0)->comment('状态'),
        ], $tableOptions . " COMMENT='管理角色'");

        $this->createTable('{{%area}}', [
            'area_id' => $this->bigPrimaryKey()->unsigned()->comment('地区 ID'),
            'parent_id' => $this->bigInteger()->unsigned()->notNull()->defaultValue(0)->comment('父级 ID'),
            'parent_arr' => $this->string()->notNull()->defaultValue(0)->comment('父级链'),
            'child' => $this->smallInteger(1)->unsigned()->notNull()->defaultValue(0)->comment('是否有子级'),
            'child_arr' => $this->text()->comment('子集合集'),
            'name' => $this->string(30)->notNull()->comment('地区名称'),
            'depth' => $this->smallInteger(1)->unsigned()->notNull()->defaultValue(1)->comment('层级'),
            'initial' => $this->string(1)->notNull()->defaultValue('')->comment('首字母'),
            'longitude' => $this->string(30)->notNull()->defaultValue('')->comment('经度'),
            'latitude' => $this->string(30)->notNull()->defaultValue('')->comment('纬度'),
            'status' => $this->smallInteger(1)->unsigned()->notNull()->defaultValue(0)->comment('状态'),
        ], $tableOptions . " COMMENT='地区'");

        $this->createIndex('Parent Id', '{{%area}}', 'parent_id');
        $this->createIndex('Depth Id', '{{%area}}', 'depth');
        $this->createIndex('Status', '{{%area}}', 'status');

        $this->createTable('{{%cart}}', [
            'user_id' => $this->bigInteger()->unsigned()->notNull()->defaultValue(0)->comment('用户 ID'),
            'goods_id' => $this->bigInteger()->unsigned()->notNull()->defaultValue(0)->comment('商品 ID'),
            'quantity' => $this->integer()->unsigned()->notNull()->defaultValue(0)->comment('数量'),
        ], $tableOptions . ' COMMENT="购物车"');

        $this->addPrimaryKey('Cart Goods', '{{%cart}}', ['user_id', 'goods_id']);

        $this->createTable('{{%category}}', [
            'category_id' => $this->bigPrimaryKey()->unsigned()->comment('ID'),
            'parent_id' => $this->bigInteger()->unsigned()->notNull()->defaultValue(0)->comment('父级分类'),
            'name' => $this->string(60)->notNull()->comment('名称'),
            'child' => $this->smallInteger(1)->unsigned()->notNull()->defaultValue(0)->comment('是否有子级'),
            'parent_arr' => $this->string()->notNull()->defaultValue(0)->comment('父级链'),
            'child_arr' => $this->text()->comment('子级群'),
            'sort' => $this->bigInteger()->unsigned()->notNull()->defaultValue(0)->comment('排序'),
            'status' => $this->smallInteger(1)->unsigned()->notNull()->defaultValue(0)->comment('状态'),
        ], $tableOptions . ' COMMENT="分类"');

        $this->createTable('{{%goods}}', [
            'goods_id' => $this->bigPrimaryKey()->unsigned()->notNull()->comment('ID'),
            'category_id' => $this->bigInteger()->unsigned()->notNull()->comment('分类 ID'),
            'store_id' => $this->bigInteger()->unsigned()->notNull()->comment('店铺 ID'),
            'store_category_id' => $this->bigInteger()->unsigned()->notNull()->comment('店铺分类 ID'),
            'freight_id' => $this->bigInteger()->unsigned()->notNull()->comment('运费 ID'),
            'name' => $this->string(100)->notNull()->comment('名称'),
            'preview' => $this->string(150)->notNull()->comment('主图'),
            'number' => $this->string(30)->notNull()->comment('编号'),
            'original_price' => $this->decimal(20, 2)->unsigned()->notNull()->defaultValue(0.00)->comment('商超价'),
            'member_price' => $this->decimal(20, 2)->unsigned()->notNull()->defaultValue(0.00)->comment('会员价'),
            'content' => $this->text()->comment('详情'),
            'created_at' => $this->integer(10)->unsigned()->notNull()->defaultValue(0)->comment('添加时间'),
            'updated_at' => $this->integer(10)->unsigned()->notNull()->defaultValue(0)->comment('修改时间'),
            'shelves_at' => $this->integer(10)->unsigned()->notNull()->defaultValue(0)->comment('上架时间'),
            'status' => $this->smallInteger(1)->unsigned()->notNull()->defaultValue(0)->comment('状态'),
        ], $tableOptions . ' COMMENT="商品"');

        $this->createTable('{{%goods_attribute}}', [
            'goods_id' => $this->bigInteger()->unsigned()->notNull()->comment('商品 ID'),
            'name' => $this->string(30)->notNull()->comment('属性名'),
            'value' => $this->string(150)->notNull()->comment('属性值'),
        ], $tableOptions . " COMMENT='商品属性'");

        $this->createIndex('Goods Id', '{{%goods_attribute}}', 'goods_id');

        $this->createTable('{{%goods_gallery}}', [
            'goods_id' => $this->bigInteger()->unsigned()->notNull()->comment('商品 ID'),
            'image' => $this->string(150)->notNull()->comment('组图'),
            'description' => $this->string()->notNull()->defaultValue('')->comment('说明'),
            'sort' => $this->integer()->unsigned()->notNull()->defaultValue(0)->comment('排序'),
        ], $tableOptions . ' COMMENT="商品组图"');

        $this->addPrimaryKey('Goods Gallery Id', '{{%goods_gallery}}', ['goods_id', 'image']);

        $this->createTable('{{%goods_info}}', [
            'goods_id' => $this->bigInteger()->unsigned()->notNull()->comment('商品 ID'),
            'stock' => $this->integer()->unsigned()->notNull()->defaultValue(0)->comment('库存'),
            'sell' => $this->integer()->unsigned()->notNull()->defaultValue(0)->comment('销量'),
        ], $tableOptions . ' COMMENT="商品数据"');

        $this->addPrimaryKey('Goods Id', '{{%goods_info}}', 'goods_id');

        $this->createTable('{{%menu}}', [
            'id' => $this->bigPrimaryKey()->unsigned()->comment('菜单 ID'),
            'parent_id' => $this->bigInteger(20)->notNull()->defaultValue(0)->comment('父级'),
            'name' => $this->string(30)->notNull()->comment('名称'),
            'icon' => $this->string(30)->notNull()->defaultValue('')->comment('图标'),
            'child' => $this->smallInteger(1)->unsigned()->notNull()->defaultValue(0)->comment('有子级'),
            'parent_arr' => $this->string()->notNull()->defaultValue(0)->comment('父级链'),
            'child_arr' => $this->text()->comment('子级群'),
            'controller' => $this->string(30)->notNull()->defaultValue('')->comment('控制器'),
            'action' => $this->string(30)->notNull()->defaultValue('')->comment('方法'),
            'params' => $this->string(255)->notNull()->defaultValue('')->comment('参数'),
            'auth_item' => $this->string(30)->notNull()->defaultValue('')->comment('权限'),
            'sort' => $this->bigInteger()->unsigned()->notNull()->defaultValue(0)->comment('排序'),
            'status' => $this->smallInteger(1)->notNull()->defaultValue(0)->comment('状态'),
        ], $tableOptions . " COMMENT='菜单'");

        $this->createIndex('Parent Id', '{{%menu}}', 'parent_id');
        $this->createIndex('Status', '{{%menu}}', 'status');

        $this->createTable('{{%order}}', [
            'order_id' => $this->bigInteger()->unsigned()->notNull()->comment('订单号'),
            'payment_id' => $this->bigInteger()->unsigned()->notNull()->comment('支付单号'),
            'user_id' => $this->bigInteger()->unsigned()->notNull()->comment('用户 ID'),
            'store_id' => $this->bigInteger()->unsigned()->notNull()->comment('店铺 ID'),
            'amount' => $this->decimal(20, 2)->unsigned()->notNull()->defaultValue(0.00)->comment('总金额'),
            'created_at' => $this->integer(10)->unsigned()->notNull()->defaultValue(0)->comment('生成时间'),
            'updated_at' => $this->integer(10)->unsigned()->notNull()->defaultValue(0)->comment('更新时间'),
            'status' => $this->smallInteger(1)->unsigned()->notNull()->defaultValue(0)->comment('状态'),
        ], $tableOptions . ' COMMENT="订单"');

        $this->addPrimaryKey('Order Id', '{{%order}}', 'order_id');

        $this->createTable('{{%order_goods}}', [
            'order_id' => $this->bigInteger()->unsigned()->notNull()->comment('订单号'),
            'goods_id' => $this->bigInteger()->unsigned()->notNull()->comment('商品 ID'),
            'quantity' => $this->smallInteger()->unsigned()->notNull()->defaultValue(0)->comment('数量'),
            'price' => $this->decimal(20, 2)->unsigned()->notNull()->defaultValue(0.00)->comment('单价'),
            'amount' => $this->decimal(20, 2)->unsigned()->notNull()->defaultValue(0.00)->comment('总金额'),
            'payment_status' => $this->smallInteger(1)->unsigned()->notNull()->defaultValue(0)->comment('支付状态'),
            'delivery_status' => $this->smallInteger(1)->unsigned()->notNull()->defaultValue(0)->comment('发货状态'),
            'status' => $this->smallInteger(1)->unsigned()->notNull()->defaultValue(0)->comment('状态'),
        ], $tableOptions . ' COMMENT="订单商品"');

        $this->addPrimaryKey('Order Goods Id', '{{%order_goods}}', ['order_id', 'goods_id']);

        $this->createTable('{{%payment}}', [
            'payment_id' => $this->bigInteger()->unsigned()->notNull()->comment('支付单号'),
            'user_id' => $this->bigInteger()->unsigned()->notNull()->comment('用户 ID'),
            'amount' => $this->decimal(20, 2)->unsigned()->notNull()->defaultValue(0.00)->comment('总金额'),
            'orders' => $this->text()->comment('订单资料'),
            'created_at' => $this->integer(10)->unsigned()->notNull()->defaultValue(0)->comment('生成时间'),
            'updated_at' => $this->integer(10)->unsigned()->notNull()->defaultValue(0)->comment('更新时间'),
            'status' => $this->smallInteger(1)->unsigned()->notNull()->defaultValue(0)->comment('状态'),
        ], $tableOptions . ' COMMENT="支付单"');

        $this->addPrimaryKey('Payment Id', '{{%payment}}', 'payment_id');

        $this->createTable('{{%store}}', [
            'store_id' => $this->bigPrimaryKey()->unsigned()->notNull()->comment('ID'),
            'name' => $this->string(100)->notNull()->comment('名称'),
            'preview' => $this->string(150)->notNull()->comment('主图'),
            'content' => $this->text()->comment('详情'),
            'created_at' => $this->integer(10)->unsigned()->notNull()->defaultValue(0)->comment('添加时间'),
            'updated_at' => $this->integer(10)->unsigned()->notNull()->defaultValue(0)->comment('修改时间'),
            'status' => $this->smallInteger(1)->unsigned()->notNull()->defaultValue(0)->comment('状态'),
        ], $tableOptions . ' COMMENT="店铺"');

        $this->createTable('{{%store_category}}', [
            'category_id' => $this->bigPrimaryKey()->unsigned()->comment('ID'),
            'store_id' => $this->bigInteger()->unsigned()->notNull()->comment('店铺 ID'),
            'parent_id' => $this->bigInteger()->unsigned()->notNull()->defaultValue(0)->comment('父级分类'),
            'name' => $this->string(60)->notNull()->comment('名称'),
            'child' => $this->smallInteger(1)->unsigned()->notNull()->defaultValue(0)->comment('是否有子级'),
            'parent_arr' => $this->string()->notNull()->defaultValue(0)->comment('父级链'),
            'child_arr' => $this->text()->comment('子级群'),
            'sort' => $this->bigInteger()->unsigned()->notNull()->defaultValue(0)->comment('排序'),
            'status' => $this->smallInteger(1)->unsigned()->notNull()->defaultValue(0)->comment('状态'),
        ], $tableOptions . ' COMMENT="店铺商品分类"');

        $this->createTable('{{%store_freight}}', [
            'freight_id' => $this->bigPrimaryKey()->unsigned()->comment('运费 ID'),
            'store_id' => $this->bigInteger()->unsigned()->notNull()->comment('店铺 ID'),
            'name' => $this->string(30)->notNull()->comment('名称'),
            'fee' => $this->decimal(20, 2)->unsigned()->notNull()->defaultValue(0.00)->comment('默认费用'),
            'area_fee' => $this->text()->comment('地区费用'),
        ], $tableOptions . " COMMENT='运费'");

        $this->createTable('{{%user}}', [
            'user_id' => $this->bigPrimaryKey()->unsigned()->notNull()->comment('ID'),
            'type' => $this->smallInteger(1)->unsigned()->notNull()->defaultValue(0)->comment('类型'),
            'referee' => $this->bigInteger()->unsigned()->notNull()->defaultValue(0)->comment('推荐人'),
            'agent' => $this->bigInteger()->unsigned()->notNull()->defaultValue(0)->comment('代理人'),
            'username' => $this->string()->notNull()->unique()->comment('用户名'),
            'auth_key' => $this->string(32)->notNull()->comment('密钥'),
            'access_token' => $this->string()->notNull()->comment('登录 Token'),
            'password_hash' => $this->string()->notNull()->comment('密码'),
            'mobile' => $this->string(60)->notNull()->unique()->comment('手机号码'),
            'created_at' => $this->integer(10)->unsigned()->notNull()->defaultValue(0)->comment('注册时间'),
            'updated_at' => $this->integer(10)->unsigned()->notNull()->defaultValue(0)->comment('登录时间'),
            'signup_at' => $this->integer(10)->unsigned()->notNull()->defaultValue(0)->comment('登录时间'),
            'expire_at' => $this->integer(10)->unsigned()->notNull()->defaultValue(0)->comment('过期时间'),
            'status' => $this->smallInteger(1)->unsigned()->notNull()->defaultValue(0)->comment('状态'),
        ], $tableOptions . ' COMMENT="用户"');

        $this->createTable('{{%user_address}}', [
            'address_id' => $this->bigPrimaryKey()->unsigned()->comment('地址 ID'),
            'user_id' => $this->bigInteger()->unsigned()->notNull()->comment('用户 ID'),
            'consignee' => $this->string(30)->notNull()->comment('收货人'),
            'area_id' => $this->bigInteger()->unsigned()->notNull()->comment('地区 ID'),
            'address' => $this->string()->notNull()->comment('详细地址'),
            'phone' => $this->string(60)->notNull()->comment('联系电话'),
            'is_default' => $this->smallInteger(1)->unsigned()->notNull()->defaultValue(0)->comment('默认'),
        ], $tableOptions . " COMMENT='收货地址'");

        $this->createIndex('User Id', '{{%user_address}}', 'user_id');
        $this->createIndex('Is Default', '{{%user_address}}', 'is_default');

        $this->createTable('{{%user_favorite}}', [
            'user_id' => $this->bigInteger()->unsigned()->notNull()->comment('用户 ID'),
            'type' => $this->smallInteger(1)->unsigned()->notNull()->defaultValue(0)->comment('类型'),
            'relation_id' => $this->bigInteger()->unsigned()->notNull()->comment('关联 ID'),
        ], $tableOptions . " COMMENT='收货地址'");

        $this->addPrimaryKey('User Favorite Id', '{{%user_favorite}}', ['user_id', 'type', 'relation_id']);
    }

    public function down()
    {
        $this->dropTable('{{%admin}}');
        $this->dropTable('{{%admin_auth}}');
        $this->dropTable('{{%admin_role}}');
        $this->dropTable('{{%area}}');
        $this->dropTable('{{%cart}}');
        $this->dropTable('{{%category}}');
        $this->dropTable('{{%goods}}');
        $this->dropTable('{{%goods_attribute}}');
        $this->dropTable('{{%goods_gallery}}');
        $this->dropTable('{{%goods_info}}');
        $this->dropTable('{{%menu}}');
        $this->dropTable('{{%order}}');
        $this->dropTable('{{%order_goods}}');
        $this->dropTable('{{%payment}}');
        $this->dropTable('{{%store}}');
        $this->dropTable('{{%store_category}}');
        $this->dropTable('{{%store_freight}}');
        $this->dropTable('{{%user}}');
        $this->dropTable('{{%user_address}}');
        $this->dropTable('{{%user_favorite}}');
    }
}
