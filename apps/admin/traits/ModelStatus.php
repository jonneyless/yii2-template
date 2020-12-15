<?php

namespace admin\traits;

use ijony\helpers\Utils;

/**
 * status 字段通用 model 方法
 *
 * @package admin\traits
 */
trait ModelStatus
{

    /**
     * @return mixed|string
     */
    public function getStatus()
    {
        $datas = $this->getStatusSelectData();

        return isset($datas[$this->status]) ? $datas[$this->status] : '';
    }

    /**
     * @return string
     */
    public function getStatusLabel()
    {
        if ($this->status == self::STATUS_ACTIVE) {
            $class = 'label-primary';
        } else {
            $class = 'label-danger';
        }

        return Utils::label($this->getStatus(), $class);
    }

    /**
     * @return array
     */
    public function getStatusSelectData()
    {
        return [
            self::STATUS_INACTIVE => '禁用',
            self::STATUS_ACTIVE => '启用',
        ];
    }
}