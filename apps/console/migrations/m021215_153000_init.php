<?php

use yii\db\Migration;

/**
 * Create database
 */
class m021215_153000_init extends Migration
{
    /**
     * @return bool|void
     */
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
            'username' => $this->string(24)->notNull()->comment('用户名'),
            'password_hash' => $this->string(64)->notNull()->defaultValue('')->comment('登录密码'),
            'auth_key' => $this->string(32)->notNull()->defaultValue('')->comment('登录保持密钥'),
            'created_at' => $this->integer(10)->unsigned()->notNull()->defaultValue(0)->comment('注册时间'),
            'updated_at' => $this->integer(10)->unsigned()->notNull()->defaultValue(0)->comment('更新时间'),
            'signin_at' => $this->integer(10)->unsigned()->notNull()->defaultValue(0)->comment('登录时间'),
            'status' => $this->tinyInteger(1)->unsigned()->notNull()->defaultValue(0)->comment('状态'),
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

        $this->createTable('{{%menu}}', [
            'id' => $this->bigPrimaryKey()->unsigned()->comment('菜单 ID'),
            'parent_id' => $this->bigInteger(20)->unsigned()->notNull()->defaultValue(0)->comment('父级'),
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
            'status' => $this->tinyInteger(1)->unsigned()->notNull()->defaultValue(0)->comment('状态'),
        ], $tableOptions . " COMMENT='菜单'");

        $this->createTable('{{%user}}', [
            'id' => $this->bigPrimaryKey()->unsigned()->comment('用户 ID'),
            'username' => $this->string(30)->notNull()->unique()->comment('用户名'),
            'password_hash' => $this->string(64)->notNull()->defaultValue('')->comment('登录密码'),
            'password_reset_token' => $this->string(64)->null()->unique()->comment('密码重置 Token'),
            'verification_token' => $this->string(64)->null()->unique()->comment('验证 Token'),
            'email' => $this->string(128)->notNull()->unique()->comment('邮箱'),
            'auth_key' => $this->string(32)->notNull()->comment('登录保持密钥'),
            'created_at' => $this->integer(10)->unsigned()->notNull()->defaultValue(0)->comment('创建时间'),
            'updated_at' => $this->integer(10)->unsigned()->notNull()->defaultValue(0)->comment('更新时间'),
            'status' => $this->tinyInteger(1)->unsigned()->notNull()->defaultValue(0)->comment('状态'),
        ], $tableOptions);
    }

    /**
     * @return bool|void
     */
    public function down()
    {
        $this->dropTable('{{%admin}}');
        $this->dropTable('{{%admin_auth}}');
        $this->dropTable('{{%admin_role}}');
        $this->dropTable('{{%menu}}');
        $this->dropTable('{{%user}}');
    }
}
