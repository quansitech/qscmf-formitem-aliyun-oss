<?php
namespace FormItem\AliyunOss\FormType\FilesOss;

use Illuminate\Support\Str;
use Qscmf\Builder\FormType\FormType;
use Think\View;

class FilesOss implements FormType{

    public function build(array $form_type){
        $form_type['options'] = (array)$form_type['options'];

        $view = new View();
        $view->assign('form', $form_type);
        $view->assign('gid', Str::uuid());
        $content = $view->fetch(__DIR__ . '/files_oss.html');
        return $content;
    }
}