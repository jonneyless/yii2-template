<?php

use admin\models\Admin;
use admin\models\AdminRole;
use yii\db\Migration;

/**
 * import init data
 */
class m021215_153100_import_data extends Migration
{
    /**
     * @return bool|void
     */
    public function up()
    {
        $role = new AdminRole;
        $role->name = '管理员';
        $role->status = AdminRole::STATUS_ACTIVE;
        $role->save();

        $admin = new Admin;
        $admin->role_id = $role->id;
        $admin->username = 'admin';
        $admin->setPassword('123456');
        $admin->generateAuthKey();
        $admin->status = Admin::STATUS_ACTIVE;
        $admin->save();

        $this->execute("INSERT INTO `menu` (`id`, `parent_id`, `name`, `icon`, `child`, `parent_arr`, `child_arr`, `controller`, `action`, `params`, `auth_item`, `sort`, `status`) VALUES
(1, 0, '仪表盘', 'dashboard', 0, '0', '1', 'site', 'index', '', '', 0, 9),
(2, 0, '系统', 'cog', 1, '0', '2,3,4,5', '', '', '', '', 0, 9),
(3, 2, '角色', '', 0, '0,2', '3', 'role', '', '', '', 0, 9),
(4, 2, '权限', '', 0, '0,2', '4', 'auth', '', '', '', 0, 9),
(5, 2, '管理员', '', 0, '0,2', '5', 'admin', '', '', '', 0, 9);");
    }

    /**
     * @return bool|void
     */
    public function down()
    {
        $this->truncateTable('{{%admin}}');
        $this->truncateTable('{{%menu}}');
    }
}
