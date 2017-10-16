<?php
$judeteOptions = '';
$judete = new Model\Model('judete');
$judete = $judete->get();
foreach($judete AS $judet) $judeteOptions .= '<option value="' . $judet['id'] . '">' . $judet['nume_judet'] . '</option>'.PHP_EOL;
$numePagina = "Editare p&#259;rin&#x21B;i";
$js = array('plugins/datatables/jquery.dataTables.js','plugins/datatables/fnReloadAjax.js','plugins/datatables/dataTables.bootstrap.js','js/jsall.js','js/clienti.js');
$css = array('plugins/datatables/dataTables.bootstrap.css');
$continut = '<div class="box">
	<div class="box-header"><h3 class="box-title">List&#259; p&#259;rin&#x21B;i</h3></div>
	<div class="box-body">
		<table id="tabel_date" class="table table-bordered table-hover">
			<thead>
				<tr><th>#<br /><input type="text" id="idf" class="filtruTabel form-control" size="2"></th><th>Nume<br /><input type="text" size="10" id="numef" class="filtruTabel ui-autocomplete-input form-control" autocomplete="off"></th><th>Prenume<br /><input type="text" size="10" id="prenumef" class="filtruTabel ui-autocomplete-input form-control" autocomplete="off"></th><th>Localitate</th><!--<th>Jude&#x21B;<br /><select id="judetf" class="filtruTabel form-control"><option value="0">Toate</option>' . $judeteOptions . '</select></th>--><th>E-mail<br /><input type="text" id="emailf" class="filtruTabel form-control"></th><th>Telefon<br /><input type="tel" id="telefonf" size="10" class="filtruTabel form-control"></th><th>Status<br /><select id="statusf" class="filtruTabel form-control"><option value="-1">Oricare</option><option value="0">Neconfirmat</option><option value="1">Confirmat</option><option value="2">Blocat</option></select></th><th>Are copii<br /><select id="arecopiif" class="filtruTabel form-control"><option value="-1">Oricare</option><option value="0">Fara</option><option value="1">Cu</option></select></th><th>Copii premium<br /><select id="arecopiipf" class="filtruTabel form-control"><option value="-1">Oricare</option><option value="0">Fara</option><option value="1">Cu</option></select></th><th>Profesor<br /><select id="proff" class="filtruTabel form-control"><option value="-1">Oricare</option><option value="0">Fara</option><option value="1">Cu</option></select></th><th>Ac&#x21B;iuni</th></tr>
			</thead>
			<tbody>
			</tbody>
		</table>
	</div>
	<button id="adaugare" class="btn btn-primary">Adaug&#259;</button>
	<button class="btn btn-primary btn-export">Excel</button>
	<button class="btn btn-primary btn-export">PDF</button>
</div>
<div id="ppEdit" class="modal dialog">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Închide">
					<span aria-hidden="true">×</span>
				</button>
				<h4 class="modal-title">Clienti</h4>
			</div>
			<div class="modal-body">
				<table id="edtabel" class="edtabel">
					<tr><td><label for="ednume_client">Nume p&#259;rinte:</label></td><td><input type="text" id="ednume_client" name="ednume_client" class="form-control" /></td><td><label for="edprenume_client">Prenume p&#259;rinte:</label></td><td><input type="text" id="edprenume_client" name="edprenume_client" class="form-control" /></td></tr>
					<tr><td><label for="edemail">E-mail:</label></td><td><input type="email" id="edemail" name="edemail" class="form-control" /></td><td><label for="edtelefon">Telefon:</label></td><td><input type="tel" id="edtelefon" name="edtelefon" class="form-control" /></td></tr>
					<tr><td><label for="edlocalitate">Localitate:</label></td><td><input type="text" id="edlocalitate" name="edlocalitate" class="form-control" /></td><td><label for="edjudet">Jude&#x21B;:</label></td><td><select id="edjudet" name="edjudet" class="form-control">' . $judeteOptions . '</select></td></tr>
					<tr><td><label for="edstare">Stare:</label></td><td><select id="edstare" name="edstare" class="form-control"><option value="1">Activ</option><option value="2">Blocat</option></select></td><td><label for="eduser_moodle">User moodle:</label></td><td><input type="text" name="eduser_moodle" id="eduser_moodle" class="form-control" disabled /></td></tr>
					<tr><td><label for="edpremium">Premium:</label></td><td><input type="checkbox" id="edpremium" name="edpremium" /></td><td><label for="edcreat">Creat:</label></td><td><input type="text" name="edcreat" id="edcreat" class="form-control" disabled /></td></tr>
					<tr><td><label for="edadresa">Adres&#259;:</label></td><td><textarea id="edadresa" name="edadresa" class="form-control" rows="3" cols="30"></textarea></td><td><label for="edcomentarii">Comentarii:</label></td><td><textarea id="edcomentarii" name="edcomentarii" rows="3" cols="30" class="form-control"></textarea></td><td></td><td></td></tr>
					<tr><td><label for="edparola">Parol&#259;:</label></td><td><input type="password" id="edparola" name="edparola" class="form-control" /></td><td><label for="edcparola">Confirmare parol&#259;:</label></td><td><input type="password" id="edcparola" name="edcparola" class="form-control" /></td></tr>
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