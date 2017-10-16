<?php
$page_title = "Editare meniu";
$js = array('plugins/nestedSortable/jquery.nestedSortable.js','js/jsall.js','js/meniu.js');
$css = array('plugins/nestedSortable/jquery.nestedSortable.css','css/meniu.css');
$content = '<div class="box">
	<div class="box-body">
		<ol class="sortable" id="available"></ol>
		<ol class="sortable" id="nestable"></ol>
		<div class="clearfix"></div>
	</div>
	<button id="salveaza" class="btn btn-primary">Salveaz&#259;</button>
</div>
';
?>