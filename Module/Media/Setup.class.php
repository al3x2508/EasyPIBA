<?php
namespace Module\Media;

class Setup extends \Module\Setup
{
    public function __construct()
    {
        parent::__construct();
        $this->registerFrontendUrl(array(
            'url' => 'media',
            'menu_text' => 'Media',
            'menu_class' => 'image',
            'menu_position' => 3,
            'menu_order' => 2,
            'mustBeLoggedIn' => true
        ));
        return true;
    }
}