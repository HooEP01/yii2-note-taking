<?php
/**
 * @author RYU Chua <me@ryu.my>
 * @link https://ryu.my
 * @copyright Copyright (c) Hustle Hero Sdn. Bhd.
 */

namespace api\base\rest;

use common\base\helpers\ArrayHelper;
use yii\data\Pagination;

/**
 * Class Serializer
 * @package api\base\rest
 */
class Serializer extends \yii\rest\Serializer
{
    /**
     * Serializes a pagination into an array.
     * @param Pagination $pagination
     * @return array the array representation of the pagination
     * @see addPaginationHeaders()
     */
    protected function serializePagination($pagination)
    {
        $data = parent::serializePagination($pagination);

        foreach ($pagination->getLinks(false) as $ref => $path) {
            ArrayHelper::setValue($data, [$this->linksEnvelope, $ref, 'method'], 'GET');
            ArrayHelper::setValue($data, [$this->linksEnvelope, $ref, 'path'], $path);
        }

        return $data;
    }
}
