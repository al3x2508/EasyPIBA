<?php

namespace Module\Users\Admin;

use Controller\AdminAct;
use Module\Users\Setup;
use Bcrypt;

require_once(dirname(__FILE__, 4) . '/Utils/Util.php');

class Act extends AdminAct
{
    /**
     * @var string
     */
    public static $PERMISSION = 'Edit users';
    /**
     * @var string
     */
    public static $ENTITY = Setup::ENTITY;
    /**
     * @var Bcrypt
     */
    private $bcrypt;

    /**
     * @param $id
     */
    public function __construct($id)
    {
        try {
            $this->bcrypt = new Bcrypt(10);
        } catch (\Exception $e) {
            $this->sendStatus(false);
        }
        parent::__construct($id);
    }

    public function pre_create_hook()
    {
        if (strlen($this->entity->password) > 5) {
            $this->entity->password = $this->bcrypt->hash($this->entity->password);
        }
    }

    public function pre_update_hook()
    {
        $this->pre_create_hook();
    }
}