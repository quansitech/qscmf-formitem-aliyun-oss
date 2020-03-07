# qscmf-formitem-aliyun-oss
上传文件至阿里云OSS组件

#### 如何使用
+ 修改.env，设置相关配置
```blade
ALIOSS_ACCESS_KEY_ID=**********
ALIOSS_ACCESS_KEY_SECRET=**********
ALIOSS_HOST=**********
```

+ 修改/app/Common/Conf/config.php，配置对应上传类型，如：
```php
//UPLOAD_TYPE_*** 其中***为对应的type

'UPLOAD_TYPE_FILE' => array(
    'mimes'    => '', //允许上传的文件MiMe类型
    'maxSize'  => 500*1024*1024, //上传的文件大小限制 (0-不做限制)
    'exts'     => 'doc,docx,xls,xlsx,pdf,ppt,txt,rar,pptx,rtf,zip,7z,jpg,png', //允许上传的文件后缀
    'autoSub'  => true, //自动子目录保存文件
    'subName'  => array('date','Ymd'), //子目录创建方式，[0]-函数名，[1]-参数，多个参数使用数组
    'rootPath' => './Uploads/', //保存根路径
    'savePath' => 'file/', //保存路径
    'saveName' => array('uniqid', ''), //上传文件命名规则，[0]-函数名，[1]-参数，多个参数使用数组
    'saveExt'  => '', //文件保存后缀，空则使用原后缀
    'replace'  => false, //存在同名是否覆盖
    'hash'     => true, //是否生成hash编码
    'callback' => false, //检测文件是否存在回调函数，如果存在返回文件信息数组
    'oss_host' => env('ALIOSS_HOST'),
    'oss_meta' => array('Cache-Control' => 'max-age=2592000', 'Content-Disposition' => 'attachment'),
),
```

+ 使用 
  + 上传音频：audio_oss/audios_oss
  
  ```php
  // audio_oss为上传单个音频，audios_oss为上传多个音频
  
  $extra_attr = 'data-url='.U('/extends/AliyunOss/policyGet', array('type' => 'audio')); // 默认值
  // 如没有特别需求，$extra_attr可不传
  addFormItem('audio_id', 'audio_oss', '单个音频', '', '', '', $extra_attr)
  addFormItem('audios_id', 'audios_oss', '多个音频')
  ```

  + 上传文件：file_oss/files_oss
  
  ```php
  // file_oss为上传单个文件，files_oss为上传多个文件
   
  $extra_attr = 'data-url='.U('/extends/AliyunOss/policyGet', array('type' => 'file')); // 默认值
  // 如没有特别需求，$extra_attr可不传
  addFormItem('file_id', 'file_oss', '单个文件', '', '', '', $extra_attr)
  addFormItem('files_id', 'files_oss', '多个文件')
  ```

  + 上传图片：picture_oss/pictures_oss
  
  ```php
  // picture_oss为上传单张图片，pictures_oss为上传多张图片
  
  $extra_attr = 'data-url='.U('/extends/AliyunOss/policyGet', array('type' => 'image')); // 默认值
  // 如没有特别需求，$extra_attr可不传
  addFormItem('picture_id', 'picture_oss', '单张图片', '', '', '', $extra_attr)
  addFormItem('pictures_id', 'pictures_oss', '多张图片')
  ```
  
  + 上传裁剪后的图片：picture_oss_intercept/pictures_oss_intercept
  
  ```php
  // picture_oss_intercept为上传单张裁剪后的图片，pictures_oss_intercept为上传多张裁剪后的图片
  
  $option = [
      'type' => 'image', // 默认值
      'width' => 1, // 裁剪框宽高比例，此为宽度，默认为1
      'height' => 1 // 裁剪框宽高比例，此为高度，默认1
  ];
                      
  // 如没有特别需求，$option可不传
  addFormItem('cover', 'picture_oss_intercept', '单张裁剪后的图片', '', $option)
  addFormItem('covers', 'pictures_oss_intercept', '多张裁剪后的图片')
  ```

