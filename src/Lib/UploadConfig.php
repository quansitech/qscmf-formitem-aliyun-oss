<?php

namespace FormItem\AliyunOss\Lib;

class UploadConfig
{
    protected array $config;

    public function __construct($type){
        $this->config = C('UPLOAD_TYPE_' . strtoupper($type));
    }

    public function getExts(){
        return !empty($this->config['exts']) ? $this->config['exts'] : '*';
    }

    public function getMaxSize(){
        $maxSize = $this->config['maxSize'];
        return is_numeric($maxSize) && $maxSize > 0 ? $maxSize : 1024*1024*1024*1024;
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