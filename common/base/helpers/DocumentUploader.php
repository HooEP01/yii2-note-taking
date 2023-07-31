<?php
/**
 * @copyright Copyright (c) Hustle Hero
 */

namespace common\base\helpers;

/**
 * Class DocumentUploader
 * @package common\base\helpers
 */
class DocumentUploader extends FileUploader
{
    /**
     * @return string
     */
    public function getFormat()
    {
        return $this->getMime();
    }

    /**
     * @return string
     * @throws \yii\base\Exception
     */
    public function getExtensionFromMime()
    {
        $mime = $this->getMime();
        if ($mime === 'image/jpeg' || $mime === 'image/jpg') {
            return 'jpg';
        } elseif ($mime === 'image/png') {
            return 'png';
        } elseif ($mime === 'application/pdf') {
            return 'pdf';
        }

        return parent::getExtensionFromMime();
    }
}