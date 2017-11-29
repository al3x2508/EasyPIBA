<?php

namespace Utils\SabberwormCSS\Comment;

use Utils\SabberwormCSS\Renderable;

class Comment implements Renderable {
	protected $iLineNo;
	protected $sComment;

	public function __construct($sComment = '', $iLineNo = 0) {
		$this->sComment = $sComment;
		$this->iLineNo = $iLineNo;
	}

	/**
	 * @return string
	 */
	public function getComment() {
		return $this->sComment;
	}

	/**
	 * @return int
	 */
	public function getLineNo() {
		return $this->iLineNo;
	}

	/**
	 * @return string
	 */
	public function setComment($sComment) {
		$this->sComment = $sComment;
	}

	/**
	 * @return string
	 */
	public function __toString() {
		return $this->render(new \Utils\SabberwormCSS\OutputFormat());
	}

	/**
	 * @return string
	 */
	public function render(\Utils\SabberwormCSS\OutputFormat $oOutputFormat) {
		return '/*' . $this->sComment . '*/';
	}

}
