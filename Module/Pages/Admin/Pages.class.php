<?php
namespace Module\Pages\Admin;
use Model\Model;
use Utils\Util;

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
		$languageOptions = [0 => __('Any')];
		foreach($languages AS $language) {
			$selected = ($language->code == _DEFAULT_LANGUAGE_);
			$languageOptions[$language->code] = [$language->name, $selected];
		}
		$this->js = array('../vendor/datatables/datatables/media/js/jquery.dataTables.min.js', '../vendor/datatables/datatables/media/js/dataTables.bootstrap4.min.js', '../vendor/ckeditor/ckeditor/ckeditor.js', 'js/jsall.js', 'Module/Pages/Admin/pages.js');
		$this->css = array('../vendor/datatables/datatables/media/css/jquery.dataTables.min.css', '../vendor/datatables/datatables/media/css/dataTables.bootstrap4.min.css', 'dataTables.fontawesome.css');
		$this->content = '<div class="card">
	<div class="card-header">
		<div class="row">
			<div class="col-md-9"><h3 class="card-title">' . __('Edit pages') . '</h3></div>
			<div class="col-md-3">
				<a href="#" class="filter-datatable"><i class="fa fa-search"></i>' . __('Filters') . '</a>
			</div>
		</div>
	</div>
	<div class="card-body">
		<div class="row">
			<div class="col-12">
				<table id="data_table" class="table table-borderless table-hover table-sm w-100">
					<thead class="thead-light">
						<tr><th data-filtertype="text" data-filterid="idf">#</th><th data-filtertype="text" data-filterid="urlf">' . __('URL') . '</th><th data-filtertype="select" data-filterid="languagef" data-options=\'' . json_encode($languageOptions, JSON_FORCE_OBJECT) . '\'>' . __('Language') . '</th><th data-filtertype="text" data-filterid="titlef">' . __('Title') . '</th><th data-filtertype="text" data-filterid="menutextf">' . __('Menu text') . '</th><th>' . __('Actions') . '</th></tr>
					</thead>
					<tbody>
					</tbody>
				</table>
			</div>
		</div>
	</div>
	<div class="card-footer">
		<div class="btn-toolbar">
			<button id="add" class="btn btn-primary btn-sm"><i class="fa fa-plus"></i> ' . __('Add page') . '</button>
		</div>
	</div>
</div>
<div id="ppEdit" class="modal dialog fade">
	<div class="modal-dialog modal-lg" style="width: 80vw; max-width: 80vw;">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">' . __('Edit pages') . '</h4>
				<button type="button" class="close" data-dismiss="modal" aria-label="' . __('Close') . '">
					<i class="fa fa-times"></i>
				</button>
			</div>
			<div class="modal-body" id="edtable">
			    <div class="row">
                    <div class="col-12 col-md-3">
                        <div class="form-group">
                            <div class="input-group">
                                <select class="form-control" id="edlanguage" name="edlanguage">' . Util::arrayToOptions($languageOptions) . '</select>
                                <label for="edlanguage" class="control-label">' . __('Language') . '</label>
                                <i class="bar"></i>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="input-group">
                                <input type="text" class="form-control" id="edurl" name="edurl" />
                                <label for="edurl" class="control-label">' . __('URL') . '</label>
                                <i class="bar"></i>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="input-group">
                                <input type="text" class="form-control" id="edtitle" name="edtitle" required />
                                <label for="edtitle" class="control-label">' . __('Title') . '</label>
                                <i class="bar"></i>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="input-group">
                                <input type="text" class="form-control" id="eddescription" name="eddescription" data-nockedit="true" required />
                                <label for="eddescription" class="control-label">' . __('Description') . '</label>
                                <i class="bar"></i>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="input-group">
                                <input type="text" class="form-control" id="edmenu_text" name="edmenu_text" />
                                <label for="edmenu_text" class="control-label">' . __('Menu text') . '</label>
                                <i class="bar"></i>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="input-group">
                                <input type="text" class="form-control" id="edsubmenu_text" name="edsubmenu_text" />
                                <label for="edsubmenu_text" class="control-label">' . __('Submenu text') . '</label>
                                <i class="bar"></i>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" id="edvisible" name="edvisible" class="custom-control-input" checked />
                                <label for="edvisible" class="custom-control-label">' . __('Visible') . '</label>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-9">
                        <div class="form-group">
                            <div class="input-group">
                                <textarea id="edcontent" name="edcontent" class="form-control" rows="60" cols="300" ></textarea>
                                <label for="edcontent" class="control-label">' . __('Content') . '</label>
                                <i class="bar"></i>
                            </div>
                        </div>
                    </div>
                </div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default pull-left" data-dismiss="modal">' . __('Close') . '</button>
				<button type="button" class="btn btn-outline-primary" id="save">' . __('Save') . '</button>
			</div>
		</div>
	</div>
</div>';
	}
}