<?php
namespace Module\Testimonials\Admin;

class AdminPage extends \Controller\AdminPage {
	public function output() {
		$page = new \stdClass();
		$page->title = __('Testimonials');
		$page->h1 = __('Testimonials');
		$page->js = array('../../vendor/datatables/datatables/media/js/jquery.dataTables.min.js', '../../vendor/datatables/datatables/media/js/dataTables.bootstrap.js', '../../vendor/datatables/datatables/media/js/dataTables.responsive.min.js', '../../vendor/datatables/datatables/media/js/responsive.bootstrap.min.js','../vendor/ckeditor/ckeditor/ckeditor.js','js/jsall.js','Module/Testimonials/Admin/testimonials.js');
		$page->css = array('../vendor/datatables/datatables/media/css/dataTables.bootstrap.min.css');
		$page->content = '<div class="box">
	<div class="box-header"><h3 class="box-title">' . __('Testimonials') . '</h3></div>
	<div class="box-body">
		<table id="data_table" class="table table-bordered table-hover">
			<thead>
				<tr><th>#<br /><input type="text" id="idf" class="tableFilter form-control" size="2"></th><th>' . __('Name') . '<br /><input type="text" size="10" id="namef" class="tableFilter form-control" /></th><th>' . __('Company') . '</th><th>' . __('Short text') . '</th><th>Status<br /><select id="statusf" class="tableFilter form-control"><option value="-1">' . __('Any') . '</option><option value="0">' . __('Hidden') . '</option><option value="1">' . __('Visible') . '</option></select></th><th>' . __('Actions') . '</th></tr>
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
				<h4 class="modal-title">' . __('Testimonials') . '</h4>
			</div>
			<div class="modal-body" id="edtable">
				<div class="form-group">
					<label for="edname">' . __('Name') . '</label>
					<input type="text" class="form-control" id="edname" name="edname" placeholder="' . __('Name') . '" />
                </div>
                <div class="form-group">
					<label for="edcompany">' . __('Company') . '</label>
					<input type="text" class="form-control" id="edcompany" name="edcompany" placeholder="' . __('Company') . '" />
                </div>
                <div class="form-group">
					<label for="edshort">' . __('Short text') . '</label>
					<textarea id="edshort" name="edshort" class="form-control" rows="3" cols="30" placeholder="' . __('Short text') . '"></textarea>
                </div>
                <div class="form-group">
					<label for="edcontent">' . __('Content') . '</label>
					<textarea id="edcontent" name="edcontent" class="form-control" rows="20" cols="300" placeholder="' . __('Content') . '"></textarea>
                </div>
                <form action="" method="post" id="imageUploadForm">
					<div class="form-group">
						<label for="edimage">' . __('Image') . '</label>
						<input type="file" class="form-control" id="edimage" name="edimage" placeholder="' . __('Image') . '" />
						<img id="imagePreview" src="" data-default="default.jpg" data-folder="testimonials" />
					</div>
				</form>
                <div class="form-group">
					<label for="edstatus">' . __('Status') . '</label>
					<select id="edstatus" name="edstatus" class="form-control"><option value="1">' . __('Active') . '</option><option value="2">' . __('Blocked') . '</option></select>
                </div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default pull-left" data-dismiss="modal">' . __('Close') . '</button>
				<button type="button" class="btn btn-primary" id="save">' . __('Save') . '</button>
			</div>
		</div>
	</div>
</div>';
		return $page;
	}
}