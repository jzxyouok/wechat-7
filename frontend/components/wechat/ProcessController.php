<?php
namespace components\wechat;

use Yii;
use yii\base\InvalidConfigException;
use modules\wechat\models\Fans;
use modules\wechat\models\Wechat;
use modules\wechat\controllers\BaseController;
use components\wechat\WechatXmlResponseFormatter as WXRF;

/**
 * 微信消息处理控制器基类
 * 微信消息出来控制器必须继承此类
 *
 * @package components\wechat
 */
class ProcessController extends BaseController
{
    /**
     * 微信请求的消息内容
     * @var array
     */
    public $message;
    /**
     * 微信请求关闭CSRF验证
     * @var bool
     */
    public $enableCsrfValidation = false;
    /**
     * @var Wechat;
     */
    private $_wechat;

    /**
     * 设置公众号
     * @param Wechat $wechat
     */
    public function setWechat(Wechat $wechat)
    {
        $this->_wechat = $wechat;
    }

    /**
     * @return mixed
     * @throws InvalidConfigException
     */
    public function getWechat()
    {
        if ($this->_wechat === null) {
            throw new InvalidConfigException('The "wechat" property must be set.');
        }
        return $this->_wechat;
    }

    /**
     * @var \modules\wechat\models\Fans
     */
    private $_fans = false;
    /**
     * 获取触发微信请求的微信用户信息
     * @return Fans
     */
    public function getFans()
    {
        if ($this->_fans === false) {
            $this->_fans = Fans::findByOpenId($this->message['FromUserName']);
        }
        return $this->_fans;
    }

    /**
     * 响应文本消息
     * 例: $this->responseText('hello world');
     * @param $content
     * @return array
     */
    public function responseText($content)
    {
        return [
            'MsgType' => ['text',WXRF::CDATA=>true],
            'Content' => [$content,WXRF::CDATA=>true]
        ];
    }

    /**
     * 响应图文消息
     * 例: $this->responseNews([
     *     [
     *         'title' => 'test title',
     *         'description' => 'test description',
     *         'picUrl' => 'pic url',
     *         'url' => 'link'
     *     ],
     *      ...
     * ]);
     * @param array $articles
     * @return array
     */
    public function responseNews(array $articles)
    {
        if (isset($articles['title'])) {
            $articles = [$articles];
        }
        $response = [
            'MsgType' => ['news',WXRF::CDATA=>true],
            'ArticleCount' => count($articles),
        ];
        foreach ($articles as $article) {
            $response['Articles'][] = [
                'Title' => [$article['title'],WXRF::CDATA=>true],
                'Description' => [$article['descriptions'],WXRF::CDATA=>true],
                'PicUrl' => [Yii::$app->request->hostInfo.'/'.$article['thumbs'],WXRF::CDATA=>true],
                'Url' => [$article['url'],WXRF::CDATA=>true]
            ];
        }
        return $response;
    }

    /**
     * 响应图片消息
     * @param $mid 图片mid(需先上传图片给wechat服务器获得mid)
     * 例: $this->responseImage([
     *     'mid' => '123456'
     * ])
     * @return array
     */
    public function responseImage($mid)
    {
        return [
            'MsgType' => 'image',
            'Image' => [
                'MediaId' => $mid
            ]
        ];
    }

    /**
     * 响应语音消息
     * @param $mid 语音mid(需先上传语音给wechat服务器获得mid)
     * 例: $this->responseVoice([
     *     'mid' => '123456'
     * ])
     * @return array
     */
    public function responseVoice($mid)
    {
        return [
            'MsgType' => 'voice',
            'Image' => [
                'MediaId' => $mid
            ]
        ];
    }

    /**
     * 响应视频消息
     * 例: $this->responseVideo([
     *     'mid' => '123456',
     *     'thumbMid' => '1234567'
     * ])
     * @param array $video mid(需先上传视频给wechat服务器获得mid和thumbMid)
     * @return array
     */
    public function responseVideo(array $video)
    {
        return [
            'MsgType' => 'video',
            'Video' => [
                'MediaId' => $video['mid'],
                'ThumbMediaId' => $video['thumbMid']
            ]
        ];
    }

    /**
     * 响应音乐消息
     * 例: $this->responseMusic([
     *     'title' => 'music title',
     *     'description' => 'music description',
     *     'musicUrl' => 'music link',
     *     'hgMusicUrl' => 'HQ music link', // 选填,
     *     'thumbMid' = '123456'
     * ])
     * @param array $music
     * @return array
     */
    public function responseMusic(array $music)
    {
        return [
            'MsgType' => 'music',
            'Image' => [
                'Title' => $music['title'],
                'Description' => $music['description'],
                'MusicUrl' => $music['musicUrl'],
                'HQMusicUrl' => isset($music['hqMusicUrl']) ? $music['hqMusicUrl'] : $music['musicUrl'],
                'ThumbMediaId' => $music['thumbMid']
            ]
        ];
    }
}