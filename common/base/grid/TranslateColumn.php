<?php
/**
 * @copyright Copyright (c) Hustle Hero
 */

namespace common\base\grid;

use common\base\behaviors\TranslationBehavior;
use common\base\enum\BehaviorCode;
use common\base\enum\LanguageCode;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use Yii;

/**
 * Class TranslateColumn
 * @package backend\base\grid
 */
class TranslateColumn extends DataColumn
{
    /**
     * @var string
     */
    public $format = 'raw';

    /**
     * Returns the data cell value.
     * @param ActiveRecord $model the data model
     * @param mixed $key          the key associated with the data model
     * @param int $index          the zero-based index of the data model among the models array returned by
     *                            [[GridView::dataProvider]].
     * @return string the data cell value
     * @throws \Exception
     */
    public function getDataCellValue($model, $key, $index)
    {
        $behavior = $model->getBehavior(BehaviorCode::TRANSLATION);
        if ($behavior instanceof TranslationBehavior) {
            $values = [];

            $original = $behavior->language;
            foreach (LanguageCode::getSupported() as $code) {
                $behavior->changeLanguage($code);

                $value = ArrayHelper::getValue($model, $this->attribute);
                if (empty($value)) {
                    $value = Yii::$app->formatter->nullDisplay;
                }

                $values[] = Html::tag('strong', $behavior->getLanguageName())  . ': ' . $value;
            }
            $behavior->changeLanguage($original);

            return implode('<br />', $values);
        }

        return parent::getDataCellValue($model, $key, $index);
    }
}
