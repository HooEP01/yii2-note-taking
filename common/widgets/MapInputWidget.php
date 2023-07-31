<?php
/**
 * @copyright Copyright (c) Hustle Hero
 */

namespace common\widgets;

use common\base\enum\ConfigName;
use kolyunya\yii2\assets\MapInputAsset;
use Yii;

/**
 * Class MapInputWidget
 * @package common\widgets
 */
class MapInputWidget extends \kolyunya\yii2\widgets\MapInputWidget
{
    /**
     * @var number
     */
    public $longitude = 103.72402060416755;

    /**
     * @var number
     */
    public $latitude = 1.5925056446750858;

    /**
     * @var int
     */
    public $zoom = 18;

    /**
     * @var string
     */
    public $width = '100%';

    /**
     * @var string
     */
    public $height = '400px';

    /**
     * @var string
     */
    public $pattern = '%longitude%,%latitude%';

    /**
     * @var bool
     */
    public $animateMarker = false;
    /**
     * @var string
     */
    public $googleMapApiKey;

    /**
     * @throws \yii\base\InvalidConfigException
     */
    public function init()
    {
        parent::init();
        $this->googleMapApiKey = Yii::$app->config->get(ConfigName::GOOGLE_MAP_API_KEY);
    }

    /**
     * @return string
     */
    public function run()
    {
        Yii::setAlias('@kolyunya', '@vendor/kolyunya');
        MapInputAsset::$key = $this->googleMapApiKey;

        return $this->render(
            '@kolyunya/yii2-map-input-widget/sources//widgets/views/MapInputWidget',
            [
                'id' => $this->getId(),
                'model' => $this->model,
                'attribute' => $this->attribute,
                'latitude' => $this->latitude,
                'longitude' => $this->longitude,
                'zoom' => $this->zoom,
                'width' => $this->width,
                'height' => $this->height,
                'pattern' => $this->pattern,
                'mapType' => $this->mapType,
                'animateMarker' => $this->animateMarker,
                'alignMapCenter' => $this->alignMapCenter,
                'enableSearchBar' => $this->enableSearchBar,
            ]
        );
    }
}