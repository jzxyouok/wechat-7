<?php

namespace modules\wechat\controllers;

use yii;
use yii\helpers\Url;
use yii\web\Response;
use yii\web\Controller;
use modules\wechat\models\Wechat;

/**
 * 微信控制器基类
 * 所有微信模块的控制器必须继承此类
 *
 * @package components
 */
abstract class BaseController extends Controller
{
    public $request;

    public function init()
    {
        $this->request = Yii::$app->request;
    }

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
