<?php

namespace Utils\SabberwormCSS;

interface Renderable {
	public function __toString();
	public function render(\Utils\SabberwormCSS\OutputFormat $oOutputFormat);
	public function getLineNo();
}