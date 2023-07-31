<?php
/**
 * @copyright Copyright (c) Hustle Hero
 * @var $content string
 * @var $this View
 */

use backend\assets\AppAsset;
use backend\base\web\View;
use common\widgets\Notification;
use yii\bootstrap4\Breadcrumbs;
use yii\helpers\Html;

AppAsset::register($this);
$this->registerIcons();
$this->registerLinkTag(['rel' => 'preconnect', 'href' => 'https://fonts.gstatic.com']);
$this->registerCssFile('https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css');
$this->registerCssFile('https://cdnjs.cloudflare.com/ajax/libs/flag-icon-css/3.3.0/css/flag-icon.min.css');
$this->registerCssFile('https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700');
$this->registerCssFile('https://fonts.googleapis.com/css2?family=Nunito:ital,wght@0,200;0,300;0,400;0,600;0,700;0,800;1,200;1,300;1,400;1,600;1,700&display=swap');
$this->registerCssFile('https://fonts.googleapis.com/css2?family=Source+Code+Pro:ital,wght@0,200;0,300;0,400;0,500;0,600;0,700;0,900;1,200;1,300;1,400;1,500;1,600;1,700;1,900&display=swap');

?>

<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php $this->registerCsrfMetaTags() ?>
    <title>Hustle Hero - <?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body class="hold-transition sidebar-mini layout-footer-fixed">
<?php $this->beginBody() ?>
    <div class="wrapper">
        <div class="content-wrapper-pop">
            <!-- Content Header (Page header) -->
            <section class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1>
                                <?= $this->title ?>
                                <?= isset($this->params['subtitle']) ? Html::tag('span', $this->params['subtitle'], ['style' => 'font-size: 60%; font-weight: 400']) : '' ?>
                            </h1>
                        </div>
                        <div class="col-sm-6">
                            <?= Breadcrumbs::widget([
                                'options' => [
                                    'class' => 'float-sm-right',
                                ],
                                'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
                            ]) ?>
                        </div>
                    </div>
                </div><!-- /.container-fluid -->
            </section>
            <section class="content">
                <div class="container-fluid">
                    <?= Notification::widget() ?>
                    <?= $content ?>
                </div>
            </section>
        </div>
    </div>
<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
