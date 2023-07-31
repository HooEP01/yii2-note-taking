<?php
/**
 * @copyright Copyright (c) Hustle Hero
 */

namespace common\widgets;

use dosamigos\tinymce\TinyMce;
use common\base\helpers\Url;
use yii\web\View;
use Yii;

/**
 * Class SimpleRichTextEditor
 * @package common\widgets
 */
class SimpleRichTextEditor extends TinyMce
{
    /**
     * @var string
     */
    public static $FOCUS_TEXT_TYPE = 'Focus Text Type';

    /**
     * @var string
     */
    public $type = 'default';

    /**
     * @var bool
     */
    public $stickyToolbar = true;

    const TYPE_DEFAULT = 'default';
    const TYPE_SIMPLE = 'simple';
    const TYPE_ADVANCE = 'advance';
    const TYPE_LISTING = 'listing';
    const TYPE_POST = 'post';

    /**
     * @throws \yii\base\InvalidConfigException
     */
    public function init()
    {
        parent::init();

        $imageUploadUrl = Url::to(['/image/upload']);

        $plugins = [
            'advlist', 'autolink', 'lists', 'link', 'image', 'charmap', 'print', 'preview', 'anchor',
            'searchreplace', 'visualblocks', 'code', 'fullscreen', 'autoresize', 'insertdatetime', 'media', 'table',
            'paste', 'wordcount',
        ];

        if (!in_array($this->type, [self::TYPE_DEFAULT, self::TYPE_SIMPLE, self::TYPE_ADVANCE, self::TYPE_LISTING, self::TYPE_POST])) {
            $this->type = 'default';
        }

        $toolbar = 'formatselect | bold italic | alignleft aligncenter alignright alignjustify | link image media | code';
        if ($this->type === self::TYPE_SIMPLE) {
            $toolbar = 'formatselect | bold italic | alignleft aligncenter alignright alignjustify';
        } elseif ($this->type === self::TYPE_ADVANCE) {
            $toolbar = 'formatselect | bold italic strikethrough forecolor backcolor | alignleft aligncenter alignright alignjustify | numlist bullist outdent indent | link image media | code';
        } elseif ($this->type === self::TYPE_LISTING) {
            $toolbar = 'formatselect | paste pastetext | bold italic strikethrough | alignleft aligncenter alignright alignjustify | numlist bullist outdent indent';
        } elseif ($this->type === self::TYPE_POST) {
            $toolbar = 'formatselect | paste pastetext | bold italic strikethrough
            | alignleft aligncenter alignright alignjustify
            | numlist bullist outdent indent | link image media
            | table tabledelete | tableprops tablerowprops tablecellprops
            | tableinsertrowbefore tableinsertrowafter tabledeleterow
            | tableinsertcolbefore tableinsertcolafter tabledeletecol
            | code';
        }

        $this->clientOptions = [
            'menubar' => false,
            'height' => 450,
            'plugins' => $plugins,
            'toolbar' => $toolbar,
            'images_upload_url' => $imageUploadUrl,
            'images_reuse_filename' => true,
            'image_dimensions' => false,
            'autoresize_bottom_margin' => 50,
            'paste_as_text' => true,
            'object_resizing' => false,
            'toolbar_sticky' => $this->stickyToolbar,
        ];
    }
}
