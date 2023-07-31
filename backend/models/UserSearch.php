<?php

namespace backend\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\User;
use yii\db\ArrayExpression;

/**
 * UserSearch represents the model behind the search form of `common\models\User`.
 */
class UserSearch extends User
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'roles', 'type', 'status', 'username', 'name', 'displayName', 'emailId', 'phoneId', 'addressId', 'imageId', 'firstName', 'middleName', 'lastName', 'fullName', 'nameFormat', 'description', 'gender', 'dateOfBirth', 'passwordSalt', 'passwordHash', 'passwordResetToken', 'token', 'authKey', 'authMfaToken', 'referrerCode', 'referrerUserId', 'languageCode', 'currencyCode', 'countryCode', 'stateCode', 'cityId', 'postcode', 'configuration', 'cacheDeviceIdentifier', 'createdBy', 'createdAt', 'updatedBy', 'updatedAt'], 'safe'],
            [['authCookieExpiry'], 'integer'],
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
        $query = User::find()->alias('t')
            ->joinWith(['phone p', 'email e']);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'createdAt' => SORT_DESC,
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
            't.isActive' => $this->isActive,
        ]);

        $query->andFilterWhere(['ilike', 't.type', $this->type])
            ->andFilterWhere(['ilike', 't.status', $this->status])
            ->andFilterWhere([
                'or',
                ['ilike', 't.name', $this->name],
                ['ilike', 't.username', $this->name],
                ['ilike', 't.displayName', $this->name],
            ])
            ->andFilterWhere(['ilike', 'e.address', $this->emailId])
            ->andFilterWhere(['ilike', 'p.complete', $this->phoneId])
            ->andFilterWhere(['ilike', 'gender', $this->gender]);

        if (!empty($this->roles)) {
            $query->andFilterWhere(['@>', 'u.roles', new ArrayExpression([$this->roles], 'text')]);
        }

        if (($condition = $query->getDataRangeCondition('t.createdAt', $this->createdAt)) !== false) {
            $query->andWhere($condition);
        }

        return $dataProvider;
    }
}
