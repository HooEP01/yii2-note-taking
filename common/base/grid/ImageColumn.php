<?php
/**
 * @copyright Copyright (c) Hustle Hero
 */

namespace common\base\grid;

use common\base\behaviors\ImageBehavior;
use common\base\helpers\ArrayHelper;
use common\models\Image;
use yii\db\ActiveRecord;
use yii\helpers\Html;
use Yii;

/**
 * Class ImageColumn
 * @package common\base\grid
 */
class ImageColumn extends DataColumn
{
    /**
     * @var string
     */
    public $format = 'raw';
    /**
     * @var string|int
     */
    public $width = 100;
    /**
     * @var string|array|bool
     */
    public $updateRoute = ['image'];

    /**
     * init
     */
    public function init()
    {
        parent::init();

        if (!isset($this->label)) {
            $this->label = 'Image';
        }

        Html::addCssStyle($this->headerOptions, ['width' => (int) $this->width . 'px']);
        Html::addCssClass($this->headerOptions, 'text-center');
        Html::addCssClass($this->contentOptions, 'grid-image-column');
    }

    /**
     * Returns the data cell value.
     * @param ActiveRecord $model the data model
     * @param mixed $key the key associated with the data model
     * @param int $index the zero-based index of the data model among the models array returned by [[GridView::dataProvider]].
     * @return string the data cell value
     */
    public function getDataCellValue($model, $key, $index)
    {
        if (($b = $model->getBehavior('image')) instanceof ImageBehavior) {
            $html = Yii::$app->formatter->nullDisplay;

            /** @var Image $image */
            $image = ArrayHelper::getValue($model, 'image');
            if ($image && $image->getHasImage()) {
                $options = ['style' => 'height: auto'];
                Html::addCssStyle($options, ['width' => (int) $this->width . 'px']);
                $html = Html::img($image->getCacheImageSrc(['width' => (int) $this->width]), $options);
            }

            if ($this->updateRoute !== false) {
                $html .= '<hr />';
                if (is_array($this->updateRoute)) {
                    $this->updateRoute['id'] = $key;
                }

                $html .= Html::a('Change', $this->updateRoute, ['class' => 'btn-update-image']);
            }

            return $html;
        }

        return parent::getDataCellValue($model, $key, $index);
    }
}
