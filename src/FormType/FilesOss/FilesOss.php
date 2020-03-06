<?php
namespace FormItem\AliyunOss\FormType\FilesOss;

use Illuminate\Support\Str;
use Qscmf\Builder\FormType\FormType;
use Think\View;

class FilesOss implements FormType{

    public function build($form_type){
        $view = new View();
        $view->assign('form', $form_type);
        $view->assign('gid', Str::uuid());
        $content = $view->fetch(__DIR__ . '/files_oss.html');
        return $content;
    }
}