<?php
/**
 * @copyright Copyright (c) Hustle Hero
 */

namespace common\base\behaviors;

use common\models\ContactQuery;
use yii\base\Behavior;
use yii\db\ActiveRecord;
use Yii;

/**
 * Class ContactBehavior
 * @property ActiveRecord $owner
 * @package common\base\behaviors
 */
class ContactBehavior extends Behavior
{
    /**
     * @var string
     */
    public $contactClass = 'common\models\Contact';

    /**
     * @return ContactQuery|\yii\db\ActiveQuery
     */
    public function getContacts()
    {
        /** @var ContactQuery $query */
        $query = call_user_func([$this->contactClass, 'find']);
        $query->alias('c')->owner($this->owner);
        $query->active()->orderByDefault();

        $query->multiple = true;

        return $query;
    }
}
