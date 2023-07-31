<?php
/**
* @copyright Copyright (c) Hustle Hero
*/

namespace common\models;

use common\base\db\ActiveRecord;
use common\base\enum\BehaviorCode;
use common\base\enum\CallToActionType;
use common\base\enum\ImageCacheStatus;
use common\base\enum\ImageCode;
use common\base\enum\ImageVariant;
use common\base\helpers\ArrayHelper;
use common\base\helpers\FileUploader;
use common\base\helpers\ImageUploader;
use common\base\helpers\Json;
use common\base\helpers\UuidHelper;
use common\base\traits\RuntimeCache;
use common\jobs\CacheImage;
use common\jobs\WatermarkImage;
use common\models\image\BaseCallToAction;
use common\models\image\UrlCallToAction;
use ReflectionClass;
use yii\web\UploadedFile;
use Yii;

/**
 * This is the model class for table "{{%image}}".
 *
 * @property string $id
 * @property string|null $code
 * @property string $name
 * @property string|null $title For image title
 * @property string|null $caption For image alt
 * @property string|null $ownerType Table name of the model or class of owner
 * @property string|null $ownerKey The string representation of pk or id
 * @property string|null $format e.g. image/png
 * @property string|null $extension e.g. png, jpg, gif
 * @property int|null $size In Bytes
 * @property int|null $width In Pixel
 * @property int|null $height In Pixel
 * @property string|null $src
 * @property string|null $callToAction
 * @property string|null $configuration
 * @property string|null $cacheIndex
 * @property int|null $position sorting, or ordering purpose
 * @property string|null $variant e.g. hover, mobile, etc
 * @property string|null $parentId for variant, to known which is parent image
 * @property string|null $cloneId for clone image, to known which image clone from
 * @property string|null $accountId to know this count to whose data
 * @property bool $isPrivate
 * @property bool $isSystem
 * @property string $createdBy
 * @property string $createdAt
 * @property string $updatedBy
 * @property string $updatedAt
 * @property bool $isActive
 *
 * @property City[] $cities
 * @property Country[] $countries
 * @property Currency[] $currencies
 * @property ImageCache[] $imageCaches
 * @property Image[] $images
 * @property Language[] $languages
 * @property Image $parent
 * @property Image $clone
 * @property State[] $states
 * @property User[] $users
 */
class Image extends ActiveRecord
{
    use RuntimeCache;

    /**
     * @var UploadedFile
     */
    public $upload;

    /**
     * @return array
     */
    public function behaviors()
    {
        return [
            BehaviorCode::BLAMEABLE => [
                'class' => 'common\base\behaviors\BlameableBehavior',
            ],
            BehaviorCode::SANITIZE => [
                'class' => 'common\base\behaviors\SanitizeBehavior',
                'stripCleanAttributes' => ['code', 'name', 'title', 'caption']
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%image}}';
    }

    /**
     * @return array|false
     */
    public function fields()
    {
        return [
            'id' => function () {
                if (!empty($this->parentId)) {
                    return UuidHelper::encodeShort($this->parentId);
                }

                return UuidHelper::encodeShort($this->id);
            },
            'title',
            'cta' => function () {
                if (($cta = $this->getCallToActionModel()) instanceof UrlCallToAction && $cta->getIsReady()) {
                    return ['type' => CallToActionType::URL, 'data' => $cta->toArray()];
                }

                return null;
            },
            'original' => function () {
                return $this->getOriginalField();
            },
            'tiny' => function () {
                return $this->getResizeField(['width' => 128]);
            },
            'small' => function () {
                return $this->getResizeField(['width' => 256]);
            },
            'medium' => function () {
                return $this->getResizeField(['width' => 512]);
            },
            'large' => function () {
                return $this->getResizeField(['width' => 1024]);
            },
            'huge' => function () {
                return $this->getResizeField(['width' => 2048]);
            },
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        $rules = [
            [['position'], 'default', 'value' => 999],
            [['name'], 'required'],
            [['title', 'caption', 'src', 'configuration'], 'string'],
            [['size', 'width', 'height', 'position'], 'integer'],
            [['code', 'ownerType', 'ownerKey', 'format', 'extension'], 'string', 'max' => 128],
            [['name'], 'string', 'max' => 160],
            [['isActive', 'isPrivate', 'isSystem'], 'boolean'],
        ];

        $maxSize = Yii::$app->config->getImageMaximumSize();

        $rules[] = [
            ['upload'],
            'image',
            'skipOnEmpty' => false,
            'extensions' => 'jpg, jpeg, png, gif, webp',
            'maxSize' => $maxSize,
            'tooBig' => 'The file "{file}" is too big. Its size cannot exceed ' . Yii::$app->formatter->asShortSize($maxSize),
            'on' => ['upload', 'backend']
        ];

        $rules[] = [
            ['upload'],
            'image',
            'skipOnEmpty' => true,
            'extensions' => 'jpg, jpeg, png, gif, webp',
            'maxSize' => $maxSize,
            'tooBig' => 'The file "{file}" is too big. Its size cannot exceed ' . Yii::$app->formatter->asShortSize($maxSize),
            'on' => ['optional']
        ];

        return $rules;
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('model', 'common.id'),
            'code' => Yii::t('model', 'common.code'),
            'name' => Yii::t('model', 'common.name'),
            'title' => Yii::t('model', 'common.title'),
            'caption' => Yii::t('model', 'common.caption'),
            'ownerType' => Yii::t('model', 'common.ownerType'),
            'ownerKey' => Yii::t('model', 'common.ownerKey'),
            'format' => Yii::t('model', 'image.format'),
            'extension' => Yii::t('model', 'image.extension'),
            'size' => Yii::t('model', 'common.size'),
            'width' => Yii::t('model', 'common.width'),
            'height' => Yii::t('model', 'common.height'),
            'src' => Yii::t('model', 'common.src'),
            'callToAction' => Yii::t('model', 'image.callToAction'),
            'data' => Yii::t('model', 'common.data'),
            'cacheIndex' => Yii::t('model', 'image.cacheIndex'),
            'position' => Yii::t('model', 'common.position'),
            'variant' => Yii::t('model', 'image.variant'),
            'parentId' => Yii::t('model', 'common.parentId'),
            'createdBy' => Yii::t('model', 'common.createdBy'),
            'createdAt' => Yii::t('model', 'common.createdAt'),
            'updatedBy' => Yii::t('model', 'common.updatedBy'),
            'updatedAt' => Yii::t('model', 'common.updatedAt'),
            'isActive' => Yii::t('model', 'common.isActive'),
        ];
    }

    /**
     * @param array $options
     * @return array
     * @throws \Exception
     */
    public function getResizeField($options = [])
    {
        $data = $this->getData();

        if ($this->getHasImage()) {
            $data['src'] = $this->getCacheImageSrc($options);
        }

        return $data;
    }

    /**
     * @return array
     */
    public function getOriginalField()
    {
        $data = $this->getData();

        if ($this->getHasImage()) {
            $data['src'] = $this->getImageSrc();
        }

        return $data;
    }

    /**
     * @param string $variant
     * @param bool $save
     * @return static
     */
    public function variant($variant, $save = true)
    {
        return $this->getOrSetRuntimeData(__METHOD__ . $variant, function () use ($variant, $save) {
            /** @var Image $model */
            $model = static::find()->parent($this)->variant($variant)->active()->one();
            if ($model === null) {
                $model = static::factory($this);
                $model->name = $this->name . ' (' . $variant . ')';
                $model->variant = $variant;
                $model->parentId = $this->id;
                $model->accountId = $this->accountId;

                if ($save) {
                    $model->save();
                }
            }

            return $model;
        });
    }

    /**
     * @return $this|Image
     */
    public function getWatermarkModel()
    {
        $variant = $this->variant(ImageVariant::WATERMARK, false);
        if (!$variant->getIsNewRecord() && $variant->getHasImage()) {
            return $variant;
        }

        $this->generateWatermark();
        return $this;
    }

    /**
     * Generate watermark variant for this image
     * @return bool
     */
    public function generateWatermark()
    {
        //-- if parent not empty should not generate watermark
        if (!empty($this->parentId)) {
            return true;
        }

        $taskId = Yii::$app->pipeline->push(new WatermarkImage(['id' => $this->id]));
        return !empty($taskId);
    }

    /**
     * @return array
     */
    protected function getData()
    {
        return ['src' => null];
    }

    /**
     * @return array
     */
    protected function getOwnerClassMaps()
    {
        return $this->getOrSetRuntimeData(__METHOD__, function () {
            $class = new ReflectionClass($this);
            $namespace = $class->getNamespaceName();

            $maps = [];
            foreach (['Listing', 'Account'] as $name) {
                $className = sprintf('%s\%s', $namespace, $name);
                if (class_exists($className)) {
                    $tableName = call_user_func([$className, 'tableName']);
                    $maps[$tableName] = $className;
                }
            }
            return $maps;
        });
    }

    /**
     * @return null
     */
    public function getOwnerModel()
    {
        return $this->getOrSetRuntimeData(__METHOD__, function () {
            $maps = $this->getOwnerClassMaps();

            if (isset($maps[$this->ownerType])) {
                $class = $maps[$this->ownerType];
                $model = $class::findOne($this->ownerKey);
                return $model;
            }

            return null;
        });
    }

    /**
     * @return bool
     */
    public function getIsPrivate()
    {
        return (bool) $this->isPrivate;
    }

    /**
     * @return string
     */
    public function getFileSource()
    {
        return $this->src;
    }

    /**
     * @return bool
     */
    public function getHasImage()
    {
        return !empty($this->src);
    }

    /**
     * @return string
     * @throws \Exception
     */
    public function getImageSrc()
    {
        $options = [
            'dimension' => '1920x1080/d2d6de/000000',
            'sign' => $this->getIsPrivate(),
            'hash' => $this->getCdnHash(),
        ];

        return FileUploader::resolveFileSource($this->src, $options);
    }

    /**
     * @param array $options
     * @return string
     * @throws \Exception
     */
    public function getCacheImageSrc($options = [])
    {
        if ($this->getIsNewRecord()) {
            return $this->getImageSrc();
        }

        if ($this->clone && $this->clone->getCdnHash() === $this->getCdnHash()) {
            return $this->clone->getCacheImageSrc($options);
        }

        if (empty($this->src)) {
            return $this->getImageSrc();
        }

        if (empty($this->width) || empty($this->height)) {
            return $this->getImageSrc();
        }

        $width = ArrayHelper::getValue($options, 'width');
        $height = ArrayHelper::getValue($options, 'height');

        if ($width === null && $height === null) {
            return $this->getImageSrc();
        }

        if ($width === null) {
            $width = (int) round($height * ($this->width / $this->height));
        }

        if ($height === null) {
            $height = (int) round($width * ($this->height / $this->width));
        }

        //-- no point generate cache for larger image
        if ($width >= $this->width || $height >= $this->height) {
            return $this->getImageSrc();
        }

        $key = sprintf('%sx%s', $width, $height);
        $cacheIndex = $this->getCacheIndex();
        if (($cacheImage = ArrayHelper::getValue($cacheIndex, $key)) !== null && is_array($cacheImage)) {
            $options = [
                'hash' => $this->getCdnHash(),
                'sign' => $this->getIsPrivate(),
            ];

            $source = ArrayHelper::getValue($cacheImage, 'src', $this->src);
            return ImageUploader::resolveFileSource($source, $options);
        }

        $this->generateCache($width, $height);
        return $this->getImageSrc();
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
     * @return array|mixed
     */
    public function getCacheIndex()
    {
        return $this->getOrSetRuntimeData(__METHOD__, function () {
            if (!empty($this->cacheIndex)) {
                if (is_array($this->cacheIndex)) {
                    return $this->cacheIndex;
                }

                if (is_string($this->cacheIndex) && Json::validate($this->cacheIndex)) {
                    return Json::decode($this->cacheIndex);
                }
            }

            return [];
        });
    }

    /**
     * @return bool
     */
    public function rebuildCacheIndex()
    {
        $index = [];

        /** @var ImageCache $cache */
        foreach ($this->getImageCaches()->ready()->each(100) as $cache) {
            if ($cache->getIsReady()) {
                $key = sprintf('%sx%s', $cache->width, $cache->height);
                $index[$key] = $cache->toArray(['id', 'src', 'height', 'width', 'format']);
            }
        }

        $this->cacheIndex = $index;
        return $this->save(false, ['cacheIndex']);
    }

    /**
     * @return bool
     * @throws \yii\db\Exception
     */
    public function purgeCaches()
    {
        $count = $this->getImageCaches()->count();
        if ($count > 0) {
            //- Update all existing cache to be purging, and randomize to prevent being indexed
            static::getDb()->createCommand()
                ->update(ImageCache::tableName(), ['status' => ImageCacheStatus::PURGING], ['imageId' => $this->id])
                ->execute();
        }

        //TODO: task of purge images
        //Yii::$app->pipeline->push(new PurgeImage());
        return true;
    }

    /**
     * @param int $width
     * @param int $height
     * @return bool
     */
    protected function generateCache($width, $height)
    {
        return Yii::$app->pipeline->push(new CacheImage(['id' => $this->id, 'width' => $width, 'height' => $height]));
    }

    /**
     * @return BaseCallToAction|UrlCallToAction|null
     */
    public function getCallToActionModel()
    {
        if ($this->code === ImageCode::HOME_POPUP) {
            return new UrlCallToAction(['params' => $this->callToAction]);
        }

        return null;
    }

    /**
     * @param array $data
     * @return bool
     */
    public function updateCallToAction($data = [])
    {
        $model = $this->getCallToActionModel();
        if (empty($data)) {
            $data = Yii::$app->request->post();
        }

        if ($model->load($data) && $model->validate()) {
            $this->callToAction = $model->toArray();
            return $this->save(false, ['callToAction']);
        }

        return false;
    }

    /**
     * @param $options       array
     * @param $runValidation boolean
     * @return boolean
     * @throws \yii\base\Exception
     * @throws \yii\db\Exception
     */
    public function upload($options = [], $runValidation = false)
    {
        if (!$this->validate()) {
            return false;
        }

        if ($this->getIsNewRecord()) {
            $this->id = UuidHelper::uuid();
        }

        $key =  $this->id;
        $uploader = new ImageUploader();
        $uploader->path('image/original')->id($key);

        if ($this->upload instanceof UploadedFile) {
            $uploader->file($this->upload);
            $uploader->mime($this->upload->type);
        } elseif (($content = ArrayHelper::getValue($options, 'content')) !== null) {
            if (($mime = ArrayHelper::getValue($options, 'mime')) !== null && $mime === 'text/url') {
                ArrayHelper::remove($options, 'mime');

                $url = $content;
                //-- remove anything after "?", else pathinfo will return wrong extension
                if (($pos = strpos($url, '?')) !== false) {
                    $url = substr($url, 0, $pos);
                }

                $info = pathinfo($url);
                if (isset($info['extension'])) {
                    $uploader->extension($info['extension']);
                }
                $uploader->url($content);
            } else {
                $uploader->base64($content);
            }
        }

        if (!$uploader->getHasFile()) {
            Yii::debug('No File uploaded !');
            return false;
        }

        $width = ArrayHelper::remove($options, 'width');
        $height = ArrayHelper::remove($options, 'height');

        if (isset($width) && isset($height)) {
            $uploader->resize($width, $height);
        }

        if (($mime = ArrayHelper::getValue($options, 'mime')) !== null) {
            $uploader->mime($mime);
        }

        if ($this->getIsPrivate()) {
            $uploader->makePrivate();
        }

        if ($uploader->save()) {
            $this->extension = $uploader->getExtension();
            $this->src = $uploader->getFileSource();
            $this->width = $uploader->getImageWidth();
            $this->height = $uploader->getImageHeight();
            $this->format = $uploader->getImageFormat();
            $this->size = $uploader->getFileSize();
            $this->cacheIndex = null;

            return $this->save($runValidation) && $this->purgeCaches();
        }

        return false;
    }

    /**
     * {@inheritdoc}
     * @return static
     */
    public static function findOneByCode($code)
    {
        return static::find()->code($code)->limit(1)->one();
    }

    /**
     * {@inheritdoc}
     * @return Image[]
     */
    public static function findAllByCode($code)
    {
        return static::find()->code($code)->orderByDefault()->all();
    }

    /**
     * @return \yii\db\ActiveQuery|ImageQuery
     */
    public function getParent()
    {
        return $this->hasOne(Image::class, ['id' => 'parentId']);
    }

    /**
     * @return \yii\db\ActiveQuery|ImageQuery
     */
    public function getClone()
    {
        return $this->hasOne(Image::class, ['id' => 'cloneId']);
    }

    /**
     * @return \yii\db\ActiveQuery|ImageCacheQuery
     */
    public function getImageCaches()
    {
        return $this->hasMany(ImageCache::class, ['imageId' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery|ImageQuery
     */
    public function getImageVariants()
    {
        return $this->hasMany(Image::class, ['parentId' => 'id']);
    }

    /**
     * @param \yii\db\ActiveRecord $model
     */
    public function setOwnerModel(yii\db\ActiveRecord $model)
    {
        $this->ownerType = $model->tableName();
        $this->ownerKey = implode(',', $model->getPrimaryKey(true));
    }

    /**
     * @return bool
     */
    protected function getIsUploadProcessRequired()
    {
        return in_array($this->scenario, ['upload', 'backend', 'optional']);
    }

    /**
     * @return bool
     */
    public function beforeValidate()
    {
        if ($this->getIsUploadProcessRequired()) {
            $this->upload = UploadedFile::getInstance($this, 'upload');
        }

        return parent::beforeValidate();
    }

    /**
     * @param mixed $value
     * @return Image
     * @throws \ReflectionException
     */
    public static function factory($value)
    {
        $model = new static();
        if ($value instanceof yii\db\ActiveRecord) {
            $model->setOwnerModel($value);

            $reflect = new ReflectionClass($value);
            $primaryKey = implode(', ', $value->getPrimaryKey(true));
            $model->name = sprintf('Image of %s [%s]', $reflect->getShortName(), $primaryKey);
        }

        return $model;
    }

    /**
     * {@inheritdoc}
     * @return ImageQuery|\yii\db\ActiveQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new ImageQuery(get_called_class());
    }
}
