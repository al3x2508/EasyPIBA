<?php
namespace Module\Settings\Admin;
use Model\Model;

class AdminPage extends \Controller\AdminPage {
    public function output() {
        $page = new \stdClass();
        $page->title = __('Edit settings');
        $page->h1 = __('Edit settings');
        $page->js = array('js/combobox.js','../node_modules/datatables.net/js/jquery.dataTables.min.js', '../node_modules/datatables.net-bs5/js/dataTables.bootstrap5.min.js', '../node_modules/datatables.net-responsive/js/dataTables.responsive.min.js', '../node_modules/datatables.net-responsive-bs5/js/responsive.bootstrap5.min.js','js/app.js','Module/Settings/Admin/settings.js');
        $page->css = array('../bower_components/select2/dist/css/select2.min.css','../node_modules/select2-bootstrap-5-theme/dist/select2-bootstrap-5-theme.min.css','../node_modules/datatables.net-bs5/css/dataTables.bootstrap5.min.css', '../node_modules/datatables.net-responsive-bs5/css/responsive.bootstrap5.min.css');
        $page->content = '<div class="box">
	<div class="box-header"><h3 class="box-title">' . __('Edit settings') . '</h3></div>
	<div class="box-body">
		<table id="data_table" class="table table-bordered table-hover">
			<thead>
				<tr><th>' . __('Name') . '<br /><input type="text" id="settingf" class="tableFilter form-control" /></th><th>' . __('Value') . '</th><th>' . __('Actions') . '</th></tr>
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
				<h4 class="modal-title">' . __('Edit setting') . '</h4>
				<button type="button" class="close" data-bs-dismiss="modal" aria-label="' . __('Close') . '">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body" id="edtable">
				<div class="form-group">
					<label for="edsetting">' . __('Setting name') . '</label>
					<input type="text" class="form-control" id="edsetting" name="edsetting" placeholder="' . __('Setting name') . '" />
                </div>
                <div class="form-group">
					<label for="edvalue">' . __('Value') . '</label>
					<input type="text" class="form-control" id="edvalue" name="edvalue" placeholder="' . __('Setting value') . '" />
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