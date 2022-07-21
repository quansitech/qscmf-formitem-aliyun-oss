<?php

namespace FormItem\AliyunOss\Lib;

class UploadConfig
{
    protected array $config;

    public function __construct($type){
        $this->config = C('UPLOAD_TYPE_' . strtoupper($type));
    }

    public function __call($method,$args) {
        if (function_exists($this->$method)){
            $this->$method($args);
        }
        if(substr($method,0,3)==='get') {
            $key = lcfirst(substr($method, 3));
            return $this->config[$key];
        }
    }

}