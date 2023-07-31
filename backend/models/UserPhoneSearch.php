<?php

namespace backend\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\UserPhone;

/**
 * UserPhoneSearch represents the model behind the search form of `common\models\UserPhone`.
 */
class UserPhoneSearch extends UserPhone
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['userId', 'prefix', 'number', 'complete', 'createdAt'], 'safe'],
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
        $query = UserPhone::find();

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

        $query->andFilterWhere(['ilike', 'prefix', $this->prefix])
            ->andFilterWhere(['ilike', 'number', $this->number])
            ->andFilterWhere(['ilike', 'complete', $this->complete]);

        if (($condition = $query->getDataRangeCondition('createdAt', $this->createdAt)) !== false) {
            $query->andWhere($condition);
        }

        return $dataProvider;
    }
}
