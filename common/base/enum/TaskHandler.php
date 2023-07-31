<?php
/**
 * @copyright Copyright (c) Hustle Hero
 */

namespace common\base\enum;

/**
 * Class TaskHandler
 * @package common\base\enum
 */
class TaskHandler extends BaseEnum
{
    const QUEUE = 'queue';
    const GCP_PUB_SUB = 'gcp-pub-sub';

    /**
     * @return array
     */
    public static function options()
    {
        return [
            self::QUEUE => 'Using System Queue + Supervisor',
            self::GCP_PUB_SUB => 'Google Cloud Pub Sub + Cloud Run (Serverless)',
        ];
    }
}
