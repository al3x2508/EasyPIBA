<?php
$page_title = __('Testimonials');
$js = array('plugins/datatables/jquery.dataTables.js','plugins/datatables/fnReloadAjax.js','plugins/datatables/dataTables.bootstrap.js','plugins/ckeditor/ckeditor.js','js/jsall.js','js/testimonials.js');
$css = array('plugins/datatables/dataTables.bootstrap.css');
$content = '<div class="box">
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
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="' . __('Close') . '">
					<span aria-hidden="true">×</span>
				</button>
				<h4 class="modal-title">' . __('Testimonials') . '</h4>
			</div>
			<div class="modal-body">
				<table id="edtable" class="edtable">
					<tr><td><label for="ednume">Nume:</label></td><td><input type="text" id="ednume" name="ednume" class="form-control" /></td></tr>
					<tr><td><label for="edcalitate">Calitate:</label></td><td><input type="text" id="edcalitate" name="edcalitate" class="form-control" /></td></tr>
					<tr><td><label for="edshort">Text scurt:</label></td><td><textarea id="edshort" name="edshort" class="form-control"></textarea></td></td></tr>
					<tr><td><label for="edcontent">Con&#x21B;inut:</label></td><td><textarea id="edcontent" name="edcontent" class="form-control" rows="20" cols="300"></textarea></td></tr>
					<tr><td><label for="edstare">Status:</label></td><td><input type="checkbox" id="edstare" name="edstare" /></td></tr>
				</table>
			</div>
			<div class="modal-footer">
				button type="button" class="btn btn-default pull-left" data-dismiss="modal">' . __('Close') . '</button>
				<button type="button" class="btn btn-primary" id="save">' . __('Save') . '</button>
			</div>
		</div>
	</div>
</div>';