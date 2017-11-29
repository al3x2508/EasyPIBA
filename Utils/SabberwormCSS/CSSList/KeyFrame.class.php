<?php

namespace Utils\SabberwormCSS\CSSList;

use Utils\SabberwormCSS\Property\AtRule;

class KeyFrame extends CSSList implements AtRule {

	private $vendorKeyFrame;
	private $animationName;

	public function __construct($iLineNo = 0) {
		parent::__construct($iLineNo);
		$this->vendorKeyFrame = null;
		$this->animationName  = null;
	}

	public function setVendorKeyFrame($vendorKeyFrame) {
		$this->vendorKeyFrame = $vendorKeyFrame;
	}

	public function getVendorKeyFrame() {
		return $this->vendorKeyFrame;
	}

	public function setAnimationName($animationName) {
		$this->animationName = $animationName;
	}

	public function getAnimationName() {
		return $this->animationName;
	}

	public function __toString() {
		return $this->render(new \Utils\SabberwormCSS\OutputFormat());
	}

	public function render(\Utils\SabberwormCSS\OutputFormat $oOutputFormat) {
		$sResult = "@{$this->vendorKeyFrame} {$this->animationName}{$oOutputFormat->spaceBeforeOpeningBrace()}{";
		$sResult .= parent::render($oOutputFormat);
		$sResult .= '}';
		return $sResult;
	}

	public function isRootList() {
		return false;
	}

	public function atRuleName() {
		return $this->vendorKeyFrame;
	}

	public function atRuleArgs() {
		return $this->animationName;
	}
}
