<?php
namespace Module\Administrators\Admin;
use Controller\AdminController;
use Model\Model;

class AdminPage extends \Controller\AdminPage {
	public function __construct() {
		$this->permission = 'Edit administrators';
		$adminController = new AdminController();
		$this->hasAccess = $adminController->checkPermission($this->permission);
		return $this;
	}
	public function getMenu($returnPermissions, $currentLink = '') {
		if($this->hasAccess) {
			if($returnPermissions) return array('administrators');
			else return self::createLink(array('href' => 'administrators', 'text' => __('Administrators'), 'class' => 'users'), $currentLink);
		}
		return false;
	}
	public function output() {
		$page = new \stdClass();
		$page->title = __('Edit administrators');
		$page->h1 = __('Edit administrators');
		$page->js = array('plugins/datatables/jquery.dataTables.js','plugins/datatables/fnReloadAjax.js','plugins/datatables/dataTables.bootstrap.js','plugins/iCheck/icheck.min.js','js/jsall.js','../../Module/Administrators/Admin/administrators.js');
		$page->css = array('plugins/datatables/dataTables.bootstrap.css', 'plugins/iCheck/all.css');
		$htmlPerms = '';
		$permissions = new Model('permissions');
		$permissions = $permissions->get();
		foreach($permissions AS $permission) $htmlPerms .= '<div class="form-group"><input type="checkbox" name="edpermission[' . $permission->id . ']" id="edpermission[' . $permission->id . ']" class="minimal" /> <label for="edpermission[' . $permission->id . ']">' . $permission->name . '</label></div>';
		$page->content = '
<script type="text/javascript">
	var aoColumns = [
		{ "mData": "id" },
		{ "mData": "name" },
		{ "mData": "username" },
		{ "mData": function (e) {
			return $(\'#statusf option[value="\' + e.status + \'"]\').text();
		} },
		{ "mData": function() {
			return "<span class=\\"actions btn fa fa-edit\\"></span>";
		} }
	];
</script>
<div class="box">
	<div class="box-header"><h3 class="box-title">' . __('Edit administrators') . '</h3></div>
	<div class="box-body">
		<table id="data_table" class="table table-bordered table-hover">
			<thead>
				<tr><th>#<br /><input type="text" id="idf" class="tableFilter form-control" size="2"></th><th>' . __('Name') . '<br /><input type="text" size="10" id="namef" class="tableFilter form-control" /></th><th>' . __('Username') . '<br /><input type="text" size="5" id="usernamef" class="tableFilter form-control" /></th><th>' . __('Status') . '<br /><select id="statusf" class="tableFilter form-control"><option value="-1">' . __('Any') . '</option><option value="0">' . __('Blocked') . '</option><option value="1">' . __('Active') . '</option></select></th><th>' . __('Actions') . '</th></tr>
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
				<h4 class="modal-title">' . __('Edit administrator') . '</h4>
			</div>
			<div class="modal-body" id="edtable">
				<div class="form-group">
					<label for="edname">' . __('Name') . '</label>
					<input type="text" class="form-control" id="edname" name="edname" placeholder="' . __('Name') . '" />
                </div>
                <div class="form-group">
					<label for="edusername">' . __('Username') . '</label>
					<input type="text" class="form-control" id="edusername" name="edusername" placeholder="' . __('Username') . '" />
                </div>
                <div class="form-group">
					<label for="edpassword">' . __('Password') . '</label>
					<input type="password" class="form-control" id="edpassword" name="edpassword" placeholder="' . __('Password') . '" />
                </div>
                <div class="form-group">
					<label for="edstatus">' . __('Status') . '</label>
					<select id="edstatus" name="edstatus" class="form-control"><option value="1">' . __('Active') . '</option><option value="2">' . __('Blocked') . '</option></select>
                </div>
				' . $htmlPerms . '
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