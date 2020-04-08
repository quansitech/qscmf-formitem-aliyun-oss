# qscmf-formitem-aliyun-oss
上传文件至阿里云OSS组件

#### 安装

```php
composer require quansitech/qscmf-formitem-aliyun-oss
```

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


  ## <a name="oss_uploader">oss_uploader oss上传图片</a>
  
  ### 介绍
  
  图片裁剪上传功能,内置cropper.js,可配置是否裁剪,可配置oss。
  
  ### 功能：
  
  * 可配置使用oss
  * 可配置是否裁剪
  * 可以定义裁剪尺寸,裁剪比例
  * 可以定义限制文件后缀、大小以及是否允许选取重复文件
  * 限制上传张数
  * 可自定义提示函数
  
  ### 初始化调用
  
  ```console
  $(selector).ossuploader(option); //selector 为隐藏域
  
  option: {
      url:                //string require  上传图片的地址
      multi_selection:    //boolean optional 是否多选
      oss:                //boolean optional 是否启用oss
      crop:{              //object optional cropper配置,若存在此项，则裁剪图片,更多配置请参考cropper.js官网
          aspectRatio: 120/120,
          viewMode: 1,
          ready: function () { 
              croppable = true;
          }
      },
      filters: {
           check_image:         // Boolean 是否检查图片类型
           max_file_size:       // String|Number 限制文件大小，参考格式："10mb"，单位为字节,byte
           prevent_duplicates:  // Boolean 是否允许选取重复文件，false：是，true 否，默认为false
      },
      show_msg:           //function optional 展示提示消息的函数,默认为window.alert
      limit:              //number   optional 上传图片张数的限制,默认值32
      beforeUpload:       //function optional 回调 参考回调说明
      uploadCompleted:    //function optional 回调 参考回调说明
  }
  ```
  备注:
  1. cropper：
      - <a href="https://fengyuanchen.github.io/cropper/">官网demo</a>  
      - <a href="https://github.com/fengyuanchen/cropper/blob/master/README.md">github</a>
  
  2. 回调说明:
      - beforeUpload : 当选中文件时的回调。若返回false,则不添加选中的文件
      - uploadCompleted : 上传成功的回调
  
  ## <a name="oss_extend_desc">oss_upload_extend oss上传扩展</a>
  
  ### 介绍
  
  基于oss上传插件，对上传插件的回调进行扩展，增强oss插件上传功能
  
  ### 功能：
  
  * 可以组合添加拓展
  * 可以对扩展进行排序
  * <a href="#preventUpload">内置preventUpload扩展</a>
  * 根据oss_upload的回调,添加各自的回调
  
  
  ### 初始化调用
  
  ```console
  $(selector).ossuploaderWrapper(option[, extend]); //selector 为隐藏域
  option: object require 原oss上传插件的option
  extend: string_array optional 扩展名
  ```
  
  链接：
  * <a href="#oss_uploader">oss_uploader</a>  
  * 扩展名: <a href="#extend_desc">扩展介绍</a>
  
  
  ## 扩展说明
  ### 执行逻辑
  * 根据传入的extend进行排序
  * 把extend遍历,从conf中取出要扩展的配置
  * 把配置中的回调放到相应的队列中
  * 当触发相应的回调,则执行相应队列中的函数 
  * 若队列中有函数返回false,则队列后面的函数都不执行
  
  ### <a name="extend_desc">内置扩展介绍</a>
  
  #### <a name="preventUpload">preventUpload</a>(默认扩展)
  上传图片的过程中,隐藏域所在的form
  * 会禁止submit事件(submit事件禁止且提示图片上传中)
  * type为submit 的按钮会显示为上传中，当上传完成会恢复原来的描述
  ```console
  $(selector).ossuploaderWrapper(option, ['preventUpload']); //selector 为隐藏域
  ```
  
  ### 添加自定义扩展
  
  conf 对象内置了preventUpload扩展  
  
  往conf对象加属性对象，实现扩展的功能 
  
  ```console
  var conf = {
      myExtend: {
          invoke: function(){
                  return {};
              } || {} //require
          order: number, // >=2,optional
      }
  };
  ```
  * invoke 属性必须为<code>返回对象的函数</code>或<code>纯对象</code>  
    <code>返回对象</code>或<code>纯对象</code>属性包含回调,如 beforeUpload,uploadCompleted 等
    扩展的回调队列中，任意一个函数返回false,都会停止执行后续回调   
  
  * order属性为插件的调用排序,值越小调用顺序越早;  
    由于preventUpload扩展(内置扩展)的order为 1,  
    其他默认内置扩展大于1，
    非默认内置扩展大于100。
