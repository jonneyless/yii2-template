<?php

namespace api\models;

use Yii;

/**
 * This is the model class for table "{{%service_attachment}}".
 *
 * @property string $service_id
 * @property string $type
 * @property string $file
 */
class ServiceAttachment extends \common\models\ServiceAttachment
{

    public function isImage()
    {
        return $this->type == 'image';
    }
}
