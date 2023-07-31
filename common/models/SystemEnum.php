<?php
/**
* @copyright Copyright (c) Hustle Hero
*/

namespace common\models;

use common\base\db\ActiveRecord;
use common\base\enum\BehaviorCode;
use common\base\enum\SystemEnumType;
use common\base\helpers\ArrayHelper;
use Yii;
use yii\helpers\Html;

/**
 * This is the model class for table "{{%system_enum}}".
 *
 * @property string $id
 * @property string $type
 * @property string $code
 * @property string $name
 * @property string|null $description
 * @property string|null $remark
 * @property string|null $imageId
 * @property string|null $parentId
 * @property string|null $configuration
 * @property string|null $cacheTranslation
 * @property int|null $position sorting, or ordering purpose
 * @property string $createdAt
 * @property string $updatedBy
 * @property string $updatedAt
 * @property string|null $deletedBy
 * @property string|null $deletedAt
 * @property bool $isActive
 *
 * @property Image|null $image
 * @property Image[] $images
 * @property Image|null $imageOrDefault
 *
 * @method ImageQuery getImage()
 * @method ImageQuery getImages()
 * @method Image getImageModel()
 * @method void setImage(Image $value)
 * @method boolean resetImage()
 */
class SystemEnum extends ActiveRecord
{
    /**
     * @return array
     */
    public function behaviors()
    {
        return [
            BehaviorCode::SLUGGABLE => [
                'class' => 'common\base\behaviors\SluggableBehavior',
                'slugAttribute' => 'code',
                'attribute' => 'name',
                'immutable' => true,
            ],
            BehaviorCode::BLAMEABLE => [
                'class' => 'common\base\behaviors\BlameableBehavior',
            ],
            BehaviorCode::SANITIZE => [
                'class' => 'common\base\behaviors\SanitizeBehavior',
                'stripCleanAttributes' => ['name', 'description', 'remark']
            ],
            BehaviorCode::TRANSLATION => [
                'class' => 'common\base\behaviors\TranslationBehavior',
                'attributes' => ['name', 'description', 'remark'],
            ],
            BehaviorCode::AUDIT => [
                'class' => 'common\base\audit\behaviors\AuditTrailBehavior',
            ],
            BehaviorCode::IMAGE => [
                'class' => 'common\base\behaviors\ImageBehavior',
                'fallbackImageSrc' => 'default/user-avatar.jpg',
                'defaultImageCode' => 'default-image',
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%system_enum}}';
    }

    /**
     * @return array
     */
    public function fields()
    {
        return [
            'id' => 'shortUuid',
            'code',
            'name',
            'image',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['type', 'name'], 'required'],
            [['description', 'remark'], 'string'],
            [['type', 'code'], 'string', 'max' => 128],
            [['name'], 'string', 'max' => 192],
            [['type', 'code'], 'unique', 'targetAttribute' => ['type', 'code']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('model', 'common.id'),
            'configuration' => Yii::t('model', 'common.configuration'),
            'cacheTranslation' => Yii::t('model', 'common.cacheTranslation'),
            'imageId' => Yii::t('model', 'common.imageId'),
            'parentId' => Yii::t('model', 'common.parentId'),
            'code' => Yii::t('model', 'common.code'),
            'name' => Yii::t('model', 'common.name'),
            'description' => Yii::t('model', 'common.description'),
            'type' => Yii::t('model', 'common.type'),
            'remark' => Yii::t('model', 'common.remark'),
            'position' => Yii::t('model', 'common.position'),
            'createdAt' => Yii::t('model', 'common.createdAt'),
            'updatedBy' => Yii::t('model', 'common.updatedBy'),
            'updatedAt' => Yii::t('model', 'common.updatedAt'),
            'deletedBy' => Yii::t('model', 'common.deletedBy'),
            'deletedAt' => Yii::t('model', 'common.deletedAt'),
            'isActive' => Yii::t('model', 'common.isActive'),
        ];
    }

    /**
     * @return array
     */
    public static function amenityOptions()
    {
        return static::options(SystemEnumType::AMENITY, [
            'from' => 'id',
            'withImage' => true,
        ]);
    }

    /**
     * @return array
     */
    public static function buildStatusOptions()
    {
        return static::options(SystemEnumType::BUILD_STATUS);
    }

    /**
     * @param string $type
     * @param array $options
     * @return array
     */
    public static function options($type, $options = [])
    {
        $from = ArrayHelper::getValue($options, ['from'], 'code');
        $withImage = ArrayHelper::getValue($options, ['withImage'], false);

        if ($withImage) {
            $models = static::find()->with(['image'])->type($type)->active()->orderByDefault()->all();
            return ArrayHelper::map($models, $from, function (SystemEnum $m) {
                $image = $m->imageOrDefault;
                return $image ? Html::img($image->getCacheImageSrc(['width' => 64]), ['style' => 'width: 32px; height: 32px;']) . Html::tag('span', $m->name, ['class' => 'ml-2']) : $m->name;
            });
        }

        $models = static::find()->type($type)->active()->orderByDefault()->all();
        return ArrayHelper::map($models, $from, 'name');
    }

    /**
     * {@inheritdoc}
     * @return SystemEnumQuery|\yii\db\ActiveQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new SystemEnumQuery(get_called_class());
    }
}
