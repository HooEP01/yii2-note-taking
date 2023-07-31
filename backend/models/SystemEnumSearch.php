<?php
/**
 * @copyright Copyright (c) Hustle Hero
 */

namespace backend\models;

use common\base\enum\SystemEnumType;
use common\models\SystemEnum;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * Class ImageSearch
 * @package backend\models
 */
class SystemEnumSearch extends SystemEnum
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['position'], 'integer'],
            [['isActive'], 'boolean'],
            [['code', 'name', 'description', 'remark'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = SystemEnum::find()->alias('s');
        if (!empty($this->type) && SystemEnumType::isValid($this->type)) {
            $query->type($this->type);
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'position' => SORT_ASC
                ]
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            '[[i]].[[id]]' => $this->id,
            '[[i]].[[isActive]]' => $this->isActive,
        ]);

        $query->andFilterWhere(['ilike', '[[i]].[[code]]', $this->code])
            ->andFilterWhere(['ilike', '[[i]].[[name]]', $this->name])
            ->andFilterWhere(['ilike', '[[i]].[[description]]', $this->description])
            ->andFilterWhere(['ilike', '[[i]].[[remark]]', $this->remark]);

        return $dataProvider;
    }
}