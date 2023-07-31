<?php

namespace backend\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\UserSocial;

/**
 * UserSocialSearch represents the model behind the search form of `common\models\UserSocial`.
 */
class UserSocialSearch extends UserSocial
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['userId', 'channel', 'channelId', 'channelName', 'createdAt'], 'safe'],
            [['isVerified', 'isActive'], 'boolean'],
        ];
    }

    /**
     * {@inheritdoc}
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
        $query = UserSocial::find();

        // add conditions that should always apply here
        if (isset($this->userId)) {
            $query->user($this->userId);
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'isVerified' => $this->isVerified,
            'isActive' => $this->isActive,
        ]);

        $query->andFilterWhere(['ilike', 'channel', $this->channel])
            ->andFilterWhere(['ilike', 'channelId', $this->channelId])
            ->andFilterWhere(['ilike', 'channelName', $this->channelName]);

        if (($condition = $query->getDataRangeCondition('createdAt', $this->createdAt)) !== false) {
            $query->andWhere($condition);
        }

        return $dataProvider;
    }
}
