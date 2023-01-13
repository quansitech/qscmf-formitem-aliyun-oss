<?php
namespace FormItem\AliyunOss\ColumnType\PictureOss;

use FormItem\AliyunOss\Lib\TUploadConfig;
use Qscmf\Builder\ColumnType\ColumnType;
use Illuminate\Support\Str;
use Qscmf\Builder\ColumnType\EditableInterface;
use Qscmf\Builder\ButtonType\Save\TargetFormTrait;
use Think\View;

class PictureOss extends ColumnType implements EditableInterface {

    use TUploadConfig;
    use TargetFormTrait;

    public function build(array &$option, array $data, $listBuilder){
        $view = new View();
        $image = [
            'url' => showFileUrl($data[$option['name']]),
        ];
        $image['small_url'] = $image['url'] . '?x-oss-process=image/resize,m_fill,w_40,h_40';
        $view->assign('image', $image);
        $content = $view->fetch(__DIR__ . '/picture_oss.html');
        return $content;
    }

    public function editBuild(array &$option, array $data, $listBuilder){
        $class = $this->getSaveTargetForm();

        $image = [
            'url' => showFileUrl($data[$option['name']]),
        ];
        $image['small_url'] = $image['url'] . '?x-oss-process=image/resize,m_fill,w_40,h_40';

        $upload_type_cls = $this->genUploadConfigCls($option['extra_attr'],'image');

        $view = new View();
        $view->assign('option', $option);
        $view->assign('image', $image);
        $view->assign('class', $class);
        $view->assign('image_id', $data[$option['name']]);
        $view->assign('gid', Str::uuid()->getHex());
        $view->assign('file_ext',  $upload_type_cls->getExts());
        $content = $view->fetch(__DIR__ . '/picture_oss_editable.html');
        return $content;
    }
}