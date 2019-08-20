<?php
namespace Module\Pages\Admin;
use Model\Model;
use Utils\Util;

class Menu {
	/**
	 * @param $key
	 * @param $value
	 */
	public function __set($key, $value) {
		$this->$key = $value;
	}
	public function __construct() {
		$this->title =  __('Edit menu');
		$this->h1 =  __('Edit menu');
		$this->js = array('../assets/js/jquery.nestedSortable.js','js/jsall.js','Module/Pages/Admin/menu.js');
		$this->css = array('Module/Pages/Admin/menu.css');
		$languages = new Model('languages');
		$languages->order('name ASC');
		$languages = $languages->get();
		$languageOptions = [0 => __('Any')];
		foreach($languages AS $language) {
			$selected = ($language->code == _DEFAULT_LANGUAGE_);
			$languageOptions[$language->code] = [$language->name, $selected];
		}
		$this->content = '<div class="box">
	<div class="box-header">
		<div class="row">
			<div class="col-12 col-md-4">
				' . __('Language') . '<br /><select class="form-control" id="languagef">' . Util::arrayToOptions($languageOptions) . '</select>
			</div>
		</div>
	</div>
	<div class="box-body">
		<div class="container-fluid">
			<div class="row row-eq-height">
				<div class="col-12 col-md-6">
					<ol class="sortable" id="available"></ol>
				</div>
				<div class="col-12 col-md-6">
					<ol class="sortable" id="nestable"></ol>
				</div>
			</div>
		</div>
	</div>
	<div class="box-footer">
		<div class="btn-toolbar">
			<button id="customSave" class="btn btn-outline-primary">' . __('Save') . '</button>
		</div>
	</div>
</div>';
	}
}