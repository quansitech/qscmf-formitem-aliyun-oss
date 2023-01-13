<?php
namespace FormItem\AliyunOss\FormType\PicturesOss;

use FormItem\AliyunOss\Lib\TUploadConfig;
use Illuminate\Support\Str;
use Qscmf\Builder\FormType\FormType;
use Think\View;

class PicturesOss implements FormType {

    use TUploadConfig;

    public function build(array $form_type){
        $upload_type_cls = $this->genUploadConfigCls($form_type['extra_attr'],'image');
        $view = new View();
        $view->assign('form', $form_type);
        $view->assign('gid', Str::uuid()->getHex());
        $view->assign('file_ext',  $upload_type_cls->getExts());
        $content = $view->fetch(__DIR__ . '/pictures_oss.html');
        return $content;
    }
}