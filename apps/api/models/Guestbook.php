<?php

namespace api\models;

use Yii;

/**
 * This is the model class for table "{{%guestbook}}".
 *
 * {@inheritdoc}
 */
class Guestbook extends \common\models\Guestbook
{

    public function buildData()
    {
        return [
            'guestbook_id' => $this->guestbook_id,
            'type' => $this->type,
            'title' => $this->title,
            'content' => $this->content,
            'created_at' => date("Y-m-d", $this->created_at),
        ];
    }
}
