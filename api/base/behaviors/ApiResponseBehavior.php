<?php
/**
 * @author RYU Chua <me@ryu.my>
 * @link https://ryu.my
 * @copyright Copyright (c) Hustle Hero Sdn. Bhd.
 */

namespace api\base\behaviors;

use yii\base\Behavior;
use yii\base\Event;
use yii\web\Response;

/**
 * Class ApiResponseBehavior
 * @package frontend\base\behaviors
 */
class ApiResponseBehavior extends Behavior
{
    /**
     * @return array
     */
    public function events()
    {
        return [
            Response::EVENT_BEFORE_SEND => 'enforceJsonResponse'
        ];
    }

    /**
     * @param Event $event
     */
    public function enforceJsonResponse($event)
    {
        /** @var Response $response */
        $response = $event->sender;
        if ($response->data !== null) {
            if (!$response->isSuccessful) {
                $responseData = [
                    'success' => false,
                    'status_code' => $response->statusCode,
                    'messages' => [],
                ];

                if (isset($response->data['messages'])) {
                    $responseData['messages'] = $response->data['messages'];
                } elseif (isset($response->data['message'])) {
                    $responseData['messages'][] = ['type' => 'error', 'text' => $response->data['message']];
                }

                if (isset($response->data['_extra']) && is_array($response->data['_extra'])) {
                    foreach ($response->data['_extra'] as $key => $value) {
                        $responseData[$key] = $value;
                    }
                }

                // model saving error
                if ($response->statusCode == 422 && is_array($response->data)) {
                    $messages = [];
                    $errorMaps = [];

                    foreach ($response->data as $error) {
                        $messages[] = ['type' => 'error', 'text' => $error['message'], 'attribute' => $error['field']];
                        $errorMaps[$error['field']] = $error['message'];
                    }
                    $responseData['messages'] = $messages;
                    $responseData['error_maps'] = $errorMaps;
                }


                /** @var \Exception $e */
                if (isset($response->data['exception']) && ($e = $response->data['exception']) instanceof \Exception) {
                    $responseData['messages'][] = ['type' => 'error', 'text' => $e->getMessage()];
                }

                if (YII_DEBUG && isset($response->data['type']) && isset($response->data['previous'])) {
                    $responseData['type'] = $response->data['type'];
                    $responseData['debug'] = $response->data['previous'];
                }

                $response->data = $responseData;
            }
        }
    }
}
