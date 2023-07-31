<?php
/**
 * @author RYU Chua <ryu@alpstein.my>
 * @link https://hustlehero.com.au
 * @copyright Copyright (c) Hustle Hero
 */

namespace api\modules\v1\controllers;

use api\base\rest\Controller;
use common\models\PageContent;
use yii\web\NotFoundHttpException;
use Yii;

/**
 * Class PageController
 * @package api\modules\v1\controllers
 */
class PageController extends Controller
{
    /**
     * @return array
     */
    protected function optionals()
    {
        return [
            'view',
        ];
    }

    /**
     * @return array
     */
    protected function verbs()
    {
        return [
            'view' => ['GET']
        ];
    }

    /**
     * @param string $code
     * @return array
     * @throws NotFoundHttpException
     */
    public function actionView($code)
    {
        $model = PageContent::find()->alias('t')
            ->code($code)
            ->active()
            ->orderByDefault()
            ->limit(1)
            ->one();

        if ($model === null) {
            throw new NotFoundHttpException(Yii::t('api', 'model.not_found'));
        }

        return $model->toArray($this->loadFields(), $this->loadExpand());

    }
}