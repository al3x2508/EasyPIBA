<?php
$page_title = __('Edit menu');
$js = array('plugins/nestedSortable/jquery.nestedSortable.js','bower_components/select2/dist/js/select2.full.min.js','js/jsall.js','js/menu.js');
$css = array('bower_components/select2/dist/css/select2.min.css', 'plugins/nestedSortable/jquery.nestedSortable.css','css/menu.css');
$pages = new Model\Model('pages');
$pages->groupBy('language');
$pages->order('language ASC');
$pagesArray = $pages->get();
$languageOptions = '';
if(count($pagesArray) > 1) {
	foreach($pagesArray AS $page) {
		$flag = ($page->language == 'en')?'us':$page->language;
		$selected = ($page->language == _DEFAULT_LANGUAGE_)?' selected':'';
		$languageOptions .= "<option value=\"{$page->language}\"{$selected}>" . $page->languages->name . "</option>";
	}
}
$content = '<div class="box">
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