<?php
/**
 * @author RYU Chua <me@ryu.my>
 * @link https://ryu.my
 * @copyright Copyright (c) Hustle Hero Sdn. Bhd.
 */

namespace api\base\rest;

use common\base\helpers\UuidHelper;
use common\models\Image;
use common\models\User;
use yii\base\InvalidConfigException;
use yii\helpers\StringHelper;
use yii\web\BadRequestHttpException;
use yii\web\IdentityInterface;
use yii\web\NotFoundHttpException;
use Yii;

/**
 * Class Controller
 * @property User $user
 * @package api\base\rest
 */
class Controller extends \yii\rest\Controller
{
    /**
     * @var bool
     */
    public $layout = false;

    /**
     * @var array
     */
    public $serializer = [
        'class' => 'api\base\rest\Serializer',
        'collectionEnvelope' => 'items',
    ];

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        $behaviors = require(__DIR__ . '/behaviors.php');
        $behaviors['verbFilter']['actions'] = $this->verbs();
        $behaviors['authenticator']['optional'] = $this->optionals();
        return $behaviors;
    }

    /**
     * Declares the allowed actions without authentications (public actions).
     * Please refer to [[AuthMethod::optional]] for detail.
     * @return array the allowed actions.
     */
    protected function optionals()
    {
        return [];
    }

    /**
     * @return array
     * @throws InvalidConfigException
     */
    protected function getBodyData()
    {
        return ['data' => Yii::$app->request->getBodyParams()];
    }

    /**
     * @return User|IdentityInterface
     */
    protected function getUser()
    {
        return Yii::$app->user->getIdentity();
    }

    /**
     * @return Response
     */
    protected function createResponse()
    {
        return new Response();
    }

    /**
     * @return array
     */
    public function loadFields()
    {
        if (($fields = Yii::$app->request->getQueryParam('fields')) !== null) {
            return StringHelper::explode($fields);
        }
        return [];
    }

    /**
     * @param array $default
     * @return array
     */
    public function loadExpand($default = ['*'])
    {
        if (($expand = Yii::$app->request->getQueryParam('expand')) !== null) {
            return StringHelper::explode($expand);
        }
        return $default;
    }

    /**
     * @param string $id
     * @return Image|null
     * @throws NotFoundHttpException
     */
    public function findImage($id)
    {
        $uuid = UuidHelper::decodeShort($id);
        if (!UuidHelper::isValid($uuid)) {
            throw new BadRequestHttpException('Invalid ID');
        }

        if (($model = Image::findOne($uuid)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('api', 'model.not_found'));
    }

    /**
     * @param string $key
     * @param callable $callback
     * @param int $duration
     * @param null $dependency
     * @return mixed
     */
    public function cache($key, $callback, $duration = 0, $dependency = null)
    {
        $key = sprintf('%s.%s', $key, Yii::$app->language);
        return Yii::$app->cache->getOrSet($key, $callback, $duration, $dependency);
    }
}
