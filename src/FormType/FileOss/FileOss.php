<?php
namespace FormItem\AliyunOss\FormType\FileOss;

use Illuminate\Support\Str;
use Qscmf\Builder\FormType\FormType;
use Think\View;

class FileOss implements FormType{

    public function build($form_type){
        $view = new View();
        $view->assign('form', $form_type);
        $view->assign('gid', Str::uuid());
        $content = $view->fetch(__DIR__ . '/file_oss.html');
        return $content;
    }
}