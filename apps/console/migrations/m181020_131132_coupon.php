<?php

use yii\db\Migration;

class m181020_131132_coupon extends Migration
{
    public function up()
    {
        $this->addColumn('{{%coupon}}', 'created_at', $this->integer()->unsigned()->notNull()->defaultValue(0)->comment('创建时间'));
        $this->addColumn('{{%coupon}}', 'updated_at', $this->integer()->unsigned()->notNull()->defaultValue(0)->comment('更新时间'));
    }

    public function down()
    {
    }
}
