<?php

use yii\db\Migration;

class m180210_210532_user_income extends Migration
{
    public function up()
    {
        $this->dropColumn('{{%user}}', 'agent');
        $this->dropColumn('{{%user}}', 'signup_at');
        $this->renameColumn('{{%admin}}', 'signup_at', 'signin_at');
        $this->createTable('{{%user_income}}', [
            'user_id' => $this->bigInteger()->unsigned()->notNull()->comment('用户 ID'),
            'type' => $this->string(20)->notNull()->comment('类型'),
            'relation_id' => $this->bigInteger()->unsigned()->notNull()->comment('关联 ID'),
            'amount' => $this->decimal(20, 2)->unsigned()->notNull()->defaultValue(0.00)->comment('金额'),
            'description' => $this->string()->notNull()->defaultValue('')->comment('说明'),
            'extend' => $this->string()->notNull()->defaultValue('')->comment('扩展数据'),
            'date' => $this->string(7)->notNull()->defaultValue('0000-00')->comment('年月'),
            'created_at' => $this->integer(10)->unsigned()->notNull()->defaultValue(0)->comment('生成时间'),
            'updated_at' => $this->integer(10)->unsigned()->notNull()->defaultValue(0)->comment('更新时间'),
        ], "CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB COMMENT='收益'");

        /* @var \api\models\UserRenew[] $renews */
        $renews = \api\models\UserRenew::find()->where(['status' => \api\models\UserRenew::STATUS_DONE])->all();
        foreach($renews as $renew){
            $renew->setReward();
        }
    }

    public function down()
    {
        $this->dropTable('{{%user_income}}');
        $this->addColumn('{{%user}}', 'agent', $this->bigInteger()->unsigned()->notNull()->defaultValue(0)->comment('代理人')->after('referee'));
        $this->addColumn('{{%user}}', 'signup_at', $this->integer(10)->unsigned()->notNull()->defaultValue(0)->comment('登录时间')->after('updated_at'));
        $this->renameColumn('{{%admin}}', 'signin_at', 'signup_at');
    }
}
