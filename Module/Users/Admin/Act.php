<?php

namespace Module\Users\Admin;

use Controller\AdminAct;
use Controller\EntityException;
use Exception;
use Model\Model;
use Utils\Bcrypt;

class Act extends AdminAct
{
    public array $ignoredFields = array('confirmPassword');

    /**
     * @throws Exception
     */
    public function __construct($id = false)
    {
        $this->permission = 'Edit users';
        $this->entity = new Model('users');
        $bcrypt = new Bcrypt(10);

        switch (strtolower($_SERVER['REQUEST_METHOD'])) {
            //Create use
            case 'post':
                $this->checkPasswordsMatch(
                    $_POST['password'] ?? '',
                    $_POST['confirmPassword'] ?? ''
                );
                parent::__construct($id);
                $this->fields['password'] = $bcrypt->hash($_POST['password']);
                $this->response = $this->create();
                break;
            //Update user
            case 'patch':
                parse_str(file_get_contents('php://input'), $_PATCH);
                parent::__construct($id);
                if (!empty($_PATCH['password'])) {
                    $this->checkPasswordsMatch(
                        $_PATCH['password'],
                        $_PATCH['confirmPassword'] ?? '',
                        false
                    );
                    $this->fields['password']
                        = $bcrypt->hash($_POST['password']);
                } else {
                    unset($this->fields['password']);
                }
                try {
                    $this->response = $this->update();
                } catch (EntityException $e) {
                    self::throwError(400, $e->getMessage());
                }
                break;
            //Delete user
            case 'delete':
                parent::__construct($id);
                try {
                    $this->response = $this->delete();
                } catch (EntityException $e) {
                    self::throwError(400, $e->getMessage());
                }
                break;
            default:
                break;
        }
    }

    public static function checkValidPassword($password)
    {
        if (empty($password) || strlen($password) < 5) {
            self::throwError(
                400,
                __("You need to set a password")
            );

            exit;
        }
    }

    public static function checkPasswordsMatch(
        $password,
        $confirmedPassword,
        $checkValidPassword = true
    ) {
        if ($checkValidPassword) {
            self::checkValidPassword($password);
        }
        if ($password !== $confirmedPassword) {
            self::throwError(
                400,
                __("Passwords don't match")
            );

            exit;
        }
    }
}
