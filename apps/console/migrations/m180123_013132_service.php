<?php

use yii\db\Migration;

class m180123_013132_service extends Migration
{
    public function up()
    {
        $this->addPrimaryKey('Service File Id', '{{%service_attachment}}', ['service_id', 'type', 'file']);
        $this->addPrimaryKey('Comment Image Id', '{{%comment_image}}', ['comment_id', 'image']);
    }

    public function down()
    {
        $this->dropPrimaryKey('Service File Id', '{{%service_attachment}}');
        $this->dropPrimaryKey('Comment Image Id', '{{%comment_image}}');
    }
}
