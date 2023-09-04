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
		$this->js = array('../node_modules/datatables.net/js/jquery.dataTables.min.js', '../node_modules/datatables.net-bs5/js/dataTables.bootstrap5.min.js', '../node_modules/datatables.net-responsive/js/dataTables.responsive.min.js', '../node_modules/datatables.net-responsive-bs5/js/responsive.bootstrap5.min.js' ,'Module/Media/Admin/media.js');
		$this->css = array('../node_modules/datatables.net-bs5/css/dataTables.bootstrap5.min.css', '../node_modules/datatables.net-responsive-bs5/css/responsive.bootstrap5.min.css', 'Module/Media/Admin/media.css');
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
<div id="ppEdit" class="modal" tabindex="-1" role="dialog">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">Media</h4>
				<button type="button" class="close" data-bs-dismiss="modal" aria-label="' . __('Close') . '">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body" id="edtable">
				<form action="" method="post" id="imageUploadForm">
					<div class="form-group">
						<label for="edprotected">' . __('Protected') . '</label>
						<input type="checkbox" id="edprotected" name="edprotected" />
					</div>
					<div class="form-group">
						<label for="edimage">' . __('Image') . '</label>
						<input type="file" class="form-control" id="edimage" name="edimage" placeholder="' . __('Image') . '" />
					</div>
				</form>
				<img id="imagePreview" src="" />
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default pull-left" data-bs-dismiss="modal">' . __('Close') . '</button>
				<button type="button" class="btn btn-primary" id="save">' . __('Save') . '</button>
			</div>
		</div>
	</div>
</div>';
	}
}