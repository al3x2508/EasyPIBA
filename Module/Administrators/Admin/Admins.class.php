<?php
namespace Module\Administrators\Admin;
use Model\Model;

class Admins {
	/**
	* @param $key
	* @param $value
	*/
	public function __set($key, $value) {
		$this->$key = $value;
	}
	public function __construct() {
		$this->title = __('Edit administrators');
		$this->h1 = __('Edit administrators');
		$this->js = array('../vendor/datatables/datatables/media/js/jquery.dataTables.min.js', '../vendor/datatables/datatables/media/js/dataTables.bootstrap4.min.js','js/jsall.js','Module/Administrators/Admin/administrators.js');
		$this->css = array('../vendor/datatables/datatables/media/css/jquery.dataTables.min.css', '../vendor/datatables/datatables/media/css/dataTables.bootstrap4.min.css', 'dataTables.fontawesome.css');
		$htmlPerms = '';
		$permissions = new Model('permissions');
		$permissions = $permissions->get();
		foreach($permissions AS $permission) $htmlPerms .= '
			<div class="form-group">
				<div class="custom-control custom-switch">
					<input type="checkbox" name="edpermission[' . $permission->id . ']" id="edpermission[' . $permission->id . ']" class="custom-control-input" />
					<label class="custom-control-label" for="edpermission[' . $permission->id . ']">' . $permission->name . '</label>
				</div>
			</div>';
		$statusOptions = array(
			-1 => __('Any'),
			0 => __('Blocked'),
			1 => __('Active')
		);
		$this->content = '
<div class="box">
	<div class="box-header">
		<div class="row">
			<div class="col-md-9"><h3 class="box-title">' . __('Edit administrators') . '</h3></div>
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
						<tr><th data-filtertype="text" data-filterid="idf">#</th><th data-filtertype="text" data-filterid="namef">' . __('Name') . '</th><th data-filtertype="text" data-filterid="usernamef">' . __('Username') . '</th><th data-filtertype="select" data-filterid="statusf" data-options=\'' . json_encode($statusOptions, JSON_FORCE_OBJECT) . '\'>' . __('Status') . '</th><th>' . __('Actions') . '</th></tr>
					</thead>
					<tbody>
					</tbody>
				</table>
			</div>
		</div>
	</div>
	<div class="box-footer">
		<div class="btn-toolbar">
			<button id="add" class="btn btn-outline-primary btn-sm"><i class="fa fa-plus"></i> ' . __('Add admin') . '</button>
		</div>
	</div>
</div>
<div id="ppEdit" class="modal dialog fade">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">' . __('Edit administrator') . '</h4>
				<button type="button" class="close" data-dismiss="modal" aria-label="' . __('Close') . '">
					<span aria-hidden="true">×</span>
				</button>
			</div>
			<div class="modal-body" id="edtable">
				<div class="form-group">
					<div class="input-group">
						<input type="text" class="form-control" id="edname" name="edname" required  />
						<label for="edname" class="control-label">' . __('Name') . '</label>
						<i class="bar"></i>
					</div>
				</div>
                <div class="form-group">
					<div class="input-group">
						<input type="text" class="form-control" id="edusername" name="edusername" required  />
						<label for="edusername" class="control-label">' . __('Username') . '</label>
						<i class="bar"></i>
					</div>
				</div>
                <div class="form-group">
					<div class="input-group">
						<input type="password" class="form-control" id="edpassword" name="edpassword"  />
						<label for="edpassword" class="control-label">' . __('Password') . '</label>
						<i class="bar"></i>
					</div>
				</div>
                <div class="form-group">
					<div class="input-group">
						<select id="edstatus" name="edstatus" class="form-control"><option value="1">' . __('Active') . '</option><option value="2">' . __('Blocked') . '</option></select>
						<label for="edstatus" class="control-label">' . __('Status') . '</label>
						<i class="bar"></i>
					</div>
				</div>
				' . $htmlPerms . '
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