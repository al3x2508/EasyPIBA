<?php
namespace Module\News\Admin;
use Model\Model;

class AdminPage extends \Controller\AdminPage {
	public function output() {
		$page = new \stdClass();
		$page->title = __('Edit news');
		$page->h1 = __('Edit news');
		$languages = new Model('languages');
		$languages->order('name ASC');
		$languages = $languages->get();
		$languageOptions = '';
		foreach($languages AS $language) {
			$selected = ($language->code == $_ENV['DEFAULT_LANGUAGE'])?' selected':'';
			$languageOptions .= "<option value=\"{$language->code}\"{$selected}>" . $language->name . "</option>";
		}
		$page->js = array('js/combobox.js','../node_modules/datatables.net/js/jquery.dataTables.min.js', '../node_modules/datatables.net-bs5/js/dataTables.bootstrap5.min.js', '../node_modules/datatables.net-responsive/js/dataTables.responsive.min.js', '../node_modules/datatables.net-responsive-bs5/js/responsive.bootstrap5.min.js' ,'../vendor/ckeditor/ckeditor/ckeditor.js','../bower_components/select2/dist/js/select2.full.min.js','js/app.js','Module/News/Admin/news.js');
		$page->css = array('../bower_components/select2/dist/css/select2.min.css','../node_modules/select2-bootstrap-5-theme/dist/select2-bootstrap-5-theme.min.css','../node_modules/datatables.net-bs5/css/dataTables.bootstrap5.min.css', '../node_modules/datatables.net-responsive-bs5/css/responsive.bootstrap5.min.css');
		$page->content = '<div class="box">
	<div class="box-header"><h3 class="box-title">' . __('Edit news') . '</h3></div>
	<div class="box-body">
		<table id="data_table" class="table table-bordered table-hover">
			<thead>
				<tr><th>#<br /><input type="text" id="idf" class="tableFilter form-control" size="2"></th><th>' . __('Language') . '<br /><select class="form-control select2" id="languagef"><option value="0">' . __('Any') . '</option>' . $languageOptions . '</select></th><th>' . __('Title') . '<br /><input type="text" size="10" id="titlef" class="tableFilter form-control"></th><th>' . __('Author') . '<br /><input type="text" size="2" id="authorf" class="tableFilter ui-autocomplete-input autocomplete-author form-control" autocomplete="off" /></th><th>' . __('Date') . '</th><th>' . __('Status') . '<br /><select id="statusf" class="tableFilter form-control"><option value="-1">' . __('Any') . '</option><option value="0">' . __('Hidden') . '</option><option value="1">' . __('Published') . '</option></select></th><th>' . __('Actions') . '</th></tr>
			</thead>
			<tbody>
			</tbody>
		</table>
	</div>
	<button id="add" class="btn btn-primary">' . __('Add') . '</button>
</div>
<div id="ppEdit" class="modal" tabindex="-1" role="dialog">
	<div class="modal-dialog" role="document" style="width: 80vw;">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">' . __('Edit news') . '</h4>
				<button type="button" class="close" data-bs-dismiss="modal" aria-label="' . __('Close') . '">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body" id="edtable">
				<div class="form-group">
					<label for="edlanguage">' . __('Language') . '</label>
					<select class="form-control select2" id="edlanguage" name="edlanguage">' . $languageOptions . '</select>
                </div>
				<div class="form-group">
					<label for="edtitle">' . __('Title') . '</label>
					<input type="text" class="form-control" id="edtitle" name="edtitle" placeholder="' . __('Title') . '" />
                </div>
                <div class="form-group">
					<label for="edcontent">' . __('Content') . '</label>
					<textarea id="edcontent" name="edcontent" class="form-control" rows="20" cols="300" placeholder="' . __('Content') . '"></textarea>
                </div>
                <form action="" method="post" id="imageUploadForm">
					<div class="form-group">
						<label for="edimage">' . __('Image') . '</label>
						<input type="file" class="form-control" id="edimage" name="edimage" placeholder="' . __('Image') . '" />
						<img id="imagePreview" src="" data-default="default.jpg" data-folder="news" />
					</div>
				</form>
				<div class="form-group">
					<label for="edstatus">' . __('Status') . '</label>
					<select id="edstatus" name="edstatus" class="form-control"><option value="1">' . __('Active') . '</option><option value="2">' . __('Blocked') . '</option></select>
                </div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default pull-left" data-bs-dismiss="modal">' . __('Close') . '</button>
				<button type="button" class="btn btn-primary" id="save">' . __('Save') . '</button>
			</div>
		</div>
	</div>
</div>';
		return $page;
	}
}