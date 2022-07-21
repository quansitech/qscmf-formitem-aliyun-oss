<?php

namespace FormItem\AliyunOss\Behaviors;

use FormItem\AliyunOss\Lib\File;

class HeicToJpgBehavior{

    protected File $file;

    public function run(&$params)
    {
        if($params['url'] && $params['mime_type']){
            $this->file = new File($params['url'], $params['mime_type']);
            $this->formatHeicToJpg();
            $params['url'] = $this->file->getUrl();
        }
    }

    protected function formatHeicToJpg(){
        if ($this->isOssUrl() && $this->isHeic()) {
            $url = $this->file->getUrl();
            $this->file->setUrl(combineOssUrlImgOpt($url, 'format,jpg'));
        }
    }

    protected function isOssUrl():bool{
        $url = $this->file->getUrl();
        $aliyun_host = env("ALIOSS_HOST");

        return isUrl($url) && strpos($url, $aliyun_host) !== false;
    }

    protected function isHeic():bool{
        return in_array($this->file->getMimeType(),$this->getMimeTypes());
    }

    protected function getMimeTypes():array{
        return (new \Symfony\Component\Mime\MimeTypes())->getMimeTypes('heic');
    }

}