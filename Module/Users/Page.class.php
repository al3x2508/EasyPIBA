<?php
namespace Module\Users;

class Page {
	public $url = '';
	public function __construct($url) {
		$this->url = $url;
	}
	public function isOwnUrl() {
		if(strpos($this->url, 'login') === 0) return true;
		elseif(strpos($this->url, 'email_confirm') === 0) return true;
		elseif(strpos($this->url, 'my-account') === 0) return true;
		elseif(strpos($this->url, 'password_reset') === 0) return true;
		return false;
	}
	public function getMenu() {
		return false;
	}
	public function output() {
		if(strpos($this->url, 'login') === 0) return new LoginPage();
		elseif(strpos($this->url, 'email_confirm') === 0) return new EmailConfirmPage();
		elseif(strpos($this->url, 'my-account') === 0) return new MyAccountPage();
		elseif(strpos($this->url, 'password_reset') === 0) return new PasswordResetPage();
		return new \stdClass();
	}
}