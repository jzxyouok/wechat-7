<?php

namespace modules\wechat\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use modules\wechat\models\ReplyRuleQuery;

class ReplyRule extends \yii\db\ActiveRecord
{
    /**
     * 激活状态
     */
    const STATUS_ACTIVE = 1;
    /**
     * 禁用状态
     */
    const STATUS_DISABLED = 0;
    const PROCESSOR_DEFAULT = 'process';
    public static $statuses = [
        self::STATUS_ACTIVE => '启用',
        self::STATUS_DISABLED => '禁用'
    ];

    /**
     * @inheritdoct
     */
    public static function find()
    {
        return Yii::createObject(ReplyRuleQuery::className(), [get_called_class()]);
    }

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
        return '{{%wechat_reply_rule}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['wid', 'name', 'status'], 'required','message'=>'{attribute}不能为空'],
            [['wid', 'status', 'priority'], 'integer'],
            [['name', 'processor','module'], 'string', 'max' => 40],
            [['mid'], 'string', 'max' => 20],
            [['priority'], 'default', 'value' => 0],
            [['processor'], 'default', 'value' => self::PROCESSOR_DEFAULT]
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
            'module' => '模块类型',
            'name' => '规则名称',
            'processor' => '请求处理类',
            'status' => '状态',
            'priority' => '优先级',
            'created_at' => '创建时间',
            'updated_at' => '修改时间',

            'keywords' => '触发关键字'
        ];
    }

    /**
     * 回复的关键字
     * @return static
     */
    public function getKeywords()
    {
        return $this->hasMany(ReplyRuleKeyword::className(), ['rid' => 'id'])
            ->inverseOf('rule');
    }
}
