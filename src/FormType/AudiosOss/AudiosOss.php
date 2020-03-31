<?php
namespace FormItem\AliyunOss\FormType\AudiosOss;

use Illuminate\Support\Str;
use Qscmf\Builder\FormType\FormType;
use Think\View;

class AudiosOss implements FormType {

    public function build(array $form_type){
        $view = new View();
        $view->assign('form', $form_type);
        $view->assign('gid', Str::uuid());
        $content = $view->fetch(__DIR__ . '/audios_oss.html');
        return $content;
    }
}