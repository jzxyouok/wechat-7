<?php
namespace modules\wechat\models;

use yii\db\ActiveQuery;

class CustomerQuery extends ActiveQuery
{

    /**
     * 查询状态
     * @param int $status 启用
     * @return $this
     */
    public function active($status)
    {
        return $this->andWhere(['status' => $status]);
    }
}