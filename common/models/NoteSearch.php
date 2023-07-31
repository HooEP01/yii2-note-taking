<?php

namespace common\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Note;

/**
 * NoteSearch represents the model behind the search form of `common\models\Note`.
 */
class NoteSearch extends Note
{
    public $folder;
    
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'due_date'], 'integer'],
            [['folder_id', 'title', 'description', 'tags', 'priority', 'status', 'createdBy', 'createdAt', 'updatedBy', 'updatedAt', 'deletedBy', 'deletedAt'], 'safe'],
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
        $query = Note::find();

        // add conditions that should always apply here

        if (isset($this->folder)) {
            $query->folder($this->folder);
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
            'id' => $this->id,
            'due_date' => $this->due_date,
            'createdAt' => $this->createdAt,
            'updatedAt' => $this->updatedAt,
            'deletedAt' => $this->deletedAt,
            'isActive' => $this->isActive,
        ]);

        $query->andFilterWhere(['ilike', 'folder_id', $this->folder_id])
            ->andFilterWhere(['ilike', 'title', $this->title])
            ->andFilterWhere(['ilike', 'description', $this->description])
            ->andFilterWhere(['ilike', 'tags', $this->tags])
            ->andFilterWhere(['ilike', 'priority', $this->priority])
            ->andFilterWhere(['ilike', 'status', $this->status])
            ->andFilterWhere(['ilike', 'createdBy', $this->createdBy])
            ->andFilterWhere(['ilike', 'updatedBy', $this->updatedBy])
            ->andFilterWhere(['ilike', 'deletedBy', $this->deletedBy]);

        return $dataProvider;
    }
}
