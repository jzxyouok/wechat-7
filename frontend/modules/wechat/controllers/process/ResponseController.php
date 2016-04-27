<?php
namespace modules\wechat\controllers\process;

use yii;
use modules\wechat\models\ReplyRuleKeyword;
use components\wechat\ProcessController;

//file_put_contents('../runtime/logs/aaaa.txt',json_encode($a->content).'||'.' '.date('Y-m-d H:i:s',time()).PHP_EOL,FILE_APPEND | LOCK_EX);

/**
 * 响应微信请求默认处理
 * @package modules\wechat\controllers
 */
class ResponseController extends ProcessController
{
    public $ruleKeyword;

    public function init()
    {
        $this->ruleKeyword = new ReplyRuleKeyword();
    }

    /**
     * 文本回复
     * @return array
     */
    public function actionText(){
        $ruleKeyword=$this->ruleKeyword->findOne(['keyword'=>$this->message['Content']]);
        return $this->responseText($ruleKeyword->content);
    }
    /**
     * 图文回复
     * @return array
     */
    public function actionNews(){
        $articles=$this->ruleKeyword->findAll(['type'=>$this->message['MsgType']]);
        return $this->responseNews($articles);
    }
    /**
     * 音频回复
     * @return array
     */
    public function actionVoice(){
        $articles=$this->ruleKeyword->findOne(['type'=>$this->message['MsgType']]);
        return $this->responseVoice($articles['mediaId']);
    }


}