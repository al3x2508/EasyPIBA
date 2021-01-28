<?php

namespace Module\Users\Admin;

use Model\Model;
use Utils\Util;

class AdminPage extends \Controller\AdminPage {
	public function output() {
		$page = new \stdClass();
		$countriesOptions = [0 => __('Any')];
		$countries = new Model('countries');
		$countries = $countries->get();
		$page->title = __('Users');
		foreach ($countries AS $country) $countriesOptions[$country->id] = $country->name;
		$statusOptions = array(-1 => __('Any'), 0 => __('Not confirmed'), 1 => __('Confirmed'), 2 => __('Blocked'));
		$page->js = array('../vendor/datatables/datatables/media/js/jquery.dataTables.min.js', '../vendor/datatables/datatables/media/js/dataTables.bootstrap4.min.js', 'js/jsall.js', 'Module/Users/Admin/users.js');
		$page->css = array('../vendor/datatables/datatables/media/css/jquery.dataTables.min.css', '../vendor/datatables/datatables/media/css/dataTables.bootstrap4.min.css', 'dataTables.fontawesome.css');
		$page->content = '<div class="card">
	<div class="card-header">
		<div class="row">
			<div class="col-md-9"><h3 class="card-title">' . __('Users') . '</h3></div>
			<div class="col-md-3">
				<a href="#" class="filter-datatable"><i class="fa fa-search"></i>' . __('Filters') . '</a>
			</div>
		</div>
	</div>
	<div class="card-body">
		<div class="row">
			<div class="col-12">
				<table id="data_table" class="table table-borderless table-hover table-sm w-100">
					<thead class="thead-light">
						<tr><th data-filtertype="text" data-filterid="idf">#</th><th data-filtertype="text" data-filterid="firstnamef">' . __('Firstname') . '</th><th data-filtertype="text" data-filterid="lastnamef">' . __('Lastname') . '</th><th data-filtertype="select" data-filterid="countryf" data-options=\'' . json_encode($countriesOptions, JSON_FORCE_OBJECT) . '\'>' . __('Country') . '</th><th data-filtertype="text" data-filterid="emailf">' . __('Email') . '</th><th data-filtertype="select" data-filterid="statusf" data-options=\'' . json_encode($statusOptions, JSON_FORCE_OBJECT) . '\'>' . __('Status') . '</th><th>' . __('Actions') . '</th></tr>
					</thead>
					<tbody>
					</tbody>
				</table>
			</div>
		</div>
	</div>
	<div class="card-footer">
		<div class="btn-toolbar">
			<button id="add" class="btn btn-primary btn-sm"><i class="fa fa-plus"></i> ' . __('Add user') . '</button>
			<button class="btn btn-secondary btn-export btn-sm" data-type="excel"><i class="fa fa-file-excel"></i> ' . __('Export to Excel') . '</button>
			<button class="btn btn-secondary btn-export btn-sm" data-type="pdf"><i class="fa fa-file-pdf"></i> ' . __('Export to PDF') . '</button>
		</div>
	</div>
</div>
<div id="ppEdit" class="modal dialog fade">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">' . __('Users') . '</h4>
				<button type="button" class="close" data-dismiss="modal" aria-label="' . __('Close') . '">
					<i class="fa fa-times"></i>
				</button>
			</div>
			<div class="modal-body" id="edtable">
				<div class="form-group">
					<div class="input-group">
						<input type="text" class="form-control" id="edfirstname" name="edfirstname" required />
						<label for="edfirstname" class="control-label">' . __('Firstname') . '</label>
						<i class="bar"></i>
					</div>
                </div>
                <div class="form-group">
                	<div class="input-group">
						<input type="text" class="form-control" id="edlastname" name="edlastname" required />
						<label for="edlastname" class="control-label">' . __('Lastname') . '</label>
						<i class="bar"></i>
					</div>
				</div>
                <div class="form-group">
                	<div class="input-group">
						<input type="email" class="form-control" id="edemail" name="edemail" required />
						<label for="edemail" class="control-label">' . __('Email') . '</label>
						<i class="bar"></i>
					</div>
				</div>
                <div class="form-group">
                	<div class="input-group">
						<input type="text" class="form-control" id="edphone" name="edphone" />
						<label for="edphone" class="control-label">' . __('Phone') . '</label>
						<i class="bar"></i>
					</div>
				</div>
                <div class="form-group">
                	<div class="input-group">
						<textarea id="edaddress" name="edaddress" rows="3" cols="30"></textarea>
						<label for="edaddress" class="control-label">' . __('Address') . '</label>
						<i class="bar"></i>
					</div>
				</div>
                <div class="form-group">
                	<div class="input-group">
						<input type="text" class="form-control" id="edcity" name="edcity" />
						<label for="edcity" class="control-label">' . __('City') . '</label>
						<i class="bar"></i>
					</div>
				</div>
                <div class="form-group">
                	<div class="input-group">
						<input type="text" class="form-control" id="edstate" name="edstate" />
						<label for="edstate" class="control-label">' . __('State') . '</label>
						<i class="bar"></i>
					</div>
				</div>
                <div class="form-group">
                	<div class="input-group">
						<select id="edcountry" name="edcountry">' . Util::arrayToOptions($countriesOptions, true) . '</select>
						<label for="edcountry" class="control-label">' . __('Country') . '</label>
						<i class="bar"></i>
					</div>
				</div>
                <div class="form-group">
                	<div class="input-group">
						<select id="edstatus" name="edstatus">' . Util::arrayToOptions($statusOptions, true) . '</select>
						<label for="edstatus" class="control-label">' . __('Status') . '</label>
						<i class="bar"></i>
					</div>
				</div>
                <div class="form-group">
                	<div class="input-group">
						<input type="password" class="form-control" id="edpassword" name="edpassword" />
						<label for="edpassword" class="control-label">' . __('Password') . '</label>
						<i class="bar"></i>
					</div>
				</div>
                <div class="form-group">
                	<div class="input-group">
						<input type="password" class="form-control" id="edconfirmPassword" name="edconfirmPassword" />
						<label for="edconfirmPassword" class="control-label">' . __('Confirm password') . '</label>
						<i class="bar"></i>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default pull-left" data-dismiss="modal">' . __('Close') . '</button>
				<button type="button" class="btn btn-outline-primary" id="save">' . __('Save') . '</button>
			</div>
		</div>
	</div>
</div>';
		return $page;
	}
}