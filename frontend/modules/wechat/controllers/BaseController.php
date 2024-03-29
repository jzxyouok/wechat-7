<?php

namespace modules\wechat\controllers;

use yii;
use yii\helpers\Url;
use yii\web\Response;
use yii\web\Controller;
use yii\web\UploadedFile;
use modules\wechat\models\Wechat;

/**
 * 微信控制器基类
 * 所有微信模块的控制器必须继承此类
 *
 * @package components
 */
abstract class BaseController extends Controller
{
    /**
     * 发送消息
     * @param $message
     * @param string $type
     * @param null $redirect
     * @param null $resultType
     * @return array|bool|string
     */
    public function message($message, $type = 'error', $redirect = null, $resultType = null)
    {
        $request = Yii::$app->getRequest();
        if ($resultType === null) {
            $resultType = $request->getIsAjax() ? 'json' : 'html';
        } elseif ($resultType === 'flash') {
            $resultType = Yii::$app->getRequest()->getIsAjax() ? 'json' : $resultType;
        }
        $data = [
            'type' => $type,
            'message' => $message,
            'redirect' => $redirect === null ? null : Url::to($redirect)
        ];
        switch ($resultType) {
            case 'html':
                return $this->render(Yii::$app->getModule('wechat')->messageLayout, $data);
            case 'json':
                Yii::$app->getResponse()->format = Response::FORMAT_JSON;
                return $data;
            case 'flash':
                Yii::$app->session->setFlash($type, $message);
                $data['redirect'] == null && $data['redirect'] = $request->getReferrer();
                Yii::$app->end(0, $this->redirect($data['redirect']));
                return true;
            default:
                return $message;
        }
    }

    /**
     * flash消息
     * @param $message
     * @param string $status
     * @param null $redirect
     * @return array|bool|string
     */
    public function flash($message, $status = 'error', $redirect = null) {
        return $this->message($message, $status, $redirect, 'flash');
    }

    /**
     * 上传文件、图片
     * @param $files_上传数据
     * @param $saveFile_保存路径
     * @return string
     */
    public function uploadTo($files,$saveFile)
    {
        if(empty($saveFile)){
            return ['status'=>0,'info'=>'请先设置保存路径！'];
        }

        $ext = $files->getExtension();//后缀名
        $imageName = time().'_'.rand(1000,9999).'.'.$ext;//文件名

        $this->MkFolder($saveFile);//判断目录是否存在，不存在则创建
        $file=$saveFile.'/'.$imageName;//文件完整路径
        $status=$files->saveAs($file);//上传文件到指定目录

        if($status){
            return ['status'=>1,'info'=>$file];
        }else{
            return ['status'=>0,'info'=>'上传失败！'];
        }

    }

    /**
     * 判断目录是否存在，不存在则创建
     * @param $path
     * @return string
     */
    protected function MkFolder($path){
        if(!is_readable($path)){
            $this->MkFolder( dirname($path) );
            if(!is_file($path)) mkdir($path,0777);
        }
    }

    /**
     * ajax上传文件接口
     * $filename 保存文件夹名
     * @throws NotFoundHttpException
     */
    public function actionAjaxUpload($filename)
    {
        $files = static::getFiles();

        $uploadedFile='';
        if (!empty($files)) {
            foreach ($files as $name) {
                if(is_array($name)){
                    $uploadedFile = UploadedFile::getInstancesByName($name);//多文件上传
                }else{
                    $uploadedFile = UploadedFile::getInstanceByName($name);//单文件上传
                }
            }

            if(empty($filename)){
                $saveFile='uploads/'.$this->getWechat()->id.'/'.date('Ymd',time());//保存路径
            }else{
                $saveFile='uploads/'.$filename.'/'.$this->getWechat()->id.'/'.date('Ymd',time());//保存路径
            }


            $status=$this->uploadTo($uploadedFile,$saveFile);
            if($status['status']==1){
                $data=['type'=>'success','message'=>['path'=>$status['info']]];
                return json_encode($data);
            }else{
                $data=['type'=>'error','message'=>$status['info']];
                return json_encode($data);
            }
        }else{
            $data=['type'=>'error','message'=>'上传失败'];
            return json_encode($data);
        }
    }

    /**
     * @var array
     */
    private static $_files;

    /**
     * 获取上传文件
     * @return array
     */
    public static function getFiles()
    {
        if (self::$_files === null) {
            self::$_files = [];
            if (isset($_FILES) && is_array($_FILES)) {
                foreach ($_FILES as $class => $info) {
                    self::getUploadFilesRecursive($class, $info['name'], $info['error']);
                }
            }
        }

        return self::$_files;
    }

    /**
     * 递归查询上传文件
     * @param $key
     * @param $names
     * @param $errors
     */
    protected static function getUploadFilesRecursive($key, $names, $errors)
    {
        if (is_array($names)) {
            foreach ($names as $i => $name) {
                static::getUploadFilesRecursive($key . '[' . $i . ']', $name, $errors);
            }
        } elseif ($errors !== UPLOAD_ERR_NO_FILE) {
            self::$_files[] = $key;
        }
        return self::$_files;
    }

    /**
     * 把数组错误信息转为字符串
     * @param $arr
     * @return string
     */
    public function arrToStr($arr)
    {
        $str='';
        if (is_array($arr)) {
            foreach($arr as $v){
                $str.=$v[0].';';
            }
        }
        return substr($str,0,-1);
    }

    /**
     * 设置当前应用的公众号
     * @param Wechat $wechat
     * @return mixed
     */
    abstract public function setWechat(Wechat $wechat);

    /**
     * 获取当前应用的公众号
     * @return mixed
     */
    abstract public function getWechat();
}
