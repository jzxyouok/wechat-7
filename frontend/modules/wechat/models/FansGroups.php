<?php

namespace modules\wechat\models;

use yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "{{%wechat_fans_groups}}".
 *
 * @property integer $id
 * @property integer $wid
 * @property string $open_id
 * @property integer $status
 * @property integer $created_at
 * @property integer $updated_at
 */
class FansGroups extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%wechat_fans_groups}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['wid','groupid', 'name'], 'required'],
            [['groupid', 'wid','isdefault'], 'integer'],
            [['name'], 'string', 'max' => 30]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'groupid' => '分组id',
            'wid' => '所属微信公众号ID',
            'name' => '分组名称',
            'isdefault' => '是否系统分组',
            'count' => '分组用户数',
        ];
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
     * 更新分组数据
     * @return bool
     */
    public function updateGroup($wechat,$data)
    {
        Yii::$app->db->createCommand()->batchInsert('wechat_fans_groups', ['wid','groupid', 'name', 'isdefault', 'count'], [
            [$wechat->id, $data['id'],$data['name'],in_array($data['id'],[0,1,2])?1:0,$data['count']]
        ])->execute();
    }

    /**
     * 删除分组数据
     * @return bool
     */
    public function deleteGroup($wechat)
    {
        Yii::$app->db->createCommand()->delete('wechat_fans_groups', ['wid'=>$wechat->id])->execute();
    }


}
