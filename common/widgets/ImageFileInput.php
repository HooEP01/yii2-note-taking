<?php
/**
 * @copyright Copyright (c) Hustle Hero
 */

namespace common\widgets;

use kartik\file\FileInput;

/**
 * Class ImageFileInput
 * @package common\widgets
 */
class ImageFileInput extends FileInput
{
    /**
     * @var array
     */
    public $options = ['accept' => 'image/*'];

    /**
     * @var array
     */
    public $pluginOptions = [
        'allowedPreviewTypes' => ['image', 'video'],
        'allowedFileExtensions' => ['jpg', 'jpeg', 'png', 'gif', 'webp'],
        'fileActionSettings' => [
            'showZoom' => false,
            'indicatorNew' => '',
        ],
        'dropZoneEnabled' => false,
        'showUpload' => false,
        'showClose' => false,
    ];
}
