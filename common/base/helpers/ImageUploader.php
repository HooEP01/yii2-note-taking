<?php

/**
 * @copyright Copyright (c) Hustle Hero
 */


namespace common\base\helpers;

use Imagine\Exception\RuntimeException;
use Imagine\Gd\Imagine;
use Imagine\Image\Box;
use Imagine\Image\ImageInterface;
use Yii;
use yii\helpers\FileHelper;

/**
 * Class ImageUploader
 * @package common\base\web
 */
class ImageUploader extends FileUploader
{
    /**
     * @param int $width Width in pixel
     * @param int $height Height in pixel
     * @param string $filter The Imagine resize filter
     * @@return $this
     */
    public function resize($width, $height, $filter = ImageInterface::FILTER_UNDEFINED)
    {
        try {
            $imagine = new Imagine();
            $image = $imagine->open($this->getFile());

            $image->resize(new Box($width, $height), $filter);

            $tempFile = $this->generateTemporaryFile() . '.' . $this->getExtension();
            $image->save($tempFile, ['jpeg_quality' => 90]);

            return $this->replaceFile($tempFile);
        } catch (RuntimeException $e) {
            Yii::error($e->getMessage());
        } catch (\Exception $e) {
            Yii::error($e);
        }

        return $this;
    }

    /**
     * @return mixed
     */
    public function getImageWidth()
    {
        return $this->getProperty('image_width', -1);
    }

    /**
     * @return mixed
     */
    public function getImageHeight()
    {
        return $this->getProperty('image_height', -1);
    }

    /**
     * @return mixed
     */
    public function getImageFormat()
    {
        return $this->getProperty('image_format', 'unknown');
    }

    /**
     * generate more property
     */
    protected function generateProperty()
    {
        parent::generateProperty();

        try {
            $filePath = $this->getFile();

            $imagine = new Imagine();
            $image = $imagine->open($filePath);

            $this->addProperty('image_width', $image->getSize()->getWidth());
            $this->addProperty('image_height', $image->getSize()->getHeight());

            $type = exif_imagetype($filePath);
            $this->addProperty('image_format', image_type_to_mime_type($type));
        } catch (\Exception $e) {
            Yii::error($e);
        }
    }

    protected function generateTemporaryFile()
    {
        $folder = sys_get_temp_dir() . DIRECTORY_SEPARATOR . '_alps';
        FileHelper::createDirectory($folder);
        return tempnam($folder, '_uploader');
    }

    /**
     * @return string
     */
    public function getExtensionFromMime()
    {
        $mime = $this->getMime();
        if ($mime === 'image/jpeg' || $mime === 'image/jpg') {
            return 'jpg';
        } elseif ($mime === 'image/png') {
            return 'png';
        }

        return 'jpg';
    }
}
