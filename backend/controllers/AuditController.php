<?php

/**
 * @author RYU Chua <me@ryu.my>
 */

namespace backend\controllers;

use backend\models\AuditTrailSearch;
use backend\base\web\Controller;
use common\base\enum\UserRole;
use Yii;
use yii\web\HttpException;

/**
 * Class AuditController
 * @package backend\controllers
 */
class AuditController extends Controller
{
    /**
     * @var string
     */
    public $defaultAction = 'trail';

    /**
     * Lists all Audit Trail models.
     * @return mixed
     */
    public function actionTrail()
    {
        if (!$this->user->getIsSystemAdmin()) {
            throw new HttpException(403, "You are not allowed to perform this action.");
        }

        $searchModel = new AuditTrailSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('trail', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
}
