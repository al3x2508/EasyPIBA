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
            'url' => 'noutati',
            'type' => 0,
            'mustBeLoggedIn' => 0,
            'menu_position' => 1,
            'menu_text' => 'Noutăți',
            'submenu_text' => '',
            'menu_parent' => 'despre-platforma.html',
            'menu_order' => 4
        ));
        $this->registerFrontendUrl(array('url' => '^noutati\/pag\-[0-9+]\/?$', 'type' => 1, 'menu_position' => 0));
        $this->registerBackendUrl(array(
            'permission' => 'Edit news',
            'url' => 'news',
            'menu_text' => 'News',
            'menu_class' => 'newspaper'
        ));

        $news = new Model('news');
        $news = $news->get();
        foreach ($news AS $n) {
            $this->registerFrontendUrl(array(
                'url' => 'noutati/' . Util::getUrlFromString($n->title) . '.html',
                'type' => 0,
                'mustBeLoggedIn' => 0,
                'menu_position' => 0
            ));
        }
        return true;
    }
}