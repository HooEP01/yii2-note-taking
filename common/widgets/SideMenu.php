<?php

/**
 * @copyright Copyright (c) Hustle Hero
 */

namespace common\widgets;

use common\base\enum\CacheTag;
use common\models\User;
use yii\base\Widget;
use yii\caching\Cache;
use yii\caching\TagDependency;
use yii\di\Instance;
use yii\helpers\Html;
use Yii;

/**
 * Class SideMenu
 * @package backend\base\widgets
 */
class SideMenu extends Widget
{
    public static $route = null;

    /**
     * @var string
     */
    public $menuSeparator = '<li role="separator" class="divider"></li>';
    /**
     * @var string|array|Cache
     */
    public $cache = 'cache';
    /**
     * @var string|array|Cache
     */
    public $cacheDuration = 3600;
    /**
     * @var User
     */
    public $user;

    /**
     * @param bool $autoGenerate
     * @return string
     */
    public function getId($autoGenerate = true)
    {
        return 'side_menu_' . parent::getId($autoGenerate);
    }

    /**
     * @throws \yii\base\InvalidConfigException
     */
    public function init()
    {
        parent::init();
        $this->cache = Instance::ensure($this->cache, Cache::class);
    }

    /**
     * @return string|void
     * @throws \Exception
     */
    public function run()
    {
        echo TreeView::widget([
            'options' => ['class' => 'nav nav-pills nav-sidebar flex-column'],
            'items' => $this->getItems(),
            'route' => static::$route,
        ]);
    }

    /**
     * @return mixed
     */
    protected function getItems()
    {
        if (Yii::$app->user->isGuest || !isset($this->user)) {
            return [
                ['label' => Yii::t('menu', 'site.login'), 'url' => ['/site/login']],
            ];
        }

        $tagDependency = new TagDependency(['tags' => CacheTag::BACKEND_SIDE_MENU]);
        $cacheKey = 'backend-side-menu-v2-' . $this->user->id . Yii::$app->language;
        $menuItems = $this->cache->get($cacheKey);
        if ($menuItems === false) {
            $menuItems = [];


            $this->appendMenuItem($menuItems, $this->getDashboardMenu());
            $this->appendMenuItem($menuItems, $this->getGeneralMenu());
            $this->appendMenuItem($menuItems, $this->getContentManagementMenu());

            $menuItems[] = '<li class="nav-header">MISCELLANEOUS</li>';
            //$this->appendMenuItem($menuItems, $this->getWebsiteMenu());
            $this->appendMenuItem($menuItems, $this->getSystemMenu());

            $this->cache->set($cacheKey, $menuItems, $this->cacheDuration, $tagDependency);
        }

        return $menuItems;
    }

    /**
     * @return array
     */
    protected function getDashboardMenu()
    {
        $menu = [
            'icon' => '<i class="nav-icon fas fa-tachometer-alt"></i>',
            'label' => 'Dashboard',
            'encode' => false,
            'url' => ['/site/index'],
            'active' => Yii::$app->controller->route === 'site/index'
        ];

        return $menu;
    }

    /**
     * @return array
     */
    protected function getGeneralMenu()
    {
        $menu = [
            'icon' => '<i class="nav-icon fas fa-globe-asia"></i>',
            'label' => 'General Setup',
            'encode' => false,
            'url' => 'javascript:void(0)',
            'items' => []
        ];

        $submenu = [];
        $submenu[] = $this->createMenu(Yii::t('menu', 'general.user.list'), ['/user/list']);
        $this->appendSubMenu($menu['items'], $submenu);

        return $menu;
    }


    /**
     * @return array
     */
    protected function getContentManagementMenu()
    {
        $menu = [
            'icon' => '<i class="nav-icon far fa-file-alt"></i>',
            'label' => 'Content Management',
            'encode' => false,
            'url' => 'javascript:void(0)',
            'items' => []
        ];

        $submenu = [];
        $submenu[] = $this->createMenu(Yii::t('menu', 'content-management.folder.list'), ['/folder']);
        $submenu[] = $this->createMenu(Yii::t('menu', 'content-management.note.list'), ['/note']);
        $submenu[] = $this->createMenu(Yii::t('menu', 'content-management.faq.list'), ['/faq/list']);
        $submenu[] = $this->createMenu(Yii::t('menu', 'content-management.page.list'), ['/page/list']);
        $this->appendSubMenu($menu['items'], $submenu);

        return $menu;
    }

    /**
     * @return array
     */
    protected function getWebsiteMenu()
    {
        $menu = [
            'icon' => '<i class="nav-icon fas fa-cogs"></i>',
            'label' => 'Website Setup',
            'encode' => false,
            'url' => 'javascript:void(0)',
            'items' => []
        ];

        $submenu = [];
        $submenu[] = $this->createMenu(Yii::t('menu', 'website.home_banner.list'), ['/home-banner/list']);
        $submenu[] = $this->createMenu(Yii::t('menu', 'website.faq.list'), ['/faq/list']);
        $submenu[] = $this->createMenu(Yii::t('menu', 'website.page.list'), ['/page/list']);

        $this->appendSubMenu($menu['items'], $submenu);

        return $menu;
    }

    /**
     * @return array
     */
    protected function getSystemMenu()
    {
        $menu = [
            'icon' => '<i class="nav-icon fas fa-cogs"></i>',
            'label' => 'System Setup',
            'encode' => false,
            'url' => 'javascript:void(0)',
            'items' => []
        ];

        $submenu = [];
        $submenu[] = $this->createMenu(Yii::t('menu', 'system.cache.flush'), ['/site/flush-cache']);
        $submenu[] = $this->createMenu(Yii::t('menu', 'system.setting.index'), ['/setup/index']);
        if ($this->user->getIsSystemAdmin()) {
            $submenu[] = $this->createMenu(Yii::t('menu', 'system.audit.trail'), ['/audit/trail']);
        }

        $this->appendSubMenu($menu['items'], $submenu);

        return $menu;
    }

    /**
     * @param int $count
     * @param string $type
     * @return string
     */
    protected function createBadge($count = 0, $type = 'info')
    {
        if ($count <= 0) {
            return '';
        }

        $classes = ['badge', 'badge-' . $type, 'right'];
        return Html::tag('span', number_format($count), ['class' => implode(' ', $classes)]);
    }

    /**
     * @param string $label
     * @param string|array $url
     * @param array $items
     * @return array
     */
    protected function createMenu($label, $url, $items = [])
    {
        return [
            'label' => $label,
            'encode' => false,
            'url' => $url,
            'items' => $items,
        ];
    }

    /**
     * @param array $menu
     * @param array $item
     */
    protected function appendMenuItem(&$menu, $item)
    {
        if (empty($item)) {
            return;
        }

        if (isset($item['items']) && empty($item['items'])) {
            return;
        }

        $menu[] = $item;
    }

    /**
     * @param array $menu
     * @param array $submenu
     */
    protected function appendSubMenu(&$menu, $submenu)
    {
        if (empty($submenu)) {
            return;
        }

        if (!empty($menu)) {
            $menu[] = $this->menuSeparator;
        }
        $menu = array_merge($menu, $submenu);
    }
}
