<?php

namespace modules\wechat\models;

use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;
use components\wechat\ExtsValidator;

/**
 * This is the model class for table "{{%wechat_reply_rule_keyword}}".
 *
 * @property integer $id
 * @property integer $rid
 * @property string $keyword
 * @property string $type
 * @property integer $priority
 * @property integer $created_at
 * @property integer $updated_at
 */
class ReplyRuleKeyword extends ActiveRecord
{
    /**
     * text类型请求 直接匹配关键字
     */
    const TYPE_MATCH = 'match';
    /**
     * text类型请求 包含关键字
     */
    const TYPE_REGULAR = 'include';
    /**
     * text类型请求 正则表达式
     */
    const TYPE_INCLUDE = 'regular';
    /**
     * image类型请求
     */
    const TYPE_IMAGE = 'image';
    /**
     * 图文类型请求
     */
    const TYPE_NEWS = 'news';
    /**
     * 音乐类型请求
     */
    const TYPE_MUSIC = 'music';
    /**
     * 语音类型请求
     */
    const TYPE_VOICE = 'voice';
    /**
     * 视频类型请求
     */
    const TYPE_VIDEO = 'video';
    /**
     * 短视频类型请求
     */
    const TYPE_SHORT_VIDEO = 'short_video';
    /**
     * 位置类型请求
     */
    const TYPE_LOCATION = 'location';
    /**
     * 链接类型请求
     */
    const TYPE_LINK = 'link';
    /**
     * 关注请求
     */
    const TYPE_SUBSCRIBE = 'subscribe';
    /**
     * 取消关注请求
     */
    const TYPE_UNSUBSCRIBE = 'unsubscribe';
    /**
     * 触发类型
     * @var array
     */
    public static $types = [
        self::TYPE_MATCH => '直接匹配关键字',
        self::TYPE_REGULAR => '正则匹配关键字',
        self::TYPE_INCLUDE => '包含关键字',

        self::TYPE_IMAGE => '图片请求',
        self::TYPE_NEWS => '图文请求',
        self::TYPE_VOICE => '语音请求',
        self::TYPE_MUSIC => '音乐请求',
        self::TYPE_VIDEO => '视频请求',
        self::TYPE_SHORT_VIDEO => '段视频请求',
        self::TYPE_LOCATION => '位置请求',
        self::TYPE_LINK => '链接请求',

        self::TYPE_SUBSCRIBE => '关注请求',
        self::TYPE_UNSUBSCRIBE => '取消关注请求'
    ];

    /**
     * @inheritdoct
     */
    public static function find()
    {
        return Yii::createObject(ReplyRuleKeywordQuery::className(), [get_called_class()]);
    }

//    public function behaviors()
//    {
//        return [
//            'class' => TimestampBehavior::className(),
//            'attributes' => [
//                ActiveRecord::EVENT_BEFORE_INSERT => ['start_at', 'end_at'],
//                ActiveRecord::EVENT_BEFORE_UPDATE => ['start_at', 'end_at'],
//            ],
//        ];
//    }
    public function behaviors()
    {
        return [
            'timestamp' => TimestampBehavior::className()
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%wechat_reply_rule_keyword}}';
    }

    public function scenarios()
    {
        $parent_scenarios = parent::scenarios();//继承父类的场景
        //定义自己的场景
        $self_scenarios =  [
            'text_instert' => ['rid','priority','keyword', 'type','content','start_at','end_at'],
            'news_instert' => ['rid','priority','keyword','title','descriptions', 'type','url','content','thumbs','start_at','end_at'],
            'music_instert' => ['rid','priority','keyword','title','descriptions', 'type','HQMusic','music','start_at','end_at'],
            'music_delete' => ['HQMusic','music'],
            'images_instert' => ['rid','priority','keyword','title','descriptions', 'type','images','start_at','end_at'],
        ];
        //合并场景
        return array_merge($parent_scenarios,$self_scenarios);
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['rid', 'keyword', 'type'], 'required','message'=>'{attribute} 不能为空'],
            ['content', 'required','message'=>'{attribute} 不能为空'],
            ['content', 'required','message'=>'{attribute} 不能为空','on'=>['text_instert','news_instert']],
            [['rid', 'priority'], 'integer'],
            [['url','title','keyword','descriptions','thumbs','images','HQMusic'], 'string', 'max' => 255],
            [['priority'], 'default', 'value' => 0],
            //[['thumbs','thumbs'], 'file', 'extensions' => 'jpg'],
            [['thumbs','thumbs'], 'checkType', 'params' => ['png,png']],
            //[['thumbs'], ExtsValidator::className(), 'extensions'=>'png'],
            [['HQMusic','music'], 'file', 'extensions' => 'mp3, acc'],

            [['type'], 'in', 'range' => array_keys(static::$types)],
            [['start_at', 'end_at'], 'default', 'value' => 0],
        ];
    }

    /**
     * 类型验证
     * @param $attribute
     * @param $params
     */
    public function checkType($attribute, $params)
    {
        if($this->thumbs){
            $extension=substr(strrchr($this->thumbs, '.'), 1);
        }else{
            $extension=substr(strrchr($this->images, '.'), 1);
        }

        if (!in_array($extension,$params)) {
            $this->addError($attribute, '图片格式错误,请用.jpg,png格式的图片');
        }
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'rid' => '所属规则ID',
            'keyword' => '规则关键字',
            'title' => '标题',
            'thumbs' => '封面图',
            'url' => '图文链接',
            'images' => '回复图片',
            'music' => '音乐链接',
            'HQMusic' => '高质量音乐链接',
            'descriptions' => '描述',
            'content' => '回复内容',
            'type' => '关键字类型',
            'priority' => '优先级',
            'start_at' => '开始时间',
            'end_at' => '结束时间',
            'created_at' => '创建时间',
            'updated_at' => '修改时间'
        ];
    }

    /**
     * 关联的回复规则
     * @return \yii\db\ActiveQuery
     */
    public function getRule()
    {
        return $this->hasOne(ReplyRule::className(), ['id' => 'rid']);
    }
}

