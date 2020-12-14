<?php

namespace common\models;

use ijony\helpers\File;
use Yii;

/**
 * This is the model class for table "{{%service_attachment}}".
 *
 * @property string $service_id
 * @property string $type
 * @property string $file
 */
class ServiceAttachment extends namespace\base\ServiceAttachment
{

    /**
     * {@inheritdoc}
     */
    public function afterDelete()
    {
        parent::afterDelete();

        File::delFile($this->file, true);
    }
}
