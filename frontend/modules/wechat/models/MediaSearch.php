<?php

namespace modules\wechat\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use modules\wechat\models\Media;

/**
 * MediaSearch represents the model behind the search form about `modules\wechat\models\Media`.
 */
class MediaSearch extends Media
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'created_at', 'updated_at'], 'integer'],
            [['material'], 'string', 'max' => 20],
            [['mediaId', 'result', 'type'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
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
        $query = Media::find();
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
            'material' => $this->material,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'type' => $this->type,
        ]);

        $query->andFilterWhere(['like', 'mediaId', $this->mediaId])
            ->andFilterWhere(['like', 'result', $this->result])
            ->andFilterWhere(['like', 'type', $this->type]);

        return $dataProvider;
    }
}
