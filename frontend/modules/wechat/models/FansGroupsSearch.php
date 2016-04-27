<?php

namespace modules\wechat\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * FansGroupsSearch represents the model behind the search form about `modules\wechat\models\Fans`.
 */
class FansGroupsSearch extends FansGroups
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id','groupid', 'wid','isdefault','count'], 'integer'],
            [['name'], 'safe'],
        ];
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params, $user = false)
    {
        $query = FansGroups::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        if ($user) {
            $query->with('user');
        }

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'groupid' => $this->groupid,
            'wid' => $this->wid,
            'name' => $this->name,
            'isdefault' => $this->isdefault,
        ]);

        $query->andFilterWhere(['like', 'wid', $this->wid]);

        return $dataProvider;
    }
}
