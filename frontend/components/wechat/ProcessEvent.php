<?php
namespace components\wechat;

use yii\base\Event;

/**
 * 微信消息请求处理事件
 * @package components\wechat
 */
class ProcessEvent extends Event
{
    /**
     * 微信请求内容信息
     * @var array
     */
    public $message;
    /**
     * 响应公众号Model
     * @var Object
     */
    public $wechat;
    /**
     * @var bool
     */
    public $isValid = true;
    /**
     * 响应内容
     * @var string
     */
    public $result;

    /**
     * @param array $message
     * @param $wechat
     * @param array $config
     */
    public function __construct($message, $wechat, $config = [])
    {
        $this->message = $message;
        $this->wechat = $wechat;
        parent::__construct($config);
    }
}