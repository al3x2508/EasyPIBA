<?php
namespace Module\News;

use Model\Model;
use Utils\Util;

class Setup extends \Module\Setup
{
    public function __construct()
    {
        parent::__construct();
        $this->registerFrontendUrl(array(
            'url' => 'news',
            'type' => 0,
            'mustBeLoggedIn' => 0,
            'menu_position' => 1,
            'menu_text' => 'News',
            'submenu_text' => '',
            'menu_parent' => '',
            'menu_order' => 2
        ));
        $this->registerFrontendUrl(array('url' => '^news\/pag\-[0-9+]\/?$', 'type' => 1, 'menu_position' => 0));
        $this->registerBackendUrl(array(
            'permission' => 'Edit news',
            'url' => 'news',
            'menu_text' => 'News',
            'menu_class' => 'fas fa-newspaper'
        ));

        $news = new Model('news');
        $news = $news->get();
        foreach ($news AS $n) {
            $this->registerFrontendUrl(array(
                'url' => Util::getUrlFromString($n->title),
                'type' => 0,
                'mustBeLoggedIn' => 0,
                'menu_position' => 0
            ));
        }
        return true;
    }
}