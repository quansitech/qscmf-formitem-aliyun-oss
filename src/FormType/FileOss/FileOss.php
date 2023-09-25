<?php
namespace FormItem\AliyunOss\FormType\FileOss;

use Illuminate\Support\Str;
use Qscmf\Builder\FormType\FormType;
use Think\View;
use FormItem\AliyunOss\Lib\TUploadConfig;
use Qscmf\Builder\FormType\FileFormType;

class FileOss extends FileFormType implements FormType{
    use TUploadConfig;

    public function build(array $form_type){
        $upload_type_cls = $this->genUploadConfigCls($form_type['extra_attr'],'file');
        $view = new View();
        if($form_type['value']){
            $file['id'] = $form_type['value'];
	        $file['url'] = U('/extends/aliyunOss/download', ['file_id'=>$form_type['value']], '', true);

            if($this->needPreview(showFileUrl($form_type['value']))){
                $file['preview_url'] = $this->genPreviewUrl(showFileUrl($form_type['value']));
            }

            $view->assign('file', $file);
        }

        $view->assign('form', $form_type);
        $view->assign('gid', Str::uuid()->getHex());
        $view->assign('file_ext',  $upload_type_cls->getExts());
        $view->assign('js_fn', $this->buildJsFn());
        $content = $view->fetch(__DIR__ . '/file_oss.html');
        return $content;
    }
}