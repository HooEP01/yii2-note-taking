<?php
/**
 * @copyright Copyright (c) Hustle Hero
 */

namespace common\base\grid;

use yii\helpers\Html;

/**
 * Class GridView
 * @package common\base\grid
 */
class GridView extends \yii\grid\GridView
{
    public $layout = "{items}\n{pager}";

    /**
     * @var array default table options
     */
    public $tableOptions = ['class' => 'table table-bordered table-responsive-sm text-nowrap mb-0'];

    /**
     * @var array
     */
    public $pager = [
        'class' => 'yii\widgets\LinkPager',
        'options' => ['class' => 'pagination pagination-sm m-0 float-right'],
        'linkContainerOptions' => ['class' => 'page-item'],
        'linkOptions' => ['class' => 'page-link'],
        'disabledListItemSubTagOptions' => ['class' => 'page-link']
    ];

    /**
     * @var string the data column class
     */
    public $dataColumnClass = DataColumn::class;

    /**
     * Renders the pager.
     * @return string the rendering result
     */
    public function renderPager()
    {
        $pager = parent::renderPager();
        $html = Html::beginTag('div', ['class' => 'card-footer clearfix']);
        $html .= Html::beginTag('div', ['class' => 'row']);
        $html .= Html::tag('div', $this->renderSummary(), ['class' => 'col-md']);
        $html .= Html::tag('div', $pager, ['class' => 'col-md']);
        $html .= Html::endTag('div');

        return $html;
    }
}