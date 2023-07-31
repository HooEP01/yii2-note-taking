<?php

namespace backend\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\City;

/**
 * CitySearch represents the model behind the search form of `common\models\City`.
 */
class CitySearch extends City
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'shortName', 'stateCode', 'countryCode','createdAt'], 'safe'],
            [['position'], 'integer'],
            [['isActive'], 'boolean'],
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
        $query = City::find()->alias('t')
            ->joinWith(['state s', 'country c']);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'position' => SORT_ASC,
                    'countryCode' => SORT_ASC,
                    'stateCode' => SORT_ASC,
                    'name' => SORT_ASC,
                ]
            ]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            't.position' => $this->position,
            't.isActive' => $this->isActive,
        ]);

        $query->andFilterWhere(['ilike', 't.name', $this->name])
            ->andFilterWhere(['ilike', 't.shortName', $this->shortName])
            ->andFilterWhere([
                'or',
                ['ilike', 's.name', $this->stateCode],
                ['ilike', 's.code', $this->stateCode],
            ])
            ->andFilterWhere([
                'or',
                ['ilike', 'c.name', $this->countryCode],
                ['ilike', 'c.code', $this->countryCode],
            ]);

        if (($condition = $query->getDataRangeCondition('t.createdAt', $this->createdAt)) !== false) {
            $query->andWhere($condition);
        }

        return $dataProvider;
    }
}
