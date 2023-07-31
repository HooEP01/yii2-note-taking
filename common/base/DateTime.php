<?php
/**
 * @copyright Copyright (c) Hustle Hero
 */


namespace common\base;

use DateTimeZone;
use JetBrains\PhpStorm\Internal\LanguageLevelTypeAware;
use Yii;

/**
 * Class DateTime
 * @package common\base
 */
class DateTime extends \DateTime
{
    /**
     * DateTime constructor.
     * @param string $time
     * @param \DateTimeZone $timezone
     * @throws \Exception
     */
    public function __construct($time = 'now', DateTimeZone $timezone = null)
    {
        if ($time === 'now') {
            $datetime = static::getCurrentDateTime();
            return parent::__construct($datetime, $timezone);
        }
        return parent::__construct($time, $timezone);
    }

    public static function createFromFormat($format, $datetime, DateTimeZone $timezone = null)
    {
        return parent::createFromFormat($format, $datetime, $timezone);
    }

    /**
     * @return $this
     */
    public function local()
    {
        $this->setTimezone(new DateTimeZone(Yii::$app->timeZone));
        return $this;
    }

    /**
     * @return string
     */
    public function formatToDisplayDateTime()
    {
        return $this->format('d/m/Y h:i A');
    }

    /**
     * @return string
     */
    public function formatToDatabaseDate()
    {
        return $this->format('Y-m-d');
    }

    /**
     * @return string
     */
    public function formatToDatabaseDatetime()
    {
        return $this->format('Y-m-d H:i:s');
    }

    /**
     * @return string
     */
    public function formatToISO8601()
    {
        return $this->format(self::ISO8601);
    }

    /**
     * @return string
     */
    public function formatToRFC3339()
    {
        return $this->format(self::RFC3339);
    }

    /**
     * @return int
     */
    public function getDateInteger()
    {
        return (int) $this->format('Ymd');
    }

    /**
     * @return string
     */
    public static function getCurrentDateTimeISO8601()
    {
        return date(self::ISO8601, self::getCurrentTimestamp());
    }

    /**
     * Get current date time format in Y-m-d H:i:s format
     * @return string
     */
    public static function getCurrentDateTime()
    {
        return date('Y-m-d H:i:s', self::getCurrentTimestamp());
    }

    /**
     * @return integer
     */
    public static function getCurrentMicroTime()
    {
        return (int) round(microtime(true) * 1000);
    }

    /**
     * @return string
     */
    public static function getCurrentTime()
    {
        return date('H:i:s', self::getCurrentTimestamp());
    }

    /**
     * Get current date format in Y-m-d format
     * @return string
     */
    public static function getCurrentDate()
    {
        return date('Y-m-d', self::getCurrentTimestamp());
    }

    /**
     * @return int
     */
    public static function getCurrentTimestamp()
    {
        if (YII_DEBUG) {
            $params = Yii::$app->params;
            if (isset($params['debug']) && isset($params['debug']['datetime'])) {
                return strtotime($params['debug']['datetime']);
            }
        }
        return time();
    }

    /**
     * @return static
     * @throws \Exception
     */
    public static function now()
    {
        $datetime = static::getCurrentDateTime();
        return new static($datetime);
    }
}
