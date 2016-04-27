<?php
namespace modules\wechat\models;

use Yii;
use yii\helpers\Html;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;
use modules\wechat\behaviors\ArrayBehavior;

/**
 * 素材存储表
 * @package callmez\wechat\models
 */
class Media extends ActiveRecord
{
    /**
     * 媒体素材(图片, 音频, 视频, 缩略图)
     */
    const TYPE_MEDIA = 'media';
    /**
     * 图文素材(永久)
     */
    const TYPE_NEWS = 'news';
    /**
     * 图片素材
     */
    const TYPE_IMAGE = 'image';
    /**
     * 音频素材
     */
    const TYPE_VOICE = 'voice';
    /**
     * 视频素材
     */
    const TYPE_VIDEO = 'video';
    /**
     * 音乐素材
     */
    const TYPE_MUSIC = 'music';
    /**
     * 缩略图素材
     */
    const TYPE_THUMB = 'thumb';
    /**
     * 临时素材
     */
    const MATERIAL_TEMPORARY = 'tomporary';
    /**
     * 永久素材
     */
    const MATERIAL_PERMANENT = 'permanent';
    /**
     * 素材类型
     * @var array
     */
    public static $types = [
        self::TYPE_IMAGE => '图片',
        self::TYPE_THUMB => '缩略图',
        self::TYPE_VOICE => '语音',
        self::TYPE_VIDEO => '视频',
        self::TYPE_MUSIC => '音乐',
    ];
    /**
     * 素材统称
     * @var array
     */
    public static $mediaTypes = [
        self::TYPE_MEDIA => '媒体素材',
        self::TYPE_NEWS => '图文素材'
    ];
    /**
     * 素材类别
     * @var array
     */
    public static $materialTypes = [
        self::MATERIAL_TEMPORARY => '临时素材',
        self::MATERIAL_PERMANENT => '永久素材'
    ];

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'timestamp' => TimestampBehavior::className(),
            'array' => [
                'class' => ArrayBehavior::className(),
                'attributes' => [
                    ArrayBehavior::TYPE_SERIALIZE => ['result']
                ]
            ]
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%wechat_media}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['file', 'result', 'wid'], 'required'],
            [['mediaId', 'file'], 'string', 'max' => 100],
            [['url'], 'string', 'max' => 255],
            [['type'], 'string', 'max' => 10],
//            [['title'], 'string', 'max' => 50],
//            [['introduction'], 'string', 'max' => 255],
            [['material'], 'string', 'max' => 20],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'wid' => '所属微信公众号ID',
            'mediaId' => '媒体ID',
            'file' => '文件名',
//            'title' => '标题',
//            'introduction' => '描述',
            'url' => '永久素材图片URL',
            'result' => '响应内容',
            'type' => '媒体类型',
            'material' => '素材类别',
            'created_at' => '创建时间',
            'updated_at' => '修改时间',
        ];
    }

    /**
     * 获取媒体类型
     */
    public function getByType($type)
    {
        $material = [
            'image' => '图片',
            'voice' => '语音',
            'video' => '视频',
            'thumb' => '缩略图',
            'news' => '图文',
        ];
        return $material[$type];
    }

    /**
     * 获取素材类别
     */
    public function getByMaterial($type)
    {
        $material = [
            'tomporary' => '临时素材',
            'permanent' => '永久素材',
        ];
        return $material[$type];
    }

    /**
     * 获取素材
     */
    public function getByFile($type,$file)
    {
        switch ($type) {
            case self::TYPE_IMAGE:
                $filename='<img height="50px" src="/'.$file.'">';
                break;
            case self::TYPE_THUMB:
                $filename='<img height="50px" src="/'.$file.'">';
                break;
            case self::TYPE_VOICE:
                $filename='<audio src="/'.$file.'" preload="auto" /></audio>';
                break;
            case self::TYPE_VIDEO:
                $filename='<video width="150" height="150" controls="controls"><source src="/'.$file.'" type="video/mp4">Your browser does not support the video tag.</video>';
                break;
            case self::TYPE_MUSIC:
                if(empty($file['hqMusicUrl'])){
                    $musicPath=$file['hqMusicUrl'];
                }else{
                    $musicPath=$file['musicUrl'];
                }
                $filename='缩略图：<img height="50px" src="/'.$file['file'].'"></br>';
                $filename.='<audio src="'.$musicPath.'" preload="auto" /></audio>';
                break;
            default:
                $filename='<img height="50px" src="/'.$file.'">';
        }
        return $filename;
    }



}