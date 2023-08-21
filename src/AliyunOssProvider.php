<?php
namespace FormItem\AliyunOss;

use Bootstrap\Provider;
use Bootstrap\LaravelProvider;
use Bootstrap\RegisterContainer;
use Composer\InstalledVersions;
use FormItem\AliyunOss\Controller\AliyunOssController;
use FormItem\AliyunOss\FormType\AudioOss\AudioOss;
use FormItem\AliyunOss\FormType\AudiosOss\AudiosOss;
use FormItem\AliyunOss\FormType\FileOss\FileOss;
use FormItem\AliyunOss\FormType\FilesOss\FilesOss;
use FormItem\AliyunOss\FormType\PictureOss\PictureOss;
use FormItem\AliyunOss\FormType\PictureOssIntercept\PictureOssIntercept;
use FormItem\AliyunOss\FormType\PicturesOss\PicturesOss;
use FormItem\AliyunOss\FormType\PicturesOssIntercept\PicturesOssIntercept;
use FormItem\AliyunOss\ColumnType\PictureOss\PictureOss as ColumnPictureOss;
use FormItem\AliyunOss\ColumnType\FileOss\FileOss as ColumnFileOss;

class AliyunOssProvider implements Provider, LaravelProvider {

    public function register(){
        $this->addHook();

        RegisterContainer::registerFormItem('audio_oss', AudioOss::class);
        RegisterContainer::registerFormItem('audios_oss', AudiosOss::class);
        RegisterContainer::registerFormItem('file_oss', FileOss::class);
        RegisterContainer::registerFormItem('files_oss', FilesOss::class);
        RegisterContainer::registerFormItem('picture_oss', PictureOss::class);
        RegisterContainer::registerFormItem('pictures_oss', PicturesOss::class);
        RegisterContainer::registerFormItem('picture_oss_intercept', PictureOssIntercept::class);
        RegisterContainer::registerFormItem('pictures_oss_intercept', PicturesOssIntercept::class);
        RegisterContainer::registerListColumnType("picture_oss",ColumnPictureOss::class);
	    RegisterContainer::registerListColumnType("file_oss",ColumnFileOss::class);

        RegisterContainer::registerController('extends', 'AliyunOss', AliyunOssController::class);

        RegisterContainer::registerSymLink(WWW_DIR . '/Public/aliyun-oss', __DIR__ . '/../asset/aliyun-oss');
    }

    protected function addHook(){
        \Think\Hook::add('heic_to_jpg', 'FormItem\\AliyunOss\\Behaviors\\HeicToJpgBehavior');
        \Think\Hook::add('get_auth_url', 'FormItem\\AliyunOss\\Behaviors\\SecurityUrlBehavior');
    }

    public function registerLara()
    {
        $think_core_version = InstalledVersions::getVersion("tiderjian/think-core");
        //think-core 13版本会增加mime_type的长度
        //12以下版本由于长度太小，office类的文件上传都会失败
        if($think_core_version < 13){
            RegisterContainer::registerMigration(__DIR__.'/migrations_v12fix');
        }

    }
}