<?php

namespace backend\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\CurrencyRate;

/**
 * CurrencyRateSearch represents the model behind the search form of `common\models\CurrencyRate`.
 */
class CurrencyRateSearch extends CurrencyRate
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['sourceCurrencyCode', 'targetCurrencyCode', 'createdAt'], 'safe'],
            [['conversionRate'], 'integer'],
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
        $query = CurrencyRate::find()->alias('r')
            ->joinWith(['sourceCurrency s', 'targetCurrency t']);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'createdAt' => SORT_DESC,
                ],
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'r.isActive' => $this->isActive,
        ]);

        $query->andFilterWhere([
            'or',
            ['ilike', 's.name', $this->sourceCurrencyCode],
            ['ilike', 's.code', $this->sourceCurrencyCode],
        ])
            ->andFilterWhere([
                'or',
                ['ilike', 't.name', $this->targetCurrencyCode],
                ['ilike', 't.code', $this->targetCurrencyCode],
            ]);

        if (($condition = $query->getIntegerDecimalCondition('[[r]].[[conversionRate]]', $this->conversionRate)) !== false) {
            $query->andWhere($condition);
        }

        if (($condition = $query->getDataRangeCondition('r.createdAt', $this->createdAt)) !== false) {
            $query->andWhere($condition);
        }

        return $dataProvider;
    }
}
