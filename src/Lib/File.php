<?php

namespace FormItem\AliyunOss\Lib;

class File
{
    protected string $url;
    protected string $mime_type;

    public function __construct($url, $mime_type){
        $this->setUrl($url);
        $this->mime_type = $mime_type;
    }

    public function getUrl():string{
        return $this->url;
    }

    public function getMimeType():string{
        return $this->mime_type;
    }

    public function setUrl($url):self{
        $this->url = $url;
        return $this;
    }

}