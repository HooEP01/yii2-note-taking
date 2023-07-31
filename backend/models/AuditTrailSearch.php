<?php
/**
 * @author RYU Chua <me@ryu.my>
 */

namespace backend\models;

use common\base\audit\models\AuditTrail;
use common\base\audit\models\AuditTrailQuery;
use common\base\helpers\UuidHelper;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;

/**
 * Class AuditTrailSearch
 * @package backend\models
 */
class AuditTrailSearch extends AuditTrail
{
    public $user;
    /**
     * @return array
     */
    public function rules()
    {
        return [
            [['id', 'userId', 'entryId', 'action', 'modelClass', 'modelKey', 'field', 'createdAt'], 'trim'],
            [['id', 'userId', 'entryId', 'action', 'modelClass', 'modelKey', 'field', 'createdAt'], 'safe'],
        ];
    }
    /**
     * @return array
     */
    public function scenarios()
    {
        return Model::scenarios();
    }

    /**
     * @param $params
     * @param null $query
     * @return ActiveDataProvider
     */
    public function search($params, $query = null)
    {
        /** @var AuditTrailQuery $query */
        $query = $query ? $query : $this->find()->alias('t')->joinWith(['user u', 'user.phone up']);
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'id' => SORT_DESC
                ]
            ]
        ]);

        // load the search form data and validate
        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        // adjust the query by adding the filters
        $userId = null;
        $user = "";
        if (UuidHelper::isValid($this->userId)) {
            $userId = $this->userId;
        } else {
            $user = $this->userId;
        }

        $query->andFilterWhere(['t.id' => $this->id]);
        $query->andFilterWhere(['t.entryId' => $this->entryId]);
        $query->andFilterWhere(['t.userId' => $userId]);
        $query->andFilterWhere(['t.action' => $this->action]);
        $query->andFilterWhere(['ilike', 't.modelClass', $this->modelClass]);
        $query->andFilterWhere(['t.modelKey' => $this->modelKey]);
        if (is_array($this->field)) {
            $query->andFilterWhere(['in', 't.field', $this->field]);
        } else {
            $query->andFilterWhere(['t.field' => $this->field]);
        }
        $query->andFilterWhere([
            'or',
            ['ilike', 'u.name', $user],
            ['ilike', 'u.displayName', $user],
            ['ilike', 'up.complete', $user],
        ]);

        if (($condition = $query->getDataRangeCondition('[[t]].[[createdAt]]', $this->createdAt)) !== false) {
            $query->andWhere($condition);
        }
        return $dataProvider;
    }

    /**
     * @return array
     */
    public static function actionFilter()
    {
        return ArrayHelper::map(
            self::find()->select('action')->groupBy('action')->orderBy('action ASC')->all(),
            'action',
            'action'
        );
    }
}
