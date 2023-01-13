<?php
namespace FormItem\AliyunOss\FormType\PictureOss;

use FormItem\AliyunOss\Lib\TUploadConfig;
use Illuminate\Support\Str;
use Qscmf\Builder\FormType\FormType;
use Think\View;

class PictureOss implements FormType {
    use TUploadConfig;

    public function build(array $form_type){
        $upload_type_cls = $this->genUploadConfigCls($form_type['extra_attr'],'image');
        $view = new View();
        $view->assign('form', $form_type);
        $view->assign('gid', Str::uuid()->getHex());
        $view->assign('file_ext',  $upload_type_cls->getExts());
        $content = $view->fetch(__DIR__ . '/picture_oss.html');
        return $content;
    }
}