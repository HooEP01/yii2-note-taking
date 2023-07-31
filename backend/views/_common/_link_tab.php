<?php
/**
 * @copyright Copyright (c) Hustle Hero
 *
 * @var $this View
 * @var $_params_ array
 */

use backend\base\web\View;
use yii\bootstrap4\ActiveForm;
use yii\bootstrap4\Tabs;
use yii\helpers\Html;

$current = isset($current) ? $current : $this->getRouteName();
$submitButton = isset($submitButton) ? $submitButton : true;

if (isset($items[$current])) {
    $items[$current]['active'] = true;
    $items[$current]['url'] = '#';
}

?>
<?php if ($submitButton) {
    $form = ActiveForm::begin(['id' => 'setup-form']);
} ?>
<div class="row">
    <div class="col-xs-10">
        <div class="tab-content">
            <div class="tab-pane active">
                <?php if (isset($view)) : ?>
                    <?php $params = $submitButton ? array_merge(['form' => $form], $_params_) : $_params_; ?>
                    <?= $this->render($view, $params) ?>
                <?php endif ?>
            </div>
        </div>
    </div>
    <div class="col-xs-2">
        <?= Tabs::widget([
            'navType' => 'nav nav-danger nav-pills nav-stacked',
            'renderTabContent' => false,
            'items' => $items,
        ]); ?>

        <?php if ($submitButton) : ?>
            <hr />

            <button type="submit" class="btn btn-danger btn-flat btn-block">Submit</button>
            <?=  isset($this->cancelUrl) ? Html::a('Cancel', $this->cancelUrl, ['class' => 'btn btn-block']) : '' ?>
        <?php elseif (isset($this->cancelUrl)) : ?>
            <hr />
            <?= Html::a('Cancel', $this->cancelUrl, ['class' => 'btn btn-block']) ?>
        <?php endif ?>


    </div>
</div>

<?php if ($submitButton) {
    ActiveForm::end();
} ?>
