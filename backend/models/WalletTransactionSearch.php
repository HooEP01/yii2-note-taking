<?php

namespace backend\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\WalletTransaction;

/**
 * WalletTransactionSearch represents the model behind the search form of `common\models\WalletTransaction`.
 */
class WalletTransactionSearch extends WalletTransaction
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'walletId', 'type', 'description', 'referenceCode', 'referenceType', 'referenceKey', 'translateCategory', 'translateMessage', 'translateData', 'settlementId', 'createdBy', 'createdAt', 'updatedBy', 'updatedAt'], 'safe'],
            [['amount', 'precision'], 'integer'],
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
        $query = WalletTransaction::find();

        // add conditions that should always apply here
        if (isset($this->walletId)) {
            $query->wallet($this->walletId);
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
            'amount' => $this->amount,
            'precision' => $this->precision,
            'createdAt' => $this->createdAt,
            'updatedAt' => $this->updatedAt,
            'isActive' => $this->isActive,
        ]);

        $query->andFilterWhere(['ilike', 'id', $this->id])
            ->andFilterWhere(['ilike', 'type', $this->type])
            ->andFilterWhere(['ilike', 'description', $this->description])
            ->andFilterWhere(['ilike', 'referenceCode', $this->referenceCode])
            ->andFilterWhere(['ilike', 'referenceType', $this->referenceType])
            ->andFilterWhere(['ilike', 'referenceKey', $this->referenceKey])
            ->andFilterWhere(['ilike', 'translateCategory', $this->translateCategory])
            ->andFilterWhere(['ilike', 'translateMessage', $this->translateMessage])
            ->andFilterWhere(['ilike', 'translateData', $this->translateData])
            ->andFilterWhere(['ilike', 'settlementId', $this->settlementId])
            ->andFilterWhere(['ilike', 'createdBy', $this->createdBy])
            ->andFilterWhere(['ilike', 'updatedBy', $this->updatedBy]);

        return $dataProvider;
    }
}
