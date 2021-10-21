<?php

namespace Controller;

use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;

class Mail extends PHPMailer
{
    public function __construct()
    {
        $this->isSMTP();
        $this->SMTPDebug = 0;
        $this->Host = _MAIL_HOST_;
        $this->Port = _MAIL_PORT_;
        $this->SMTPOptions = array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            )
        );
        $this->SMTPAuth = true;
        $this->Username = _MAIL_USER_;
        $this->Password = _MAIL_PASS_;
        $this->CharSet = "UTF-8";
        try {
            $this->setFrom(_MAIL_FROM_, _MAIL_NAME_);
        } catch (Exception $e) {
            debug($e->getMessage());
        }
        parent::__construct();
    }
}