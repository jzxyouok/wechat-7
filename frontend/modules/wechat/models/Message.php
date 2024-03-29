<?php
namespace modules\wechat\models;

use yii\base\Model;

class Message extends Model
{
    /**
     * 文本
     */
    const TYPE_TEXT = 'text';
    /**
     * 图片
     */
    const TYPE_IMAGE = 'image';
    /**
     * 语音
     */
    const TYPE_VOCIE = 'voice';
    /**
     * 视频
     */
    const TYPE_VIDEO = 'video';
    /**
     * 音乐
     */
    const TYPE_MUSIC = 'music';
    /**
     * 图文
     */
    const TYPE_NEWS = 'news'; // TODO 图文信息发送完善
        /**
         * 信息可用发送类型
         * @var array
         */
    public static $types = [
        'text' => '文本',
        'image' => '图片',
        'voice' => '语音',
        'video' => '视频',
        'music' => '音乐',
        'news' => '图文'
    ];
    /**
     * 发送用户微信ID
     * @var sting|array
     */
    public $toUser;
    /**
     * 发送信息类型
     * @var 信息类型
     */
    public $msgType;
    /**
     * 文本信息内容
     * @var string
     */
    public $content;
    /**
     * 图片, 声音, 视频信息媒体ID
     * @var string
     */
    public $mediaId;
    /**
     * 视频信息缩略图媒体ID
     * @var string
     */
    public $thumbMediaId;
    /**
     * 视频,音频信息标题
     * @var string
     */
    public $title;
    /**
     * 视频,音频信息描述
     * @var string
     */
    public $description;
    /**
     * 音频信息链接
     * @var string
     */
    public $musicUrl;
    /**
     * 音频信息高品质链接
     * @var string
     */
    public $hqMusicUrl;
    /**
     * 微信公众号
     * @var Wechat
     */
    protected $wechat;

    public function __construct(Wechat $wechat, $config = [])
    {
        $this->wechat = $wechat; // 需设置微信公众号交互主体
        parent::__construct($config);
    }
    /**
     * @inhertdoc
     */
    public function rules()
    {
        return [
            [['toUser'], 'required'],
            [['content'], 'checkType', 'skipOnEmpty' => false, 'skipOnError' => false],
            [['msgType','title','description','musicUrl','hqMusicUrl','thumbMediaId'], 'safe'],
            [['mediaId'], 'required', 'when' => function ($model, $attribute) {
                return in_array($model->msgType, [Message::TYPE_IMAGE, Message::TYPE_VOCIE, Message::TYPE_VIDEO]);
            }, 'whenClient' => "function (attribute, value) {
                var value = $('#message-msgtype input[type=radio]:checked').val()
                return $.inArray(value, " . json_encode([Message::TYPE_IMAGE, Message::TYPE_VOCIE, Message::TYPE_VIDEO]) . ") >= 0;
            }"]
        ];
    }

    /**
     * 类型验证
     * @param $attribute
     * @param $params
     */
    public function checkType($attribute, $params)
    {
        if ($this->msgType == 'text' && $this->content=='') {
            $this->addError($attribute, '内容 不能为空');
        }
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'msgType' => '消息类型',
            'title' => '标题',
            'thumbMediaId' => '媒体缩略图',
            'musicUrl' => '音乐链接',
            'hqMusicUrl' => '高品质链接',
            'description' => '描述',
            'mediaId' => '媒体ID',
            'content' => '消息内容',
        ];
    }

    /**
     * 发送消息
     * @param bool $runValidation
     * @return bool
     */
    public function send($runValidation = true)
    {
        if ($runValidation && !$this->validate()) {
            return false;
        }
        $method = 'send' . $this->msgType;
        if (!method_exists($this, $method)) {
            $this->addError('msgType', '未找到指定发送方法');
            return false;
        }
        $result = call_user_func([$this, $method]);
        if (!$result) {
            $this->addError('msgType', json_encode($this->wechat->getSdk()->lastError));
        }
        return $result;
    }

    /**
     * 发送文本消息
     */
    protected function sendText()
    {
        return $this->wechat->getSdk()->sendMessage([
            'touser' => $this->toUser,
            'msgtype' => $this->msgType,
            $this->msgType => [
                'content' => $this->content
            ]
        ]);
    }

    /**
     * 发送图片消息
     */
    protected function sendImage()
    {
        return $this->wechat->getSdk()->sendMessage([
            'touser' => $this->toUser,
            'msgtype' => $this->msgType,
            $this->msgType => [
                'media_id' => $this->mediaId
            ]
        ]);
    }

    /**
     * 发送语音消息
     */
    protected function sendVoice()
    {
        return $this->wechat->getSdk()->sendMessage([
            'touser' => $this->toUser,
            'msgtype' => $this->msgType,
            $this->msgType => [
                'media_id' => $this->mediaId
            ]
        ]);
    }

    /**
     * 发送视频消息
     */
    protected function sendVideo()
    {
        return $this->wechat->getSdk()->sendMessage([
            'touser' => $this->toUser,
            'msgtype' => $this->msgType,
            $this->msgType => [
                'media_id' => $this->mediaId,
                'thumb_media_id' => $this->thumbMediaId,
                'title' => $this->title,
                'description' => $this->description
            ]
        ]);
    }

    /**
     * 发送音乐消息
     */
    protected function sendMusic()
    {
        return $this->wechat->getSdk()->sendMessage([
            'touser' => $this->toUser,
            'msgtype' => $this->msgType,
            $this->msgType => [
                'title' => $this->title,
                'description' => $this->description,
                'musicurl' => $this->musicUrl,
                'hqmusicurl' => $this->hqMusicUrl,
                'thumb_media_id' => $this->thumbMediaId
            ]
        ]);
    }
}