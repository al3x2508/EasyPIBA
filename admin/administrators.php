<?php
$page_title = "Editare operatori";
$js = array('plugins/datatables/jquery.dataTables.js','plugins/datatables/fnReloadAjax.js','plugins/datatables/dataTables.bootstrap.js','js/jsall.js','js/admins.js');
$css = array('plugins/datatables/dataTables.bootstrap.css');
$htmlPermsTh = '';
$permisiuni = new Model\Model('permisiuni');
$permisiuni = $permisiuni->get();
foreach($permisiuni AS $permisiune) {
	$htmlPermsTh .= '<tr><td><label for="edpermisiune[' . $permisiune['id'] . ']">' . $permisiune['nume'] . '</label></td><td><input type="checkbox" name="edpermisiune[' . $permisiune['id'] . ']" id="edpermisiune[' . $permisiune['id'] . ']" /></td></tr>';
}
$content = '
<script type="text/javascript">
	var aoColumns = [
		{ "mData": "id" },
		{ "mData": "nume" },
		{ "mData": "user" },
		{ "mData": function (e) {
			return $(\'#statusf option[value="\' + e.stare + \'"]\').text();
		} },
		{ "mData": function() {
			return "<span class=\\"actions btn fa fa-edit\\"></span>";
		} }
	];
</script>
<div class="box">
	<div class="box-header"><h3 class="box-title">List&#259; operatori</h3></div>
	<div class="box-body">
		<table id="data_table" class="table table-bordered table-hover">
			<thead>
				<tr><th>#<br /><input type="text" id="idf" class="tableFilter form-control" size="2"></th><th>Nume<br /><input type="text" size="10" id="numef" class="tableFilter form-control" /></th><th>User<br /><input type="text" size="5" id="userf" class="tableFilter form-control" /></th><th>Status<br /><select id="statusf" class="tableFilter form-control"><option value="-1">Oricare</option><option value="0">Inactiv</option><option value="1">Activ</option></select></th><th>Ac&#x21B;iuni</th></tr>
			</thead>
			<tbody>
			</tbody>
		</table>
	</div>
	<button id="add" class="btn btn-primary">Adaug&#259;</button>
</div>
<div id="ppEdit" class="modal dialog">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Închide">
					<span aria-hidden="true">×</span>
				</button>
				<h4 class="modal-title">Administrator</h4>
			</div>
			<div class="modal-body">
				<table id="edtable" class="edtable">
					<tr><td><label for="edfirstname">' . __('Firstname') . ':</label></td><td><input type="text" id="edfirstname" name="edfirstname" class="form-control" /></td></tr>
					<tr><td><label for="edlastname">' . __('Lastname') . ':</label></td><td><input type="text" id="edlastname" name="edlastname" class="form-control" /></td></tr>
					<tr><td><label for="edemail">' . __('Email') . ':</label></td><td><input type="email" id="edemail" name="edemail" class="form-control" /></td></tr>
					<tr><td><label for="edphone">' . __('Phone') . ':</label></td><td><input type="text" id="edphone" name="edphone" class="form-control" /></td></tr>
					<tr><td><label for="edaddress">' . __('Address') . ':</label></td><td><textarea id="edaddress" name="edaddress" class="form-control" rows="3" cols="30"></textarea></td></tr>
					<tr><td><label for="edcity">' . __('City') . ':</label></td><td><input type="text" id="edcity" name="edcity" class="form-control" /></td></tr>
					<tr><td><label for="edstate">' . __('State') . ':</label></td><td><input type="text" id="edstate" name="edstate" class="form-control" /></td></tr>
					<tr><td><label for="edcountry">' . __('Country') . ':</label></td><td><select id="edcountry" name="edcountry" class="form-control">' . $countryOptions . '</select></td></tr>
					<tr><td><label for="edparola">Parola:</label></td><td><input type="password" id="edparola" name="edparola" class="form-control" /></td></tr>
					<tr><td><label for="edstare">Stare:</label></td><td><select id="edstare" name="edstare" class="form-control"><option value="0">Blocat</option><option value="1">Activ</option></select></td></tr>
					' . $htmlPermsTh . '
				</table>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default pull-left" data-dismiss="modal">Închide</button>
				<button type="button" class="btn btn-primary" id="salveaza">Salvează</button>
			</div>
		</div>
	</div>
</div>';
?>