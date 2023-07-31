<?php
/**
 * @author RYU Chua <ryu@alpstein.my>
 * @link https://hustlehero.com.au
 * @copyright Copyright (c) Hustle Hero
 */

namespace api\modules\v1\controllers;

use api\base\rest\Controller;
use common\base\enum\CategoryCode;
use common\base\enum\FloorLevelCode;
use common\base\enum\FurnishingType;
use common\base\enum\PetPolicyCode;
use common\base\enum\TenureCode;
use common\base\helpers\ArrayHelper;
use common\models\SystemEnum;
use yii\data\ActiveDataProvider;
use Yii;

/**
 * Class CommonController
 * @package api\modules\v1\controllers
 */
class CommonController extends Controller
{
    /**
     * @return array
     */
    protected function optionals()
    {
        return ['index'];
    }

    /**
     * @return array
     */
    protected function verbs()
    {
        return [
            'index' => ['GET']
        ];
    }

    /**
     * @return ActiveDataProvider
     */
    public function actionIndex()
    {
        $data = $this->cache(__METHOD__ . '-v1', function () {
            return [
                'filter' => $this->getFilter(),
            ];
        }, mt_rand(60, 600));

        if (YII_DEBUG) {
            $data['_debug'] = true;
        }

        return $data;
    }

    /**
     * @return array[]
     */
    protected function getFilter()
    {
        $roomItems = [];
        foreach (range(0, 5) as $i) {
            $roomItems[(string) $i] = ['value' => (int) $i, 'name' => (string) $i];
        }
        ArrayHelper::setValue($roomItems, ['0', 'name'], 'Studio');
        ArrayHelper::setValue($roomItems, ['5', 'name'], '5+');

        $data = [
            'category' => CategoryCode::toArray(),
            'tenure' => [
                'items' => TenureCode::toArray(),
            ],
            'floorLevel' => [
                'items' => FloorLevelCode::toArray(),
            ],
            'furnishing' => [
                'items' => FurnishingType::toArray(),
            ],
            'petPolicy' => [
                'items' => PetPolicyCode::toArray(),
            ],
            'bedRoom' => [
                'items' => array_values($roomItems),
            ],
        ];

        ArrayHelper::remove($roomItems, '0');
        $data['bathRoom'] = ['items' => array_values($roomItems)];

        return $data;
    }


    /**
     * @param string $type
     * @return mixed
     */
    protected function getEnumItems($type)
    {
        $items = [];
        $query = SystemEnum::find()->alias('s')
            ->type($type)->active()->orderByDefault();

        /** @var SystemEnum $model */
        foreach ($query->all() as $model) {
            $items[] = ['value' => $model->getShortUuid(), 'name' => $model->name];
        }
        return $items;
    }
}