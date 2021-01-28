<?php
namespace Module\News\Admin;

use Model\Model;
use Utils\Util;

class AdminPage extends \Controller\AdminPage
{
    public function output()
    {
        $page = new \stdClass();
        $page->title = __('Edit news');
        $page->h1 = __('Edit news');
        $languages = new Model('languages');
        $languages->order('name ASC');
        $languages = $languages->get();
        $languageOptions = [0 => __('Any')];
        foreach($languages AS $language) {
            $selected = ($language->code == _DEFAULT_LANGUAGE_);
            $languageOptions[$language->code] = [$language->name, $selected];
        }
        $statusOptions = [-1 => __('Any'), 0 => __('Hidden'), 1 => __('Published')];
        $page->js = array('../vendor/datatables/datatables/media/js/jquery.dataTables.min.js', '../vendor/datatables/datatables/media/js/dataTables.bootstrap4.min.js', '../vendor/ckeditor/ckeditor/ckeditor.js', 'js/jsall.js', '../assets/js/bootstrap-autocomplete.min.js', 'Module/News/Admin/news.js');
        $page->css = array('../vendor/datatables/datatables/media/css/jquery.dataTables.min.css', '../vendor/datatables/datatables/media/css/dataTables.bootstrap4.min.css', 'dataTables.fontawesome.css');
        $page->content = '<div class="box">
	<div class="box-header">
	    <div class="row">
			<div class="col-md-9"><h3 class="box-title">' . __('Edit news') . '</h3></div>
			<div class="col-md-3">
				<a href="#" class="filter-datatable"><i class="fa fa-search"></i>' . __('Filters') . '</a>
			</div>
		</div>
	</div>
	<div class="box-body">
	    <div class="row">
			<div class="col-12">
				<table id="data_table" class="table table-borderless table-hover table-sm w-100">
					<thead class="thead-light">
						<tr><th data-filtertype="text" data-filterid="idf">#</th><th data-filtertype="select" data-filterid="languagef" data-options=\'' . json_encode($languageOptions, JSON_FORCE_OBJECT) . '\'>' . __('Language') . '</th><th data-filtertype="text" data-filterid="titlef">' . __('Title') . '</th><th data-filtertype="text" data-filterid="authorf" data-autocomplete="author">' . __('Author') . '</th><th>' . __('Date') . '</th><th data-filtertype="select" data-filterid="statusf" data-options=\'' . json_encode($statusOptions, JSON_FORCE_OBJECT) . '\'>' . __('Status') . '</th><th>' . __('Actions') . '</th></tr>
					</thead>
					<tbody>
					</tbody>
				</table>
			</div>
		</div>
	</div>
	<div class="box-footer">
		<div class="btn-toolbar">
			<button id="add" class="btn btn-outline-primary btn-sm"><i class="fa fa-plus"></i> ' . __('Add news') . '</button>
		</div>
	</div>
</div>
<div id="ppEdit" class="modal dialog fade">
	<div class="modal-dialog modal-lg" style="width: 80vw; max-width: 80vw;">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">' . __('Edit news') . '</h4>
				<button type="button" class="close" data-dismiss="modal" aria-label="' . __('Close') . '">
					<span aria-hidden="true">×</span>
				</button>
			</div>
			<div class="modal-body" id="edtable">
				<div class="form-group">
					<div class="input-group">
						<select class="form-control" id="edlanguage" name="edlanguage">' . Util::arrayToOptions($languageOptions) . '</select>
						<label for="edlanguage" class="control-label">' . __('Language') . '</label>
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
						<textarea id="edcontent" name="edcontent" class="form-control" rows="20" cols="300" ></textarea>
						<label for="edcontent" class="control-label">' . __('Content') . '</label>
						<i class="bar"></i>
					</div>
                </div>
                <form action="" method="post" id="imageUploadForm">
					<div class="form-group">
					    <div class="input-group">
                            <input type="file" class="form-control" id="edimage" name="edimage" />
                            <label for="edimage" class="control-label">' . __('Image') . '</label>
                            <i class="bar"></i>
                            <img id="imagePreview" src="" data-default="default.jpg" data-folder="news" />
                        </div>
					</div>
				</form>
				<div class="form-group">
					<div class="input-group">
						<select id="edstatus" name="edstatus">' . Util::arrayToOptions($statusOptions, true) . '</select>
						<label for="edstatus" class="control-label">' . __('Status') . '</label>
						<i class="bar"></i>
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
        return $page;
    }
}