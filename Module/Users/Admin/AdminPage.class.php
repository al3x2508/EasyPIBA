<?php
namespace Module\Users\Admin;
use Model\Model;

class AdminPage extends \Controller\AdminPage {
	public function output() {
		$page = new \stdClass();
		$countriesOptions = '';
		$countries = new Model('countries');
		$countries = $countries->get();
		$page->title = __('Users');
		$page->h1 = __('Users');
		foreach($countries AS $country) $countriesOptions .= '<option value="' . $country->id . '">' . $country->name . '</option>'.PHP_EOL;
		$page->js = array('../vendor/datatables/datatables/media/js/jquery.dataTables.min.js','../vendor/datatables/datatables/media/js/dataTables.bootstrap.min.js','js/jsall.js','../Module/Users/Admin/users.js');
		$page->css = array('../vendor/datatables/datatables/media/css/dataTables.bootstrap.min.css');
		$page->content = '<div class="box">
	<div class="box-header"><h3 class="box-title">' . __('Users') . '</h3></div>
	<div class="box-body">
		<table id="data_table" class="table table-bordered table-hover">
			<thead>
				<tr><th>#<br /><input type="text" id="idf" class="tableFilter form-control" size="2"></th><th>' . __('Firstname') . '<br /><input type="text" size="10" id="firstnamef" class="tableFilter ui-autocomplete-input form-control" autocomplete="off"></th><th>' . __('Lastname') . '<br /><input type="text" size="10" id="lastnamef" class="tableFilter form-control" /></th><th>' . __('Country') . '<br /><select id="countryf" class="tableFilter form-control"><option value="0">' . __('Any') . '</option>' . $countriesOptions . '</select></th><th>' . __('Email') . '<br /><input type="text" id="emailf" class="tableFilter form-control"></th><th>' . __('Status') . '<br /><select id="statusf" class="tableFilter form-control"><option value="-1">' . __('Any') . '</option><option value="0">' . __('Not confirmed') . '</option><option value="1">' . __('Confirmed') . '</option><option value="2">' . __('Blocked') . '</option></select></th><th>' . __('Actions') . '</th></tr>
			</thead>
			<tbody>
			</tbody>
		</table>
	</div>
	<button id="add" class="btn btn-primary">' . __('Add') . '</button>
	<button class="btn btn-primary btn-export">Excel</button>
	<button class="btn btn-primary btn-export">PDF</button>
</div>
<div id="ppEdit" class="modal dialog">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="' . __('Close') . '">
					<span aria-hidden="true">×</span>
				</button>
				<h4 class="modal-title">' . __('Users') . '</h4>
			</div>
			<div class="modal-body" id="edtable">
				<div class="form-group">
					<label for="edfirstname">' . __('Firstname') . '</label>
					<input type="text" class="form-control" id="edfirstname" name="edfirstname" placeholder="' . __('Firstname') . '" />
                </div>
                <div class="form-group">
					<label for="edlastname">' . __('Lastname') . '</label>
					<input type="text" class="form-control" id="edlastname" name="edlastname" placeholder="' . __('Lastname') . '" />
                </div>
                <div class="form-group">
					<label for="edemail">' . __('Email') . '</label>
					<input type="email" class="form-control" id="edemail" name="edemail" placeholder="' . __('Email') . '" />
                </div>
                <div class="form-group">
					<label for="edphone">' . __('Phone') . '</label>
					<input type="text" class="form-control" id="edphone" name="edphone" placeholder="' . __('Phone') . '" />
                </div>
                <div class="form-group">
					<label for="edaddress">' . __('Address') . '</label>
					<textarea id="edaddress" name="edaddress" class="form-control" rows="3" cols="30" placeholder="' . __('Address') . '"></textarea>
                </div>
                <div class="form-group">
					<label for="edcity">' . __('City') . '</label>
					<input type="text" class="form-control" id="edcity" name="edcity" placeholder="' . __('City') . '" />
                </div>
                <div class="form-group">
					<label for="edstate">' . __('State') . '</label>
					<input type="text" class="form-control" id="edstate" name="edstate" placeholder="' . __('State') . '" />
                </div>
                <div class="form-group">
					<label for="edcountry">' . __('Country') . '</label>
					<select id="edcountry" name="edcountry" class="form-control">' . $countriesOptions . '</select>
                </div>
                <div class="form-group">
					<label for="edstatus">' . __('Status') . '</label>
					<select id="edstatus" name="edstatus" class="form-control"><option value="1">' . __('Active') . '</option><option value="2">' . __('Blocked') . '</option></select>
                </div>
                <div class="form-group">
					<label for="edpassword">' . __('Password') . '</label>
					<input type="password" class="form-control" id="edpassword" name="edpassword" placeholder="' . __('Password') . '" />
                </div>
                <div class="form-group">
					<label for="edconfirmPassword">' . __('Confirm password') . '</label>
					<input type="password" class="form-control" id="edconfirmPassword" name="edconfirmPassword" placeholder="' . __('Confirm password') . '" />
                </div>
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