<?php
/**
 * @copyright Copyright (c) Hustle Hero
 */

namespace common\base;

/**
 * Class Application
 * @property \yii\mutex\Mutex $mutex
 * @property \api\base\web\AccountUser $accountUser
 * @property \backend\base\web\User $user
 * @property \common\base\i18n\Formatter $formatter
 * @property \common\base\audit\Audit $audit
 * @property \common\base\services\Sanitizer $sanitizer
 * @property \common\base\services\Platform $platform
 * @property \common\base\services\Config $config
 * @property \common\base\services\OpenSSL $openssl
 * @property \common\base\services\Aws $aws
 * @property \common\base\services\Gcp $gcp
 * @property \common\base\services\Firebase $firebase
 * @property \common\base\services\DynamoDb $dynamoDb
 * @property \common\base\services\Qr $qr
 * @property \common\base\jwt\Jwt $jwt
 * @property \yii\queue\Queue $queue
 * @property \yii\queue\Queue $pipeline
 *
 * @package common\base
 */
abstract class Application extends \yii\base\Application
{
}
