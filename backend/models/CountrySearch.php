<?php

namespace backend\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Country;

/**
 * CountrySearch represents the model behind the search form of `common\models\Country`.
 */
class CountrySearch extends Country
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['code', 'name', 'shortName', 'createdAt'], 'safe'],
            [['isStateRequired', 'isPostcodeRequired', 'isActive'], 'boolean'],
            [['position'], 'integer'],
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
        $query = Country::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'position' => SORT_ASC,
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
            'isStateRequired' => $this->isStateRequired,
            'isPostcodeRequired' => $this->isPostcodeRequired,
            'position' => $this->position,
            'isActive' => $this->isActive,
        ]);

        $query->andFilterWhere(['ilike', 'code', $this->code])
            ->andFilterWhere(['ilike', 'name', $this->name])
            ->andFilterWhere(['ilike', 'shortName', $this->shortName])
            ->andFilterWhere(['ilike', 'imageId', $this->imageId])
            ->andFilterWhere(['ilike', 'iso3', $this->iso3])
            ->andFilterWhere(['ilike', 'numCode', $this->numCode])
            ->andFilterWhere(['ilike', 'telCode', $this->telCode])
            ->andFilterWhere(['ilike', 'currencyCode', $this->currencyCode])
            ->andFilterWhere(['ilike', 'defaultStateCode', $this->defaultStateCode]);

        if (($condition = $query->getDataRangeCondition('createdAt', $this->createdAt)) !== false) {
            $query->andWhere($condition);
        }

        return $dataProvider;
    }
}
