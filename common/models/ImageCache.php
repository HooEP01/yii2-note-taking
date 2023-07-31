<?php

/**
 * @copyright Copyright (c) Hustle Hero
 */

namespace common\models;

use common\base\db\ActiveRecord;
use common\base\enum\BehaviorCode;
use common\base\enum\ImageCacheStatus;
use common\base\helpers\ImageUploader;
use Imagine\Image\Box;
use Imagine\Imagick\Imagine;
use Yii;

/**
 * This is the model class for table "{{%image_cache}}".
 *
 * @property string $id
 * @property string $status e.g. Pending, Generated, Deleting, Deleted
 * @property string $imageId
 * @property string|null $format e.g. image/png
 * @property string|null $extension e.g. png, jpg, gif
 * @property int|null $size In Bytes
 * @property int|null $width In Pixel
 * @property int|null $height In Pixel
 * @property string|null $src
 * @property string $createdBy
 * @property string $createdAt
 * @property string $updatedBy
 * @property string $updatedAt
 * @property bool $isActive
 *
 * @property Image $image
 */
class ImageCache extends ActiveRecord
{
    /**
     * @return array
     */
    public function behaviors()
    {
        return [
            BehaviorCode::BLAMEABLE => [
                'class' => 'common\base\behaviors\BlameableBehavior',
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%image_cache}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['status', 'imageId'], 'required'],
            [['imageId', 'size', 'width', 'height'], 'default', 'value' => null],
            [['imageId'], 'string'],
            [['size', 'width', 'height'], 'integer'],
            [['src'], 'string'],
            [['status'], 'string', 'max' => 64],
            [['format', 'extension'], 'string', 'max' => 128],
            [['imageId', 'width', 'height', 'format', 'extension'], 'unique', 'targetAttribute' => ['imageId', 'width', 'height', 'format', 'extension']],
            [['imageId'], 'exist', 'skipOnError' => true, 'targetClass' => Image::class, 'targetAttribute' => ['imageId' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('model', 'common.id'),
            'status' => Yii::t('model', 'image_cache.status'),
            'imageId' => Yii::t('model', 'image_cache.imageId'),
            'format' => Yii::t('model', 'image_cache.format'),
            'extension' => Yii::t('model', 'image_cache.extension'),
            'size' => Yii::t('model', 'image_cache.size'),
            'width' => Yii::t('model', 'image_cache.width'),
            'height' => Yii::t('model', 'image_cache.height'),
            'src' => Yii::t('model', 'image_cache.src'),
            'createdBy' => Yii::t('model', 'common.createdBy'),
            'createdAt' => Yii::t('model', 'common.createdAt'),
            'updatedBy' => Yii::t('model', 'common.updatedBy'),
            'updatedAt' => Yii::t('model', 'common.updatedAt'),
            'isActive' => Yii::t('model', 'common.isActive'),
        ];
    }

    /**
     * @return bool
     */
    public function getIsPending()
    {
        return $this->status === ImageCacheStatus::PENDING;
    }

    /**
     * @return bool
     */
    public function getIsReady()
    {
        return $this->status === ImageCacheStatus::READY;
    }

    /**
     * @return string
     * @throws \Exception
     */
    public function getImageSrc()
    {
        $options = [
            'dimension' => sprintf('%dx%d/d2d6de/000000', $this->width, $this->height),
            'hash' => $this->getCdnHash(),
        ];

        return ImageUploader::resolveFileSource($this->src, $options);
    }

    /**
     * For CDN Query String if thing changes
     * @return string
     */
    public function getCdnHash()
    {
        $key = sprintf('%d-%dx%d-%s', $this->size, $this->width, $this->height, $this->src);
        return md5($key);
    }

    /**
     * @return bool
     * @throws \yii\base\Exception
     */
    public function generate()
    {
        if ($this->getIsReady()) {
            return true;
        }

        $fileSource = $this->image->src;
        $driver = ImageUploader::resolveDriverFromFileSource($fileSource);

        if ($driver === ImageUploader::DRIVER_S3 || $driver === ImageUploader::DRIVER_GCLOUD) {
            $original = $this->image->getImageSrc();
        } elseif ($driver === ImageUploader::DRIVER_LOCAL) {
            $original = ImageUploader::resolveFilePath($fileSource);
        }

        if (isset($original)) {
            $imagine = new Imagine();
            $image = $imagine->open($original);

            $tempFile = tempnam(sys_get_temp_dir(), '_reams_img_cache') . '.' . $this->image->extension;

            $image->resize(new Box($this->width, $this->height))
                ->save($tempFile, ['jpeg_quality' => 90]);

            if (is_file($tempFile)) {
                $this->size = filesize($tempFile);

                $uploader = new ImageUploader();
                $uploader->file($tempFile)
                    ->id(sprintf('%s_%dx%d', $this->imageId, $this->width, $this->height))
                    ->path('image/cache')
                    ->filename(sprintf('%s_%dx%d', $this->imageId, $this->width, $this->height))
                    ->extension($this->image->extension);

                if ($this->image->getIsPrivate()) {
                    $uploader->makePrivate();
                }

                if ($uploader->save()) {
                    //-- delete the file after upload
                    if (isset($tempFile) && file_exists($tempFile) && is_file($tempFile)) {
                        unlink($tempFile);
                    }

                    $this->src = $uploader->getFileSource();
                    $this->status = ImageCacheStatus::READY;
                    return $this->save(false);
                }
            }

            //-- delete the file in case reach here
            if (isset($tempFile) && file_exists($tempFile) && is_file($tempFile)) {
                unlink($tempFile);
            }
        }

        return false;
    }

    /**
     * @return \yii\db\ActiveQuery|ImageQuery
     */
    public function getImage()
    {
        return $this->hasOne(Image::class, ['id' => 'imageId']);
    }

    /**
     * {@inheritdoc}
     * @return ImageCacheQuery|\yii\db\ActiveQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new ImageCacheQuery(get_called_class());
    }
}
