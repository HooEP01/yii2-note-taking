<?php
/**
 * @copyright Copyright (c) Hustle Hero
 */

namespace backend\base\web;


use backend\setups\BaseSetup;
use backend\setups\ModelBaseSetup;
use yii\base\Action;
use yii\base\InvalidConfigException;
use yii\db\ActiveRecord;
use Yii;

/**
 * Class SetupAction
 * @package backend\base\web
 */
class SetupAction extends Action
{
    /**
     * @var BaseSetup
     */
    public $setup;
    /**
     * @var string
     */
    public $mainView;
    /**
     * @var string
     */
    public $setupView;
    /**
     * @var string
     */
    public $screenshots;

    /**
     * checking
     */
    public function init()
    {
        parent::init();

        if (!isset($this->setup)) {
            throw new InvalidConfigException('$setup must be set!');
        }

        if (!isset($this->mainView)) {
            throw new InvalidConfigException('$mainView must be set!');
        }

        if (!isset($this->setupView)) {
            throw new InvalidConfigException('$setupView must be set!');
        }
    }

    /**
     * Runs the action.
     *
     * @return string result content
     * @throws InvalidConfigException
     */
    public function run()
    {
        /** @var BaseSetup $model */
        $model = Yii::createObject($this->setup);
        if ($model instanceof ModelBaseSetup && ($id = Yii::$app->request->getQueryParam('id')) !== null) {
            if (!empty($model->modelClass)) {
                /** @var ActiveRecord $m */
                $m = $model->modelClass;
                $model->setModel($m::findOne($id));
            }
        }

        if ($model instanceof BaseSetup) {
            if ($model->load(Yii::$app->request->post()) && $model->save()) {
                Yii::$app->session->setFlash('success', Yii::t('backend', 'model.update.success'));
                return $this->controller->refresh();
            }
        }

        $params = ['setup' => $model];
        if (!empty($this->screenshots) && is_array($this->screenshots)) {
            $params['screenshots'] = $this->screenshots;
        }

        return $this->controller->render($this->mainView, [
            'content' => $this->controller->renderPartial($this->setupView, $params)
        ]);
    }
}