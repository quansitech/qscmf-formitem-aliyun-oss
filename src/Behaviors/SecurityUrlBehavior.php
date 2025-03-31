<?php
namespace FormItem\AliyunOss\Behaviors;

use FormItem\AliyunOss\Lib\AliyunOss;

class SecurityUrlBehavior{

    public function run(&$params)
    {

        if(isset($params['auth_url'])){
            return $params['auth_url'];
        }

        $file_ent = $params['file_ent'];

        $config = C('UPLOAD_TYPE_' . strtoupper($file_ent['cate']));
        $object = trim(str_replace($config['oss_host'], '', $file_ent['url']), '/');

        $ali_oss = new AliyunOss();
        $url = $ali_oss->getOssClient($file_ent['cate'])->signUrl($object, $params['timeout']);
        if($url){
            if(isset($config['oss_public_host'])){
                $show_host = $config['oss_public_host'];
                $url = str_replace($config['oss_host'], $show_host, $url);
            }
            $params['auth_url'] = $url;
        }
    }
}