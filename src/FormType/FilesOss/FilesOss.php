<?php
namespace FormItem\AliyunOss\FormType\FilesOss;

use Illuminate\Support\Str;
use Qscmf\Builder\FormType\FormType;
use Think\View;
use FormItem\AliyunOss\Lib\TUploadConfig;

class FilesOss implements FormType{
    use TUploadConfig;

    public function build(array $form_type){
        $form_type['options'] = (array)$form_type['options'];
        $upload_type_cls = $this->genUploadConfigCls($form_type['extra_attr'],'file');

        $view = new View();
        $view->assign('form', $form_type);
        $view->assign('gid', Str::uuid()->getHex());
        $view->assign('file_ext',  $upload_type_cls->getExts());
        $content = $view->fetch(__DIR__ . '/files_oss.html');
        return $content;
    }
}