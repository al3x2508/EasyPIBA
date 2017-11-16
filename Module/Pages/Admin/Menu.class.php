<?php
namespace Module\Pages\Admin;
use Model\Model;

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
		$this->js = array('plugins/nestedSortable/jquery.nestedSortable.js','bower_components/select2/dist/js/select2.full.min.js','js/jsall.js','../Module/Pages/Admin/menu.js');
		$this->css = array('bower_components/select2/dist/css/select2.min.css','../Module/Pages/Admin/menu.css');
		$pages = new Model('pages');
		$pages->groupBy('language');
		$pages->order('language ASC');
		$pagesArray = $pages->get();
		$languageOptions = '';
		if(count($pagesArray) > 1) {
			foreach($pagesArray AS $page) {
				$selected = ($page->language == _DEFAULT_LANGUAGE_)?' selected':'';
				$languageOptions .= "<option value=\"{$page->language}\"{$selected}>" . $page->languages->name . "</option>";
			}
		}
		$this->content = '<div class="box">
	<div class="box-body">
		<div class="row">
			<div class="col-lg-4">
				' . __('Language') . '<br /><select class="form-control select2" id="languagef">' . $languageOptions . '</select>
			</div>
		</div>
		<div class="row row-eq-height">
			<div class="col-lg-6">
				<ol class="sortable" id="available"></ol>
			</div>
			<div class="col-lg-6">
				<ol class="sortable" id="nestable"></ol>
			</div>
		</div>
		<div class="clearfix"></div>
	</div>
	<button id="customSave" class="btn btn-primary">' . __('Save') . '</button>
</div>';
	}
}