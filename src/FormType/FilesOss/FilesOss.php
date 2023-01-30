<?php
namespace FormItem\AliyunOss\FormType\FilesOss;

use Illuminate\Support\Str;
use Qscmf\Builder\FormType\FormType;
use Think\View;
use FormItem\AliyunOss\Lib\TUploadConfig;
use Qscmf\Builder\FormType\FileFormType;

class FilesOss extends FileFormType implements FormType{
    use TUploadConfig;

    public function build(array $form_type){
        $form_type['options'] = (array)$form_type['options'];
        $upload_type_cls = $this->genUploadConfigCls($form_type['extra_attr'],'file');

        $view = new View();
        if($form_type['value']){
            $files = [];
            foreach(explode(',', $form_type['value']) as $file_id){
                $data = [];
                $data['url'] = U('/extends/aliyunOss/download', array('file_id' => $file_id), '', true);

                $data['id'] = $file_id;
                if($this->needPreview(showFileUrl($file_id))){
                    $data['preview_url'] = $this->genPreviewUrl(showFileUrl($file_id));
                }
                $files[] = $data;
            }


            $view->assign('files', $files);
        }

        $view->assign('form', $form_type);
        $view->assign('gid', Str::uuid()->getHex());
        $view->assign('file_ext',  $upload_type_cls->getExts());
        $view->assign('js_fn', $this->buildJsFn());
        $content = $view->fetch(__DIR__ . '/files_oss.html');
        return $content;
    }
}