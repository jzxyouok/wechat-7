<?php

namespace modules\wechat\models;

use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;

class Customer extends ActiveRecord
{
    /**
     * 等待确认
     */
    const STATUS_WAITING = 'waiting';
    /**
     * 被拒绝
     */
    const STATUS_REJECTED = 'rejected';
    /**
     * 过期
     */
    const STATUS_EXPRIED = 'expired';

    public static $invite_status = [
        self::STATUS_WAITING => '等待确认',
        self::STATUS_REJECTED => '被拒绝',
        self::STATUS_EXPRIED => '过期'
    ];

    /**
     * @inheritdoct
     */
    public static function find()
    {
        return Yii::createObject(CustomerQuery::className(), [get_called_class()]);
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
        return '{{%wechat_customer}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['kf_account','kf_nick'], 'required'],
            [['id','wid','kf_id', 'created_at', 'updated_at','invite_expire_time'], 'integer'],
            [['kf_account'], 'string', 'max' => 100],
            [['kf_headimgurl'], 'string', 'max' => 255],
            [['kf_nick'], 'string', 'max' => 20],
            [['invite_wx','kf_wx'], 'string', 'max' => 100],
            [['invite_status'], 'string', 'max' => 10],
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
            'kf_account' => '客服账号',
            'kf_nick' => '客服昵称',
            'kf_headimgurl' => '客服头像',
            'invite_wx' => '邀请微信号',
            'kf_wx' => '绑定微信号',
            'invite_expire_time' => '邀请过期时间',
            'kf_headiminvite_statusgurl' => '邀请状态',
            'created_at' => '创建时间',
            'updated_at' => '修改时间'
        ];
    }

    /**
     * 更新客服数据
     * @return bool
     */
    public function updateCustomer($wechat,$data)
    {
        Yii::$app->db->createCommand()->batchInsert('wechat_customer', ['wid','kf_account', 'kf_headimgurl' ,'kf_wx', 'kf_nick', 'kf_id', 'invite_wx', 'invite_expire_time', 'invite_status', 'updated_at'], [
            [$wechat->id, $data['kf_account'],$data['kf_headimgurl'],isset($data['kf_wx']) ? $data['kf_wx'] : '',$data['kf_nick'],$data['kf_id'],isset($data['invite_wx']) ? $data['invite_wx'] : '',isset($data['invite_expire_time']) ? $data['invite_expire_time'] : '',isset($data['invite_status']) ? $data['invite_status'] : '',time()]
        ])->execute();
    }

    /**
     * 删除客服数据
     * @return bool
     */
    public function deleteCustomer($wechat)
    {
        Yii::$app->db->createCommand()->delete('wechat_customer', ['wid'=>$wechat->id])->execute();
    }
}
