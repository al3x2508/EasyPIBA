<?php

namespace Module\Settings\Admin;

use Controller\AdminAct;
use Model\Model;

require_once(dirname(__FILE__, 4) . '/Utils/functions.php');

class Act extends AdminAct
{
    public function __construct()
    {
        $this->permission = 'Edit settings';
        $this->entity = new Model('settings');
        foreach ($_POST as $key => $value) {
            $this->fields[$key] = $value;
        }
        return $this->act(
            'setting = "'
            . $this->entity->escape($this->fields['setting']) . '"'
        );
    }
}