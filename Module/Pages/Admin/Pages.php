<?php

namespace Module\Pages\Admin;

use Model\Model;
use stdClass;

class Pages extends \Controller\AdminPage
{
    public static function output(): stdClass
    {
        $page = new stdClass();
        $page->title = __('Edit pages');
        $page->h1 = __('Edit pages');
        $languages = new Model('languages');
        $languages->order('name ASC');
        $languages = $languages->get();
        $languagesOptions = '';
        $languagesOptionsArray = [0 => __('Any')];
        foreach ($languages as $language) {
            $selected = ($language->code == $_ENV['DEFAULT_LANGUAGE'])
                ? ' selected' : '';
            $languagesOptions .= "<option value=\"$language->code\"$selected>"
                .$language->name."</option>";
            $languagesOptionsArray[$language->code] = $language->name;
        }
        $jsonLanguagesOptions = self::jsonOptions($languagesOptionsArray);
        $page->js = array(
            '../node_modules/datatables.net/js/jquery.dataTables.min.js',
            '../node_modules/datatables.net-bs5/js/dataTables.bootstrap5.min.js',
            '../node_modules/datatables.net-responsive/js/dataTables.responsive.min.js',
            '../node_modules/datatables.net-responsive-bs5/js/responsive.bootstrap5.min.js',
            '../node_modules/select2/dist/js/select2.min.js',
            '../node_modules/@ckeditor/ckeditor5-build-classic/build/ckeditor.js',
            'js/app.js',
            'Module/Pages/Admin/bootstrap3-typeahead.min.js',
            'Module/Pages/Admin/bootstrap-tagsinput.min.js',
            'Module/Pages/Admin/pages.js'
        );
        $page->css = array(
            '../node_modules/select2-bootstrap-5-theme/dist/select2-bootstrap-5-theme.min.css',
            '../node_modules/select2/dist/css/select2.min.css',
            '../node_modules/datatables.net-bs5/css/dataTables.bootstrap5.min.css',
            '../node_modules/datatables.net-responsive-bs5/css/responsive.bootstrap5.min.css',
            'Module/Pages/Admin/bootstrap-tagsinput.css'
        );
        $page->content = <<<HTML
<div class="card">
    <div class="card-header">
        <div class="row">
            <div class="col-md-9">
                <h3 class="card-title">{__Edit pages}</h3>
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
                    <th data-filtertype="text" data-filterid="urlf">{__URL}</th>
                    <th data-filtertype="select2" data-filterid="languagef"
                        data-options='$jsonLanguagesOptions'>{__Language}</th>
                    <th data-filtertype="text" data-filterid="titlef">{__Title}</th>
                    <th data-filtertype="text" data-filterid="menutextf">{__Menu text}</th>
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
    <div class="modal-dialog modal-xl" style="width: 80vw;">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">{__Edit pages}</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{__Close}"></button>
            </div>
            <div class="modal-body" id="edtable">
                <ul class="nav nav-tabs">
                    <li class="nav-item" role="presentation">
                        <a class="nav-link active" id="pageSettingsLink" data-bs-toggle="tab"
                            href="#pageSettings" role="tab" aria-controls="pageSettings"
                            aria-selected="true">{__Page settings}
                        </a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a class="nav-link" id="pageContentLink" data-bs-toggle="tab"
                            href="#pageContent" role="tab" aria-controls="pageContent"
                            aria-selected="false">{__Page content}
                        </a>
                    </li>
                </ul>
                <div class="tab-content mt-3">
                    <div class="tab-pane fade show active" id="pageSettings" role="tabpanel"
                        aria-labelledby="pageSettingsLink">
                        <div class="form-group">
                            <div class="input-group">
                                <select id="edlanguage" name="edlanguage" class="form-control">
                                    $languagesOptions
                                </select>
                                <label for="edlanguage" class="control-label">{__Language}</label>
                                <i class="bar"></i>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="input-group">
                                <input type="text" class="form-control" id="edurl"
                                    name="edurl" placeholder="{__URL}" />
                                <label for="edurl" class="control-label">{__URL}</label>
                                <i class="bar"></i>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="input-group">
                                <input type="text" class="form-control" id="edtitle"
                                    name="edtitle" placeholder="{__Title}" />
                                <label for="edtitle" class="control-label">{__Title}</label>
                                <i class="bar"></i>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="input-group">
                                <input type="text" class="form-control" id="eddescription"
                                    name="eddescription" placeholder="{__Description}" />
                                <label for="eddescription" class="control-label">{__Description}</label>
                                <i class="bar"></i>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="input-group">
                                <input type="text" class="form-control" id="edkeywords"
                                    name="edkeywords" placeholder="{__Keywords}" />
                                <label for="edkeywords" class="control-label">{__Keywords}</label>
                                <i class="bar"></i>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="input-group">
                                <input type="text" class="form-control" id="edh1"
                                    name="edh1" placeholder="{__H1}" />
                                <label for="edh1" class="control-label">{__H1}</label>
                                <i class="bar"></i>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="input-group">
                                <input type="text" class="form-control" id="edmenu_text"
                                    name="edmenu_text" placeholder="{__Menu text}" />
                                <label for="edmenu_text" class="control-label">{__Menu text}</label>
                                <i class="bar"></i>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="input-group">
                                <input type="text" class="form-control" id="edsubmenu_text"
                                    name="edsubmenu_text" placeholder="{__Submenu text}" />
                                <label for="edsubmenu_text" class="control-label">{__Submenu text}</label>
                                <i class="bar"></i>
                            </div>
                        </div>
                        <div class="form-group form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="edvisible"
                                id="edvisible" />
                            <label class="form-check-label" for="edvisible">{__Visible}</label>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="pageContent" role="tabpanel"
                        aria-labelledby="pageContentLink">
                        <div class="form-group">
                            <div class="input-group">
                                <textarea id="edcontent" name="edcontent" class="form-control" rows="20" cols="300"
                                    placeholder="{__Content}"></textarea>
                                <label for="edcontent" class="control-label">{__Submenu text}</label>
                                <i class="bar"></i>
                            </div>
                        </div>
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
