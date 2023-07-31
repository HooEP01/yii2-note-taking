<?php
/**
 * @author RYU Chua <me@ryu.my>
 * @link https://ryu.my
 * @copyright Copyright (c) Hustle Hero Sdn. Bhd.
 */

namespace api\base\rest;

use yii\base\BaseObject;
use Yii;

/**
 * Class Response
 * @package api\base\rest
 */
class Response extends BaseObject
{
    /**
     * @var array stacks of messages
     */
    public $messages = [];

    /**
     * @var array extra data
     */
    private $_extra = [];

    /**
     * @param int $statusCode
     * @param string|null $text
     * @return array
     */
    public function success($statusCode = 200, $text = null)
    {
        Yii::$app->response->setStatusCode($statusCode, $text);

        $data = $this->generate();
        $data['success'] = true;

        return $data;
    }
    /**
     * @param int $statusCode
     * @param string|null $text
     * @return array
     */
    public function error($statusCode = 400, $text = null)
    {
        Yii::$app->response->setStatusCode($statusCode, $text);

        $data = $this->generate();

        $data['_extra'] = [];
        foreach ($this->_extra as $key => $value) {
            $data['_extra'][$key] = $value;
        }

        $data['success'] = false;

        return $data;
    }

    /**
     * @param string $name
     * @param mixed $value
     * @return $this
     */
    public function addExtraData($name, $value)
    {
        $this->_extra[$name] = $value;
        return $this;
    }

    /**
     * @param string $message
     * @return $this
     */
    public function addSuccessMessage($message)
    {
        return $this->addMessage('success', $message);
    }
    /**
     * @param string $message
     * @return $this
     */
    public function addErrorMessage($message)
    {
        return $this->addMessage('error', $message);
    }

    /**
     * @param string $type
     * @param string $text
     * @return $this
     */
    protected function addMessage($type, $text)
    {
        $this->messages[] = ['type' => $type, 'text' => $text];
        return $this;
    }

    /**
     * @return array
     */
    protected function generate()
    {
        $data = [
            'success' => true,
            'messages' => $this->messages,
        ];

        foreach ($this->_extra as $key => $value) {
            $data[$key] = $value;
        }

        return $data;
    }
}
