<?php
/**
 * @copyright Copyright (c) Hustle Hero
 */

namespace backend\assets;

use yii\web\AssetBundle;
use yii\web\View;

/**
 * Class ThemeAsset
 * @package backend\assets
 */
class ThemeAsset extends AssetBundle
{
    /**
     * To be published
     * @var string
     */
    public $sourcePath = __DIR__ . '/adminlte';

    /**
     * @var array
     */
    public $jsOptions = [
        'position' => View::POS_END,
    ];

    /**
     * @var array
     */
    public $css = [
        'plugins/fontawesome-free/css/all.min.css',
        'plugins/icheck-bootstrap/icheck-bootstrap.min.css',
        'plugins/toastr/toastr.min.css',
        'dist/css/adminlte.min.css',
    ];

    /**
     * @var array
     */
    public $js = [
        'plugins/toastr/toastr.min.js',
        'dist/js/adminlte.min.js',
    ];
}