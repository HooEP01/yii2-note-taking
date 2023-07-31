<?php

namespace backend\assets;

use yii\web\AssetBundle;

/**
 * Main backend application asset bundle.
 */
class AppAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'css/site.css',
        'css/theme-extra.css',
        'vendor/fancybox/css/jquery.fancybox.css',
    ];

    public $js = [
        'vendor/fancybox/js/jquery.fancybox.js',
    ];

    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap4\BootstrapAsset',
        'backend\assets\ThemeAsset',
    ];
}
