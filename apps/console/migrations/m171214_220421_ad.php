<?php

use yii\db\Migration;

class m171214_220421_ad extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%ad}}', [
            'ad_id' => $this->bigPrimaryKey()->unsigned()->comment('广告 ID'),
            'type' => $this->integer()->unsigned()->notNull()->defaultValue(0)->comment('广告类型'),
            'name' => $this->string(150)->notNull()->comment('名称'),
            'image' => $this->string(150)->notNull()->comment('广告图'),
            'url' => $this->string(150)->notNull()->defaultValue('')->comment('链接'),
            'sort' => $this->bigInteger()->unsigned()->notNull()->defaultValue(0)->comment('排序'),
            'status' => $this->smallInteger(1)->unsigned()->notNull()->defaultValue(0)->comment('状态'),
        ], $tableOptions . " COMMENT='广告'");
    }

    public function down()
    {
        $this->dropTable('{{%ad}}');
    }
}
