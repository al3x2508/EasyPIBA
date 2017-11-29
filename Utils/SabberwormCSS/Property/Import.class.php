<?php

namespace Utils\SabberwormCSS\Property;

use Utils\SabberwormCSS\Value\URL;

/**
* Class representing an @import rule.
*/
class Import implements AtRule {
	private $oLocation;
	private $sMediaQuery;
	protected $iLineNo;
	protected $aComments;
	
	public function __construct(URL $oLocation, $sMediaQuery, $iLineNo = 0) {
		$this->oLocation = $oLocation;
		$this->sMediaQuery = $sMediaQuery;
		$this->iLineNo = $iLineNo;
		$this->aComments = array();
	}

	/**
	 * @return int
	 */
	public function getLineNo() {
		return $this->iLineNo;
	}

	public function setLocation($oLocation) {
			$this->oLocation = $oLocation;
	}

	public function getLocation() {
			return $this->oLocation;
	}
	
	public function __toString() {
		return $this->render(new \Utils\SabberwormCSS\OutputFormat());
	}

	public function render(\Utils\SabberwormCSS\OutputFormat $oOutputFormat) {
		return "@import ".$this->oLocation->render($oOutputFormat).($this->sMediaQuery === null ? '' : ' '.$this->sMediaQuery).';';
	}

	public function atRuleName() {
		return 'import';
	}

	public function atRuleArgs() {
		$aResult = array($this->oLocation);
		if($this->sMediaQuery) {
			array_push($aResult, $this->sMediaQuery);
		}
		return $aResult;
	}

	public function addComments(array $aComments) {
		$this->aComments = array_merge($this->aComments, $aComments);
	}

	public function getComments() {
		return $this->aComments;
	}

	public function setComments(array $aComments) {
		$this->aComments = $aComments;
	}
}