<?php
namespace modules\wechat\controllers\process;

use modules\wechat\models\MpUser;
use modules\wechat\models\Wechat;
use yii;
use modules\wechat\models\Fans;
use components\wechat\ProcessController;

/**
 * 微信粉丝请求默认处理
 * @package modules\wechat\controllers
 */
class FansController extends ProcessController
{
    /**
     * 数据记录
     * @return array
     * @throws \yii\base\InvalidConfigException
     */
    public function actionRecord()
    {
        $wechat = $this->getWechat();
        $fans = $this->getFans();
        if (!$fans) { // 存储粉丝信息
            $fans = Yii::createObject(Fans::className());
            $fans->setAttributes([
                'wid' => $wechat->id,
                'open_id' => $this->message['FromUserName'],
                'subscribe' => Fans::STATUS_SUBSCRIBED
            ]);
            if ($fans->save() && $wechat->status > Wechat::TYPE_SUBSCRIBE) { // 更新用户详细数据, 普通订阅号无权限获取
                $fans->updateUser();
            }
        } elseif ($fans->subscribe != Fans::STATUS_SUBSCRIBED) { // 更新关注状态
            $fans->subscribe();
        }

//        $history = new MessageHistory();
//        $attributes = [
//            'wid' => $wechat->id,
//            'module' => $this->getModuleName($this->api->lastProcessController),
//        ];
    }

    public function actionText(){
        return $this->responseText('暂无回复信息');
    }

    /**
     * 关注处理
     * @return array
     * @throws \yii\base\InvalidConfigException
     */
    public function actionSubscribe()
    {
        $fans = $this->getFans();
        if (!$fans) { // 存储粉丝信息
            $fans = Yii::createObject(Fans::className());
            $fans->setAttributes([
                'wid' => $this->getWechat()->id,
                'open_id' => $this->message['FromUserName'],
                'subscribe' => Fans::STATUS_SUBSCRIBED
            ]);
            if ($fans->save() && $this->getWechat()->status > Wechat::TYPE_SUBSCRIBE) { // 更新用户详细数据, 普通订阅号无权限获取
                $fans->updateUser();
            }
        } elseif ($fans->subscribe != Fans::STATUS_SUBSCRIBED) { // 更新关注状态
            $fans->updateAttributes(['subscribe' => Fans::STATUS_SUBSCRIBED]);
        }
    }

    /**
     * 取消关注处理
     */
    public function actionUnsubscribe()
    {
        if ($fans = $this->getFans()) {
            $fans->unsubscribe();
        }
    }
}