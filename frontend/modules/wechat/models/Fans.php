<?php

namespace modules\wechat\models;

use yii;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;
use components\wechat\MpWechat;

/**
 * This is the model class for table "{{%wechat_fans}}".
 *
 * @property integer $id
 * @property integer $wid
 * @property string $open_id
 * @property integer $status
 * @property integer $created_at
 * @property integer $updated_at
 */
class Fans extends ActiveRecord
{
    /**
     * 取消关注
     */
    const STATUS_UNSUBSCRIBED = 0;
    /**
     * 关注状态
     */
    const STATUS_SUBSCRIBED = 1;
    public static $subscribes = [
        self::STATUS_SUBSCRIBED => '关注',
        self::STATUS_UNSUBSCRIBED => '未关注'
    ];

    public function behaviors()
    {
        return [
            'timestamp' => TimestampBehavior::className()
        ];
    }

    /**
     * @inheritdoc
     */
    public static function find()
    {
        return Yii::createObject(FansQuery::className(), [get_called_class()]);
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%wechat_fans}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['wid', 'open_id'], 'required'],
            [['wid', 'subscribe', 'created_at', 'updated_at'], 'integer'],
            [['open_id'], 'string', 'max' => 50]
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
            'open_id' => '微信ID',
            'subscribe' => '关注状态',
            'groupid' => '用户分组',
            'created_at' => '添加时间',
            'updated_at' => '修改时间',
        ];
    }

    /**
     * 关联公众号
     * @return \yii\db\ActiveQuery
     */
    public function getWechat()
    {
        return $this->hasOne(Wechat::className(), ['id' => 'wid']);
    }

    /**
     * 关联的用户信息
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(MpUser::className(), ['id' => 'id']);
    }

    /**
     * 通过唯一的openid查询粉丝
     * @param $open_id
     * @return mixed
     */
    public static function findByOpenId($open_id)
    {
        return self::findOne(['open_id' => $open_id]);
    }

    /**
     * 关注
     * @return bool
     */
    public function subscribe()
    {
        return $this->updateAttributes(['subscribe' => self::STATUS_SUBSCRIBED]) > 0;
    }

    /**
     * 取消关注
     * @return bool
     */
    public function unsubscribe()
    {
        return $this->updateAttributes(['subscribe' => self::STATUS_UNSUBSCRIBED]) > 0;
    }

    /**
     * 更新用户微信数据
     * @return bool
     */
    public function updateUser($wechat,$data)
    {
        Yii::$app->db->createCommand()->batchInsert('wechat_fans', ['wid','subscribe', 'open_id', 'created_at', 'updated_at'], [
            [$wechat->id, $data['subscribe'],$data['openid'],time(),time()]
        ])->execute();

        Yii::$app->db->createCommand()->batchInsert('wechat_mp_user', ['id','open_id', 'nickname', 'sex', 'city', 'country', 'province', 'language', 'avatar', 'subscribe_time', 'remark', 'union_id', 'group_id', 'updated_at'], [
            [Yii::$app->db->getLastInsertID(),$data['openid'], $data['nickname'], $data['sex'], $data['city'], $data['country'], $data['province'], $data['language'], $data['headimgurl'], $data['subscribe_time'], $data['remark'], isset($data['unionid']) ? $data['unionid'] : '', $data['groupid'],time()]
        ])->execute();
    }

    /**
     * 删除用户微信数据
     * @return bool
     */
    public function deleteUser($wechat,$data)
    {
        Yii::$app->db->createCommand()->delete('wechat_fans', ['open_id'=>$data['openid'],'wid'=>$wechat->id])->execute();

        Yii::$app->db->createCommand()->delete('wechat_mp_user', ['open_id'=>$data['openid']])->execute();


    }


}
