<?php
namespace Module\Users;

use Utils\Util;

class Page {
	public function output() {
		$currentUrl = Util::getCurrentUrl();
		if(strpos($currentUrl, 'login') === 0) return new LoginPage();
		elseif(strpos($currentUrl, 'email_confirm') === 0) return new EmailConfirmPage();
		elseif(strpos($currentUrl, 'my-account') === 0) return new MyAccountPage();
		elseif(strpos($currentUrl, 'password_reset') === 0) return new PasswordResetPage();
		return new \stdClass();
	}
}