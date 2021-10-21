<?php

namespace Module\Pages\Admin;

use Model\Model;
use Util;

class Menu
{
    public function __construct()
    {
        $this->title = __('Edit menu');
        $this->h1 = __('Edit menu');
        $this->js = array('../assets/js/jquery.nestedSortable.js', 'js/jsall.js', 'Module/Pages/Admin/menu.js');
        $this->css = array('Module/Pages/Admin/menu.css');
        $languages = new Model('languages');
        $languages->order('name ASC');
        $languages = $languages->get();
        $languageOptions = [0 => __('Any')];
        foreach ($languages as $language) {
            $selected = ($language->code == _DEFAULT_LANGUAGE_);
            $languageOptions[$language->code] = [$language->name, $selected];
        }
        $this->content = '<div class="card">
	<div class="card-header">
		<div class="row">
			<div class="col-12 col-md-4">
				' . __('Language') . '<br /><select class="form-control" id="languagef">' . Util::arrayToOptions(
                $languageOptions
            ) . '</select>
			</div>
		</div>
	</div>
	<div class="card-body">
		<div class="container-fluid">
			<div class="row row-eq-height">
				<div class="col-12 col-md-6">
					<ol class="sortable" id="available"></ol>
				</div>
				<div class="col-12 col-md-6">
					<ol class="sortable" id="nestable"></ol>
				</div>
			</div>
		</div>
	</div>
	<div class="card-footer">
		<div class="btn-toolbar">
			<button id="customSave" class="btn btn-primary">' . __('Save') . '</button>
		</div>
	</div>
</div>';
    }

    /**
     * @param $key
     * @param $value
     */
    public function __set($key, $value)
    {
        $this->$key = $value;
    }
}