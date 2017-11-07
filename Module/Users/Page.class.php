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
		if(!array_key_exists('user', $_SESSION)) {
			$login_page = 'login.html';
			$menu = array(array('url' => $login_page, 'menu_text' => __('Login'), 'submenu_text' => '', 'menu_parent' => 0));
		}
		else {
			$myaccountPage = 'my-account.html';
			$menu = array(
				array('id' => 'myaccount', 'url' => $myaccountPage, 'menu_text' => __('My account'), 'submenu_text' => __('My account'), 'menu_parent' => 0),
				array('url' => 'logout', 'menu_text' => __('Logout'), 'submenu_text' => __('Logout'), 'menu_parent' => 'myaccount')
			);
		}
		return array('menu_right' => $menu);
	}
	public function output() {
		if(strpos($this->url, 'login') === 0) return new LoginPage();
		elseif(strpos($this->url, 'email_confirm') === 0) return new EmailConfirmPage();
		elseif(strpos($this->url, 'my-account') === 0) return new MyAccountPage();
		elseif(strpos($this->url, 'password_reset') === 0) return new PasswordResetPage();
		return new \stdClass();
	}
}