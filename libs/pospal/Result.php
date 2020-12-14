<?php

namespace libs\pospal;

/**
 * Class Result
 *
 * @property $status
 * @property $messages
 * @property $data
 * @property $errorCode
 *
 * @package libs\pospal
 */
class Result
{
    public $status;
    public $messages;
    public $data;
    public $errorCode;

    public function __construct($result)
    {
        foreach($result as $key => $value){
            $this->$key = $value;
        }
    }

    public function isSuccess()
    {
        return $this->status == 'success';
    }

    public function isCode($code)
    {
        return $this->errorCode == $code;
    }

    public function getCode()
    {
        return $this->errorCode;
    }

    public function getMessages()
    {
        return $this->messages;
    }

    public function getFirstData($key = '', $default = '')
    {
        if($key){
            return isset($this->data[0][$key]) ? $this->data[0][$key] : $default;
        }

        return $this->data[0];
    }

    /**
     * @param string $key
     * @param string $default
     *
     * @return string|array
     */
    public function getData($key = '', $default = '')
    {
        if($key){
            return isset($this->data[$key]) ? $this->data[$key] : $default;
        }

        return $this->data;
    }
}