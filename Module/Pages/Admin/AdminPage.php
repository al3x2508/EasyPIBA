<?php
namespace Module\Pages\Admin;
use Module\Media\Admin\Media;

class AdminPage extends \Controller\AdminPage {
    public function output($filename) {
        switch($filename) {
            case 'pages':
                return Pages::output();
            case 'menu':
                return new Menu();
            case 'media':
                return new Media();
            default:
                break;
        }
        return false;
    }
}