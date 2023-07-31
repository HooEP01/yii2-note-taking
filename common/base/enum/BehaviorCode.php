<?php

/**
 * @copyright Copyright (c) Hustle Hero
 */


namespace common\base\enum;

/**
 * Class BehaviorCode
 * @package common\base\enum
 */
class BehaviorCode extends BaseEnum
{
    const BLAMEABLE = 'blameable';
    const NULL = 'null';

    const AUDIT = 'audit';
    const IMAGE = 'image';
    const CONTACT = 'contact';
    const SANITIZE = 'sanitize';
    const TRANSLATION = 'translation';
    const SLUGGABLE = 'sluggable';
    const INTEGER_DECIMAL = 'integerDecimal';
    const ARRAY_EXPRESSION = 'arrayExpression';
    const DATETIME = 'datetime';
    const DATE_FORMAT = 'date-format';
    const COORDINATE = 'coordinate';
    const CONFIG = 'config';

    const UUID = 'uuid';
    const TOKEN = 'token';

    //-- for controller
    const CONTEXT = 'context';

    const ACTION = "action";
}
