<?php

namespace Module\Administrators\Admin;

use Model\Model;

class Admins
{
    private const TITLE = 'Edit administrators';
    public string $title;
    public string $h1;
    /**
     * @var array|string[]
     */
    public array $js;
    /**
     * @var array|string[]
     */
    public array $css;
    public string $content;

    public function __construct()
    {
        $this->title = __(self::TITLE);
        $this->h1 = __(self::TITLE);
        $this->js = array(
            '../node_modules/datatables.net/js/jquery.dataTables.min.js',
            '../node_modules/datatables.net-bs5/js/dataTables.bootstrap5.min.js',
            '../node_modules/datatables.net-responsive/js/dataTables.responsive.min.js',
            '../node_modules/datatables.net-responsive-bs5/js/responsive.bootstrap5.min.js',
            'Module/Administrators/Admin/administrators.js'
        );
        $this->css = array(
            '../node_modules/select2-bootstrap-5-theme/dist/select2-bootstrap-5-theme.min.css',
            '../node_modules/datatables.net-bs5/css/dataTables.bootstrap5.min.css',
            '../node_modules/datatables.net-responsive-bs5/css/responsive.bootstrap5.min.css',
        );
        $htmlPerms = '';
        $permissions = new Model('permissions');
        $permissions = $permissions->get();
        foreach ($permissions as $permission) {
            $htmlPerms .= '<div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" name="edpermission['.$permission->id.']"
                    id="edpermission['.$permission->id.']" />
                <label class="form-check-label" for="edpermission['.$permission->id.']">'.$permission->name.'</label>
            </div>';
        }
        $statusOptions = array(
            -1  => __('Any'),
            1  => __('Active'),
            2  => __('Blocked')
        );
        $jsonStatusOptions = json_encode($statusOptions, JSON_FORCE_OBJECT);
        $this->content = <<<HTML
<div class="card">
    <div class="card-header">
        <div class="row">
            <div class="col-md-9">
                <h3 class="card-title">$this->title</h3>
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
                    <th data-filtertype="text" data-filterid="namef">{__Name}</th>
                    <th data-filtertype="text" data-filterid="usernamef">{__Username}</th>
                    <th data-filtertype="select" data-filterid="statusf"
                        data-options='$jsonStatusOptions'>{__Status}</th>
                    <th>{__Actions}</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div>
    <div class="card-footer">
        <button id="add" class="btn btn-primary">{__Add}</button>
    </div>
</div>
<div id="ppEdit" class="modal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">{__Edit administrator}</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{__Close}"></button>
            </div>
            <div class="modal-body" id="edtable">
                <div class="form-group">
                    <div class="input-group">
                        <input type="text" class="form-control" id="edname"
                            name="edname" placeholder="{__Name}" />
                        <label for="edname" class="control-label">{__Name}</label>
                        <i class="bar"></i>
                    </div>
                </div>
                <div class="form-group">
                    <div class="input-group">
                        <input type="text" class="form-control" id="edusername"
                            name="edusername" placeholder="{__Username}" />
                        <label for="edusername" class="control-label">{__Username}</label>
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
                        <select id="edstatus" name="edstatus" class="form-control">
                            <option value="1">{__Active}</option>
                            <option value="2">{__Blocked}</option>
                        </select>
                        <label for="edstatus" class="control-label">{__Status}</label>
                        <i class="bar"></i>
                    </div>
                </div>
                $htmlPerms
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default pull-left" data-bs-dismiss="modal">{__Close}</button>
                <button type="button" class="btn btn-primary" id="save">{__Save}</button>
            </div>
        </div>
    </div>
</div>
HTML;
    }
}
