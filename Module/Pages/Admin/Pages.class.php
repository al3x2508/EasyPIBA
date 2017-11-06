<?php
namespace Module\Pages\Admin;
use Model\Model;

class Pages {
	/**
	 * @param $key
	 * @param $value
	 */
	public function __set($key, $value) {
		$this->$key = $value;
	}
	public function __construct() {
		$this->title = __('Edit pages');
		$this->h1 = __('Edit pages');
		$languages = new Model('languages');
		$languages->order('name ASC');
		$languages = $languages->get();
		$languageOptions = '';
		foreach($languages AS $language) {
			$selected = ($language->code == _DEFAULT_LANGUAGE_)?' selected':'';
			$languageOptions .= "<option value=\"{$language->code}\"{$selected}>" . $language->name . "</option>";
		}
		$this->js = array('plugins/datatables/jquery.dataTables.js','plugins/datatables/fnReloadAjax.js','plugins/datatables/dataTables.bootstrap.js','plugins/ckeditor/ckeditor.js','bower_components/select2/dist/js/select2.full.min.js','js/jsall.js','../../Module/Pages/Admin/pages.js');
		$this->css = array('bower_components/select2/dist/css/select2.min.css', 'plugins/datatables/dataTables.bootstrap.css');
		$this->content = '<div class="box">
	<div class="box-header"><h3 class="box-title">' . __('Edit pages') . '</h3></div>
	<div class="box-body">
		<table id="data_table" class="table table-bordered table-hover">
			<thead>
				<tr><th>#<br /><input type="text" id="idf" class="tableFilter form-control" /></th><th>' . __('URL') . '<br /><input type="text" id="urlf" class="tableFilter form-control" /></th><th>' . __('Language') . '<br /><select class="form-control select2" id="languagef"><option value="0">' . __('Any') . '</option>' . $languageOptions . '</select></th><th>' . __('Title') . '<br /><input type="text" id="titlef" class="tableFilter form-control" /></th><th>' . __('Menu text') . '<br /><input type="text" id="menutextf" class="tableFilter form-control" /></th><th>' . __('Actions') . '</th></tr>
			</thead>
			<tbody>
			</tbody>
		</table>
	</div>
	<button id="add" class="btn btn-primary">' . __('Add') . '</button>
</div>
<div id="ppEdit" class="modal dialog">
	<div class="modal-dialog" style="width: 80vw;">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="' . __('Close') . '">
					<span aria-hidden="true">×</span>
				</button>
				<h4 class="modal-title">' . __('Edit pages') . '</h4>
			</div>
			<div class="modal-body" id="edtable">
				<div class="form-group">
					<label for="edlanguage">' . __('Language') . '</label>
					<select class="form-control select2" id="edlanguage" name="edlanguage">' . $languageOptions . '</select>
                </div>
				<div class="form-group">
					<label for="edurl">' . __('URL') . '</label>
					<input type="text" class="form-control" id="edurl" name="edurl" placeholder="' . __('URL') . '" />
                </div>
                <div class="form-group">
					<label for="edtitle">' . __('Title') . '</label>
					<input type="text" class="form-control" id="edtitle" name="edtitle" placeholder="' . __('Title') . '" />
                </div>
                <div class="form-group">
					<label for="eddescription">' . __('Description') . '</label>
					<input type="text" class="form-control" id="eddescription" name="eddescription" placeholder="' . __('Description') . '" />
                </div>
                <div class="form-group">
					<label for="edh1">' . __('H1') . '</label>
					<input type="text" class="form-control" id="edh1" name="edh1" placeholder="' . __('H1') . '" />
                </div>
                <div class="form-group">
					<label for="edmenu_text">' . __('Menu text') . '</label>
					<input type="text" class="form-control" id="edmenu_text" name="edmenu_text" placeholder="' . __('Menu text') . '" />
                </div>
                <div class="form-group">
					<label for="edsubmenu_text">' . __('Submenu text') . '</label>
					<input type="text" class="form-control" id="edsubmenu_text" name="edsubmenu_text" placeholder="' . __('Submenu text') . '" />
                </div>
                <div class="form-group">
					<label for="edcontent">' . __('Content') . '</label>
					<textarea id="edcontent" name="edcontent" class="form-control" rows="20" cols="300" placeholder="' . __('Content') . '"></textarea>
                </div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default pull-left" data-dismiss="modal">' . __('Close') . '</button>
				<button type="button" class="btn btn-primary" id="save">' . __('Save') . '</button>
			</div>
		</div>
	</div>
</div>';
	}
}