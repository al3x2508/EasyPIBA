<?php
$page_title = __('Edit news');
$js = array('js/combobox.js','plugins/datatables/jquery.dataTables.js','plugins/datatables/fnReloadAjax.js','plugins/datatables/dataTables.bootstrap.js','plugins/ckeditor/ckeditor.js','js/jsall.js','js/news.js');
$css = array('plugins/datatables/dataTables.bootstrap.css');
$content = '<div class="box">
	<div class="box-header"><h3 class="box-title">' . __('Edit news') . '</h3></div>
	<div class="box-body">
		<table id="data_table" class="table table-bordered table-hover">
			<thead>
				<tr><th>#<br /><input type="text" id="idf" class="tableFilter form-control" size="2"></th><th>' . __('Title') . '<br /><input type="text" size="10" id="titluf" class="tableFilter form-control"></th><th>' . __('Author') . '<br /><input type="text" size="2" id="authorf" class="tableFilter ui-autocomplete-input autocomplete-autor form-control" autocomplete="off"></th><th>' . __('Date') . '</th><th>' . __('Status') . '<br /><select id="statusf" class="tableFilter form-control"><option value="-1">' . __('Any') . '</option><option value="0">' . __('Hidden') . '</option><option value="1">' . __('Published') . '</option></select></th><th>' . __('Actions') . '</th></tr>
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
				<h4 class="modal-title">Știri</h4>
			</div>
			<div class="modal-body" class="edtable">
				
				<table id="edtable">
					<tr><td><label for="edtitlu">Titlu:</label></td><td><input type="text" id="edtitlu" name="edtitlu" class="form-control" /></td></tr>
					<tr><td><label for="edcontent">Con&#x21B;inut:</label></td><td><textarea id="edcontent" name="edcontent" class="form-control" rows="15" cols="300"></textarea></td></tr>
					<tr><td><label for="edimagine">Imagine:</label></td><td><input type="file" name="edimagine" id="edimagine" class="form-control" accept="image/*" /><img src="/img/stiri/lansare_mykoolio-360x220.jpg" id="imagePreview" /></td></tr>
					<tr><td><label for="edstare">Status:</label></td><td><input type="checkbox" id="edstare" name="edstare" /></td></tr>
				</table>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default pull-left" data-dismiss="modal">' . __('Close') . '</button>
				<button type="button" class="btn btn-primary" id="save">' . __('Save') . '</button>
			</div>
		</div>
	</div>
</div>';
?>