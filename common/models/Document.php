<?php
/**
* @copyright Copyright (c) Hustle Hero
*/

namespace common\models;

use common\base\db\ActiveRecord;
use common\base\enum\BehaviorCode;
use common\base\helpers\ArrayHelper;
use common\base\helpers\DocumentUploader;
use common\base\helpers\FileUploader;
use ReflectionClass;
use Yii;
use yii\web\UploadedFile;

/**
 * This is the model class for table "{{%document}}".
 *
 * @property string $id
 * @property string|null $code
 * @property string $name
 * @property string|null $ownerType Table name of the model or class of owner
 * @property string|null $ownerKey The string representation of pk or id
 * @property string|null $format e.g. image/png
 * @property string|null $extension e.g. pdf, jpg, docx
 * @property int|null $size In Bytes
 * @property string|null $src
 * @property string|null $data For storing, raw json special non-searchable info
 * @property int|null $position sorting, or ordering purpose
 * @property string $createdAt
 * @property string $updatedBy
 * @property string $updatedAt
 * @property bool $isActive
 */
class Document extends ActiveRecord
{
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
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%document}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        $rules = [
            [['name'], 'required', 'enableClientValidation' => false],
            [['size'], 'default', 'value' => null],
            [['size'], 'integer'],
            [['src', 'data'], 'string'],
            [['code', 'ownerType', 'ownerKey', 'format', 'extension'], 'string', 'max' => 128],
            [['name'], 'string', 'max' => 160],
        ];

        $maxSize = Yii::$app->config->getDocumentMaximumSize();
        $rules[] = [
            ['upload'],
            'file',
            'skipOnEmpty' => false,
            'maxSize' => $maxSize,
            'tooBig' => 'The file "{file}" is too big. Its size cannot exceed ' . Yii::$app->formatter->asShortSize($maxSize),
            'on' => ['upload'],
        ];

        $rules[] = [
            ['upload'],
            'file',
            'skipOnEmpty' => true,
            'maxSize' => $maxSize,
            'tooBig' => 'The file "{file}" is too big. Its size cannot exceed ' . Yii::$app->formatter->asShortSize($maxSize),
            'on' => ['optional'],
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
            'ownerType' => Yii::t('model', 'common.ownerType'),
            'ownerKey' => Yii::t('model', 'common.ownerKey'),
            'format' => Yii::t('model', 'common.format'),
            'extension' => Yii::t('model', 'common.extension'),
            'size' => Yii::t('model', 'common.size'),
            'src' => Yii::t('model', 'common.src'),
            'data' => Yii::t('model', 'common.data'),
            'position' => Yii::t('model', 'common.position'),
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
    public function getHasDocument()
    {
        return !empty($this->src);
    }

    /**
     * @return bool
     */
    public function getIsImage()
    {
        return in_array($this->format, ['image/jpeg', 'image/png', 'image/gif']);
    }

    /**
     * @return string
     * @throws \Exception
     */
    public function getDocumentSrc()
    {
        return FileUploader::resolveFileSource($this->src);
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
            foreach (['Payment', 'Merchant'] as $name) {
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
     * @return null|ActiveRecord
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
     * @param array $options
     * @return bool
     * @throws \yii\base\Exception
     * @throws \Exception
     */
    public function upload($options = [])
    {
        if (!$this->validate()) {
            return false;
        }

        $id = (int) $this->id;
        if ($this->getIsNewRecord()) {
            $last = (int) static::find()->select('id')->limit(1)->orderBy(['id' => SORT_DESC])->scalar();
            $id = $last + 1;
        }

        $key =  $id . '-' . mt_rand(111, 999);

        $uploader = new DocumentUploader();
        $uploader->path('document/original')->id($key);

        if ($this->upload instanceof UploadedFile) {
            $uploader->file($this->upload);
        } elseif (($content = ArrayHelper::getValue($options, 'content')) !== null) {
            $uploader->file($content);
        }

        if (($mime = ArrayHelper::getValue($options, 'mime')) !== null) {
            $uploader->mime($mime);
        }

        if ($uploader->save()) {
            $this->extension = $uploader->getExtension();
            $this->format = $uploader->getFormat();
            $this->src = $uploader->getFileSource();
            $this->size = $uploader->getFileSize();

            return $this->save();
        }

        return false;
    }

    /**
     * @return bool
     */
    protected function getIsUploadProcessRequired()
    {
        return in_array($this->scenario, ['upload', 'optional']);
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
     * @return bool
     */
    public function softDelete() :bool
    {
        if (!empty($this->ownerType) && strpos($this->ownerType, '_trash') === false) {
            $this->ownerType = $this->ownerType . '_trash';
        }

        if (!empty($this->code) && strpos($this->ownerType, '_deleted_') === false) {
            $this->code = $this->code . '_deleted_' . $this->id;
        }

        return parent::softDelete();
    }

    /**
     * @param $value
     * @return Document
     * @throws \ReflectionException
     */
    public static function factory($value)
    {
        $model = new static();
        if ($value instanceof ActiveRecord) {
            $model->ownerType = $value->tablename();
            $model->ownerKey = implode(',', $value->getPrimaryKey(true));
        }

        $reflect = new ReflectionClass($value);
        $model->name = sprintf('Document of %s [%s]', $reflect->getShortName(), $model->ownerKey);

        return $model;
    }

    /**
     * {@inheritdoc}
     * @return DocumentQuery|\yii\db\ActiveQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new DocumentQuery(get_called_class());
    }
}
