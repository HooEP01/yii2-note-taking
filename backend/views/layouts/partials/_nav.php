<?php
/**
 * @copyright Copyright (c) Hustle Hero
 * @var $this View
 */

use backend\base\web\View;
use common\base\helpers\Url;
use common\widgets\SideMenu;
use yii\helpers\Html;

$imageBaseUrl = $this->getBaseUrl();
$language = Yii::$app->language;

?>
<!-- Navbar -->
<nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
        <li class="nav-item d-sm-none">
            <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
        </li>
    </ul>

    <ul class="navbar-nav ml-auto">
        <li class="nav-item d-sm-none">
            <img src="<?= $this->getBaseUrl() . '/images/logos/logo_horizontal_default.png' ?>" alt="Hustle Hero Logo" style="max-height: 40px; max-width: 150px;">
        </li>
    </ul>

    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">
        <?php if ($this->user) : ?>
            <li class="nav-item dropdown user-menu">
                <a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown">
                    <?= Html::img($this->user->getAvatarImageSrc(), ['class' => 'user-image img-circle elevation-2', 'alt' => $this->user->getDisplayName(), 'style' => 'margin-top: -7px; margin-right: 0']) ?>
                    <span class="d-none d-md-inline"><?= $this->user->getDisplayName() ?></span>
                </a>
                <ul class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                    <!-- User image -->
                    <li class="user-header bg-secondary">
                        <?= Html::img($this->user->getAvatarImageSrc(), ['class' => 'img-circle elevation-2', 'alt' => $this->user->getDisplayName()]) ?>
                        <p>
                            <?= $this->user->getDisplayName() ?>
                            <small>Member since <?= $this->user->getDateJoined() ?></small>
                        </p>
                    </li>
                    <!-- Menu Footer-->
                    <li class="user-footer">
                        <?= Html::a('My Profile', ['/profile/update'], ['class' => 'btn btn-default btn-flat']) ?>
                        <?= Html::a('Logout', ['/site/logout'], ['class' => 'btn btn-danger btn-flat float-right', 'data-method' => 'post']) ?>
                    </li>
                </ul>
            </li>
        <?php endif ?>
    </ul>
</nav>
<!-- /.navbar -->

<!-- Main Sidebar Container -->
<aside class="main-sidebar sidebar-dark-light elevation-4">
    <!-- Brand Logo -->
    <a href="<?= Url::home() ?>" class="brand-link logo-switch">
        <img src="<?= $this->getBaseUrl() . '/images/logos/logo_square_white.png' ?>" alt="Hustle Hero Logo" class="brand-image-xl logo-xs">
        <img src="<?= $this->getBaseUrl() . '/images/logos/logo_horizontal_white.png' ?>" alt="Hustle Hero Logo" class="brand-image-xs logo-xl">
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <?= SideMenu::widget(['user' => $this->user]) ?>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>