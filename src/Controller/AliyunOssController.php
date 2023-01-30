<?php

namespace FormItem\AliyunOss\Controller;

use FormItem\AliyunOss\Lib\AliyunOss;
use FormItem\AliyunOss\Lib\UploadConfig;

class AliyunOssController extends \Think\Controller{

    public function callBack(){
        $r = $this->_verify($body);
        if($r === false){
            exit();
        }

        parse_str($body, $body_arr);
        $config = C('UPLOAD_TYPE_' . strtoupper($body_arr['upload_type']));
        if(!$config){
            E('获取不到文件规则config设置');
        }

        $mime_type = $body_arr['image_format'] ?
            $this->intersectMimeType($body_arr['image_format'],$body_arr['mimeType']) :
            $body_arr['mimeType'];
        if(!empty($config['mimes'])){
            $mimes = explode(',', $config['mimes']);
            if(!in_array(strtolower($mime_type), $mimes)){
                $this->ajaxReturn(array('err_msg' => '上传的文件类型不符合要求'));
            }
        }
        if ($body_arr['title']){
            $file_data['title'] = $body_arr['title'];
        }else {
            $name_arr = explode('/', $body_arr['filename']);
            $file_data['title'] = end($name_arr);
        }

        $file_data['url'] = $config['oss_host'] . '/' . $body_arr['filename'] . ($config['oss_style'] ? $config['oss_style'] : '');
        $file_data['size'] = $body_arr['size'];
        $file_data['cate'] = $body_arr['upload_type'];
        \Think\Log::write($mime_type);
        $file_data['mime_type'] = $mime_type;
        $file_data['security'] = $config['security'] ? 1 : 0;
        $file_data['file'] = '';

        C('TOKEN_ON',false);
        $r = D('FilePic')->createAdd($file_data);
        if($r === false){
            E(D('FilePic')->getError());
        }
        else{
            if($file_data['security'] == 1){
                $ali_oss = new AliyunOss();
                $file_data['url'] = $ali_oss->getOssClient($body_arr['upload_type'])->signUrl($body_arr['filename'], 60);
            }
            \Think\Hook::listen('heic_to_jpg', $file_data);
            $this->ajaxReturn(array('file_id' => $r, 'file_url' => $file_data['url']));
        }
    }

    public function policyGet($type){
        $callbackUrl = HTTP_PROTOCOL . '://' . SITE_URL . '/extends/AliyunOss/callBack';

        $callback_param = array('callbackUrl'=>$callbackUrl,
                 'callbackBody'=>'filename=${object}&size=${size}&mimeType=${mimeType}&upload_type=${x:upload_type}&image_format=${imageInfo.format}',
                 'callbackBodyType'=>"application/x-www-form-urlencoded");
        if (I('get.title')){
            $callback_param['callbackBody'].='&title=${x:title}';
        }
        $callback_string = json_encode($callback_param);
        $base64_callback_body = base64_encode($callback_string);
        $now = time();
        $expire = 10;
        $end = $now + $expire;
        $expiration = gmt_iso8601($end);

        $config = C('UPLOAD_TYPE_' . strtoupper($type));
//        $sub_name = $this->_getName($config['subName']);
//        $pre_path = $config['rootPath'] . $config['savePath'] . $sub_name .'/';
//        $save_name = $this->_getName($config['saveName']);
//
//        $dir = trim(trim($pre_path . $save_name, '.'), '/');

        $dir = AliyunOss::genOssObjectName($config);
        $condition = array(0=>'content-length-range', 1=>0, 2=> $this->getMaxSize($type));

        $conditions[] = $condition;

        $start = array(0=>'starts-with', 1=>'$key', 2=>$dir);
        $conditions[] = $start;

        $arr = array('expiration'=>$expiration,'conditions'=>$conditions);

        $policy = json_encode($arr);
        $base64_policy = base64_encode($policy);
        $string_to_sign = $base64_policy;
        $signature = base64_encode(hash_hmac('sha1', $string_to_sign, C('ALIOSS_ACCESS_KEY_SECRET'), true));

        $callback_var = array('x:upload_type' => $type);
        if (I('get.title')){
            $callback_var['x:title'].=I('get.title');
        }
        $callback_var=json_encode($callback_var);

        $response = array();
        $response['accessid'] = C('ALIOSS_ACCESS_KEY_ID');
        $response['host'] = $config['upload_oss_host'] ?? $config['oss_host'];
        $response['policy'] = $base64_policy;
        $response['signature'] = $signature;
        $response['expire'] = $end;
        $response['callback'] = $base64_callback_body;
        $response['callback_var'] = $callback_var;
        if($config['oss_meta']){
            $get_data = I('get.');
            foreach($config['oss_meta'] as $k => &$vo){
                $vo = preg_replace_callback('/__(\w+?)__/', function($matches) use($get_data){
                    return $get_data[$matches[1]];
                }, $vo);


                if(strtolower($k) == 'content-disposition' && preg_match("/attachment;\s*?filename=(.+)/", $vo, $matches)){
                    $vo = preg_replace_callback("/attachment;\s*?filename=(.+)/", function($matches){
                        return 'attachment;filename=' . urlencode($matches[1]) . ";filename*=utf-8''" . urlencode($matches[1]);
                    }, $vo);
                }
            }
            $response['oss_meta'] = json_encode($config['oss_meta']);
        }
        //这个参数是设置用户上传指定的前缀
        $response['dir'] = $dir;
        $this->ajaxReturn($response);
    }

    private function _verify(&$body){
        $authorizationBase64 = "";
        $pubKeyUrlBase64 = "";

        if (isset($_SERVER['HTTP_AUTHORIZATION']))
        {
            $authorizationBase64 = $_SERVER['HTTP_AUTHORIZATION'];
        }
        if (isset($_SERVER['HTTP_X_OSS_PUB_KEY_URL']))
        {
            $pubKeyUrlBase64 = $_SERVER['HTTP_X_OSS_PUB_KEY_URL'];
        }

        if ($authorizationBase64 == '' || $pubKeyUrlBase64 == '')
        {
            return false;
        }

        $authorization = base64_decode($authorizationBase64);
        $pubKeyUrl = base64_decode($pubKeyUrlBase64);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $pubKeyUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        $pubKey = curl_exec($ch);

        if ($pubKey == "")
        {
            return false;
        }

        $body = file_get_contents('php://input');
        $authStr = '';
        $path = REQUEST_URI;
        $pos = strpos($path, '?');

        if ($pos === false)
        {
            $authStr = urldecode($path)."\n".$body;
        }
        else
        {
            $authStr = urldecode(substr($path, 0, $pos)).substr($path, $pos, strlen($path) - $pos)."\n".$body;
        }

        $ok = openssl_verify($authStr, $authorization, $pubKey, OPENSSL_ALGO_MD5);
        if ($ok == 1)
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    protected function intersectMimeType(string $format, string $mime_type):string{
        if (empty($format)){
            return $mime_type;
        }
        $guess_mime_type = (new \Symfony\Component\Mime\MimeTypes())->getMimeTypes($format);
        if ($guess_mime_type){
            return $guess_mime_type[0];
        }

        return $mime_type;
    }

    protected function getMaxSize($type){
        return (new UploadConfig($type))->getMaxSize();
    }

    public function download(int $file_id){
        $ent = D("FilePic")->where(['id' => $file_id])->find();
        $url = showFileUrl($file_id);
        header("Content-type: application/force-download");
        header('Content-Disposition: inline; filename="' . $ent['title'] . '"');
        header("Content-Transfer-Encoding: Binary");
        header("Content-length: " . $ent['size']);
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . $ent['title'] . '"');
        echo file_get_contents($url);
    }
}
