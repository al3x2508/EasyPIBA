<?php
namespace Controller;
use PHPMailer\PHPMailer\PHPMailer;

class Mail extends PHPMailer  {
	public function __construct() {
		$this->isSMTP();
		$this->SMTPDebug = 0;
		$this->Host = $_ENV['MAIL_HOST'];
		$this->Port = $_ENV['MAIL_PORT'];
		$this->SMTPOptions = array('ssl' => array('verify_peer' => false, 'verify_peer_name' => false, 'allow_self_signed' => true));
		$this->SMTPAuth = true;
		$this->Username = $_ENV['MAIL_USER'];
		$this->Password = $_ENV['MAIL_PASS'];
		$this->CharSet = "UTF-8";
//		$this->setFrom($_ENV['MAIL_FROM'], $_ENV['MAIL_NAME']);
		parent::__construct();
	}
}