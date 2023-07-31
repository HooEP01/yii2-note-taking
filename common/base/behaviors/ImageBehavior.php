<?php
/**
 * @copyright Copyright (c) Hustle Hero
 */

namespace common\base\behaviors;

use common\models\Country;
use common\models\Image;
use common\models\ImageQuery;
use yii\base\Behavior;
use yii\db\ActiveRecord;
use Yii;

/**
 * Class ImageBehavior
 * @property Image $image
 * @property ActiveRecord|Country $owner
 * @package common\base\behaviors
 */
class ImageBehavior extends Behavior
{
    /**
     * @var string
     */
    public $imageAttribute = 'imageId';
    /**
     * @var string
     */
    public $defaultImageCode = 'default-any';
    /**
     * @var string
     */
    public $fallbackImageSrc = 'default/no-image.png';
    /**
     * @var string
     */
    public $imageClass = 'common\models\Image';

    /**
     * @return ImageQuery|\yii\db\ActiveQuery
     */
    public function getImages()
    {
        /** @var ImageQuery $query */
        $query = call_user_func([$this->imageClass, 'find']);
        $query->alias('i')->owner($this->owner);
        $query->active()->orderByDefault();
        $query->with(['clone']);

        $query->multiple = true;

        return $query;
    }

    /**
     * @param Image|null $value
     */
    public function setImage($value)
    {
        if ($value === null) {
            $this->owner->setAttribute($this->imageAttribute, null);
            return;
        }

        if (!$value->getIsNewRecord()) {
            $this->owner->setAttribute($this->imageAttribute, $value->id);
        } elseif ($value->save()) {
            $this->owner->setAttribute($this->imageAttribute, $value->id);
        }
    }

    /**
     * @return ImageQuery|\yii\db\ActiveQuery
     */
    public function getImage()
    {
        /** @var ImageQuery $query */
        $query = $this->owner->hasOne($this->imageClass, ['id' => $this->imageAttribute]);
        $query->with(['clone'])->active();
        return $query;
    }

    /**
     * @return Image|null
     */
    public function getImageOrDefault()
    {
        if ($this->owner->image && $this->owner->image->getHasImage()) {
            return $this->owner->image;
        }

        /** @var Image $image */
        $image = call_user_func([$this->imageClass, 'findOneByCode'], $this->defaultImageCode);
        if ($image !== null && $image->getHasImage()) {
            return $image;
        }

        $image = Yii::createObject(['class' => $this->imageClass, 'id' => 'invalid-uuid']);
        $image->src = $this->fallbackImageSrc;

        return $image;
    }

    /**
     * @return Image
     */
    public function getImageModel()
    {
        $image = $this->getImage()->one();
        if ($image === null) {
            $image = Image::factory($this->owner);
            if ($image->save()) {
                $this->setImage($image);
                $this->owner->save();
            }
        }

        return $image;
    }

    /**
     * @return bool
     */
    public function resetImage()
    {
        $image = $this->getImages()->limit(1)->one();
        $this->setImage($image);
        return $this->owner->save(false, [$this->imageAttribute]);
    }
}
