<?php

use yii\db\Migration;

class m180104_123242_cart extends Migration
{
    public function up()
    {
        $this->addColumn('{{%cart}}', 'created_at', $this->integer(10)->unsigned()->notNull()->defaultValue(0)->comment('生成时间'));
        $this->addColumn('{{%cart}}', 'updated_at', $this->integer(10)->unsigned()->notNull()->defaultValue(0)->comment('更新时间'));
    }

    public function down()
    {
        $this->dropColumn('{{%cart}}', 'created_at');
        $this->dropColumn('{{%cart}}', 'updated_at');
    }
}
