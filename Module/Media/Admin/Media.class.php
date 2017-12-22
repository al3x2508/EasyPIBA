<?php
namespace Module\Media\Admin;
class Media {
	/**
	 * @param $key
	 * @param $value
	 */
	public function __set($key, $value) {
		$this->$key = $value;
	}
	public function __construct() {
		$this->title = __('Media');
		$this->h1 = __('Media');
		$this->js = array('../vendor/datatables/datatables/media/js/jquery.dataTables.min.js','../vendor/datatables/datatables/media/js/dataTables.bootstrap.min.js','js/jsall.js','../Module/Media/Admin/media.js');
		$this->css = array('../vendor/datatables/datatables/media/css/dataTables.bootstrap.min.css');
		$this->content = '<div class="box">
	<div class="box-header"><h3 class="box-title">' . __('Media') . '</h3></div>
	<div class="box-body">
		<table id="data_table" class="table table-bordered table-hover">
			<thead>
				<tr><th>#<br /><input type="text" id="idf" class="tableFilter form-control" size="2"></th><th>' . __('Filename') . '</th><th>' . __('Preview') . '</th><th>' . __('Actions') . '</th></tr>
			</thead>
			<tbody>
			</tbody>
		</table>
	</div>
	<button id="add" class="btn btn-primary">' . __('Add') . '</button>
</div>
<div id="ppEdit" class="modal dialog">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="' . __('Close') . '">
					<span aria-hidden="true">×</span>
				</button>
				<h4 class="modal-title">Media</h4>
			</div>
			<div class="modal-body" id="edtable">
				<form action="" method="post" id="imageUploadForm">
					<div class="form-group">
						<label for="edimage">' . __('Image') . '</label>
						<input type="file" class="form-control" id="edimage" name="edimage" placeholder="' . __('Image') . '" />
					</div>
				</form>
				<img id="imagePreview" src="" />
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