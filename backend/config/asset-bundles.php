<?php
return [
    'yii\bootstrap4\BootstrapAsset' => [
        'css' => [
            'css/bootstrap.min.css'
        ],
    ],
    'yii\bootstrap4\BootstrapPluginAsset' => [
        'js' => [
            'js/bootstrap.bundle.min.js',
        ],
    ],
    'dosamigos\tinymce\TinyMceAsset' => [
        'sourcePath' => null,
        'basePath' => '@webroot/vendor/tinymce/v5.10.5',
        'baseUrl' => '@web/vendor/tinymce/v5.10.5',
        'js' => [
            'js/tinymce.min.js',
        ],
        'jsOptions' => [
            'position' => \yii\web\View::POS_HEAD,
        ],
    ],
];