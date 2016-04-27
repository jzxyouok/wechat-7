<?php

namespace modules\wechat\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * CustomerSearch represents the model behind the search form about `modules\wechat\models\Media`.
 */
class CustomerSearch extends Customer
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id','wid','kf_id', 'created_at', 'updated_at'], 'integer'],
            [['kf_account'], 'string', 'max' => 100],
            [['kf_headimgurl'], 'string', 'max' => 255],
            [['kf_nick'], 'string', 'max' => 20],
            [['invite_wx'], 'string', 'max' => 100],
            [['invite_status'], 'string', 'max' => 10],
        ];
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = Customer::find();
        $pagesize=isset($params['per-page'])?$params['per-page']:20;
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pagesize' => $pagesize,
             ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'kf_account' => $this->kf_account,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'kf_account', $this->kf_account])
            ->andFilterWhere(['like', 'kf_nick', $this->kf_nick])
            ->andFilterWhere(['like', 'kf_id', $this->kf_id]);

        return $dataProvider;
    }
}
