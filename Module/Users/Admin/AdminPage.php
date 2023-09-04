<?php

namespace Module\Users\Admin;

use Model\Countries;
use Model\Model;

class AdminPage extends \Controller\AdminPage
{
    public function output(): \stdClass
    {
        $page = new \stdClass();
        $countriesOptions = '';
        $countries = new Countries();
        $countries = $countries->get();
        $page->title = __('Users');
        $page->h1 = __('Users');
        $countriesOptionsArray = [0 => __('Any')];
        $statuses = [
            -1 => __('Any'),
            0  => __('Not confirmed'),
            1  => __('Confirmed'),
            2  => __('Blocked')
        ];
        foreach ($countries as $country) {
            $countriesOptions .= '<option value="'.$country->id.'">'
                .$country->name.'</option>'.PHP_EOL;
            $countriesOptionsArray[$country->id] = $country->name;
        }
        $jsonCountriesOptions = self::jsonOptions($countriesOptionsArray);
        $jsonStatusesOptions = self::jsonOptions($statuses);
        $page->js = array(
            '../node_modules/datatables.net/js/jquery.dataTables.min.js',
            '../node_modules/datatables.net-bs5/js/dataTables.bootstrap5.min.js',
            '../node_modules/select2/dist/js/select2.min.js',
            '../node_modules/datatables.net-responsive/js/dataTables.responsive.min.js',
            '../node_modules/datatables.net-responsive-bs5/js/responsive.bootstrap5.min.js',
            'Module/Users/Admin/users.js'
        );
        $page->css = array(
            '../node_modules/select2-bootstrap-5-theme/dist/select2-bootstrap-5-theme.min.css',
            '../node_modules/select2/dist/css/select2.min.css',
            '../node_modules/datatables.net-bs5/css/dataTables.bootstrap5.min.css',
            '../node_modules/datatables.net-responsive-bs5/css/responsive.bootstrap5.min.css'
        );
        $page->content = <<<HTML
<div class="card">
    <div class="card-header">
        <div class="row">
            <div class="col-md-9">
                <h3 class="card-title">{__Users}</h3>
            </div>
            <div class="col-md-3">
                <a href="#" class="filter-datatable"><i class="fa fa-search"></i> {__Filters}</a>
            </div>
        </div>
    </div>
    <div class="card-body">
        <table id="data_table" class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th data-filtertype="text" data-filterid="idf">#</th>
                    <th data-filtertype="text" data-filterid="firstnamef"
                    data-autocomplete="firstname">{__Firstname}</th>
                    <th data-filtertype="text" data-filterid="lastnamef">{__Lastname}</th>
                    <th data-filtertype="select2" data-filterid="countryf"
                        data-options='$jsonCountriesOptions'>{__Country}</th>
                    <th data-filtertype="text" data-filterid="emailf">{__Email}</th>
                    <th data-filtertype="select" data-filterid="statusf"
                        data-options='$jsonStatusesOptions'>{__Status}</th>
                    <th>{__Actions}</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div>
    <div class="card-footer">
        <button id="add" class="btn btn-primary">{__Add}</button>
        <button class="btn btn-primary btn-export">Excel</button>
        <button class="btn btn-primary btn-export">PDF</button>
    </div>
</div>
<div id="ppEdit" class="modal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">{__Users}</h4>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="{__Close}">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="edtable">
                <div class="form-group">
                    <div class="input-group">
                        <input type="text" class="form-control" id="edfirstname"
                        name="edfirstname" placeholder="{__Firstname}" />
                        <label for="edfirstname" class="control-label">{__Firstname}</label>
                        <i class="bar"></i>
                    </div>
                </div>
                <div class="form-group">
                    <div class="input-group">
                        <input type="text" class="form-control" id="edlastname"
                        name="edlastname" placeholder="{__Lastname}" />
                        <label for="edlastname" class="control-label">{__Lastname}</label>
                        <i class="bar"></i>
                    </div>
                </div>
                <div class="form-group">
                    <div class="input-group">
                        <input type="email" class="form-control" id="edemail"
                        name="edemail" placeholder="{__Email}" />
                        <label for="edemail" class="control-label">{__Email}</label>
                        <i class="bar"></i>
                    </div>
                </div>
                <div class="form-group">
                    <div class="input-group">
                        <input type="text" class="form-control" id="edphone"
                        name="edphone" placeholder="{__Phone}" />
                        <label for="edphone" class="control-label">{__Phone}</label>
                        <i class="bar"></i>
                    </div>
                </div>
                <div class="form-group">
                    <div class="input-group">
                        <textarea id="edaddress" name="edaddress" class="form-control"
                        rows="3" cols="30" placeholder="{__Address}"></textarea>
                        <label for="edaddress" class="control-label">{__Address}</label>
                        <i class="bar"></i>
                    </div>
                </div>
                <div class="form-group">
                    <div class="input-group">
                        <input type="text" class="form-control" id="edcity" name="edcity" placeholder="{__City}" />
                        <label for="edcity" class="control-label">{__City}</label>
                        <i class="bar"></i>
                    </div>
                </div>
                <div class="form-group">
                    <div class="input-group">
                        <input type="text" class="form-control" id="edstate" name="edstate" placeholder="{__State}" />
                        <label for="edstate" class="control-label">{__State}</label>
                        <i class="bar"></i>
                    </div>
                </div>
                <div class="form-group">
                    <div class="input-group">
                        <select id="edcountry" name="edcountry" class="form-control">
                            $countriesOptions
                        </select>
                        <label for="edcountry" class="control-label">{__Country}</label>
                        <i class="bar"></i>
                    </div>
                </div>
                <div class="form-group">
                    <div class="input-group">
                        <select id="edstatus" name="edstatus" class="form-control">
                            <option value="1">{__Active}</option>
                            <option value="2">{__Blocked}</option>
                        </select>
                        <label for="edstatus" class="control-label">{__Status}</label>
                        <i class="bar"></i>
                    </div>
                </div>
                <div class="form-group">
                    <div class="input-group">
                        <input type="password" class="form-control" id="edpassword"
                        name="edpassword" placeholder="{__Password}" />
                        <label for="edpassword" class="control-label">{__Password}</label>
                        <i class="bar"></i>
                    </div>
                </div>
                <div class="form-group">
                    <div class="input-group">
                        <input type="password" class="form-control" id="edconfirmPassword"
                        name="edconfirmPassword" placeholder="{__Confirm password}" />
                        <label for="edconfirmPassword" class="control-label">{__Confirm password}</label>
                        <i class="bar"></i>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default pull-left" data-bs-dismiss="modal">{__Close}</button>
                <button type="button" class="btn btn-primary" id="save">{__Save}</button>
            </div>
        </div>
    </div>
</div>
HTML;
        return $page;
    }
}
