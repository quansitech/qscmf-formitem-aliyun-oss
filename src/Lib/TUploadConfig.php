<?php

namespace FormItem\AliyunOss\Lib;

trait TUploadConfig
{

    protected function genUploadConfigCls(string $form_extra, string $def_type):UploadConfig{
        $type = $this->getType($form_extra, $def_type);
        return new UploadConfig($type);
    }

    protected function getType(string $form_extra, string $def_type):string{
        if (!$form_extra){
            return $def_type;
        }
        $regex = '/(data-url)(\s*=\s*[\"|\']?)(\S*)([\"|\']?)/';
        $r = preg_match($regex, $form_extra, $matches);
        if (!$r){
            return $def_type;
        }
        $url = $matches[3];
        $param_arr = extractParamsByUrl($url);
        return $param_arr ? $param_arr['type'] : $def_type;
    }
}