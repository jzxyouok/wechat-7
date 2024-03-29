<?php

namespace modules\wechat\models;

use Yii;
use yii\db\ActiveQuery;

class FansQuery extends ActiveQuery
{
    /**
     * 关注状态
     * @return static
     */
    public function subscribed()
    {
        return $this->andWhere(['subscribe' => Fans::STATUS_SUBSCRIBED]);
    }
}
