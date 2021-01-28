<?php

namespace Module\Testimonials\Admin;

class AdminPage extends \Controller\AdminPage {
	public function output() {
		$page = new \stdClass();
		$page->title = __('Testimonials');
		$page->h1 = __('Testimonials');
		$page->js = array('../vendor/datatables/datatables/media/js/jquery.dataTables.min.js', '../vendor/datatables/datatables/media/js/dataTables.bootstrap4.min.js', '../vendor/ckeditor/ckeditor/ckeditor.js', 'js/jsall.js', 'Module/Products/Admin/dropzone.js', 'Module/Testimonials/Admin/testimonials.js');
		$page->css = array('../vendor/datatables/datatables/media/css/jquery.dataTables.min.css', '../vendor/datatables/datatables/media/css/dataTables.bootstrap4.min.css', 'dataTables.fontawesome.css', 'Module/Products/Admin/dropzone.css', 'Module/Products/Admin/builder.css');
		$statusOptions = array(-1 => __('Any'), 0 => __('Hidden'), 1 => __('Visible'));
		$page->content = '<div class="card">
	<div class="card-header">
		<div class="row">
			<div class="col-md-9"><h3 class="card-title">' . __('Testimonials') . '</h3></div>
			<div class="col-md-3">
				<a href="#" class="filter-datatable"><i class="fal fa-search"></i>' . __('Filters') . '</a>
			</div>
		</div>
	</div>
	<div class="card-body">
		<div class="row">
			<div class="col-12">
				<table id="data_table" class="table table-borderless table-hover table-sm w-100">
					<thead class="thead-light">
						<tr><th data-filtertype="text" data-filterid="idf">#</th><th data-filtertype="text" data-filterid="namef">' . __('Name') . '</th><th>' . __('Company') . '</th><th>' . __('Short text') . '</th><th data-filtertype="select" data-filterid="statusf" data-options=\'' . json_encode($statusOptions, JSON_FORCE_OBJECT) . '\'>Status</th><th>' . __('Actions') . '</th></tr>
					</thead>
					<tbody>
					</tbody>
				</table>
			</div>
		</div>
	</div>
	<div class="card-footer">
		<div class="btn-toolbar">
			<button id="add" class="btn btn-primary btn-sm"><i class="fal fa-plus"></i> ' . __('Add testimonial') . '</button>
		</div>
	</div>
</div>
<div id="ppEdit" class="modal dialog fade">
	<div class="modal-dialog modal-lg" style="width: 80vw; max-width: 80vw;">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">' . __('Testimonials') . '</h4>
				<button type="button" class="close" data-dismiss="modal" aria-label="' . __('Close') . '">
					<i class="fal fa-times"></i>
				</button>
			</div>
			<div class="modal-body" id="edtable">
				<div class="form-group">
					<div class="input-group">
						<input type="text" class="form-control" id="edname" name="edname" required />
						<label for="edname" class="control-label">' . __('Name') . '</label>
						<i class="bar"></i>
					</div>
                </div>
                <div class="form-group">
					<div class="input-group">
						<input type="text" class="form-control" id="edcompany" name="edcompany" />
						<label for="edcompany" class="control-label">' . __('Company') . '</label>
						<i class="bar"></i>
					</div>
                </div>
                <div class="form-group">
					<div class="input-group">
						<textarea id="edshort" name="edshort" class="form-control" rows="3" cols="30" required></textarea>
						<label for="edshort" class="control-label">' . __('Short text') . '</label>
						<i class="bar"></i>
					</div>
                </div>
                <div class="form-group">
					<div class="input-group">
						<textarea id="edcontent" name="edcontent" class="form-control" rows="20" cols="300" required></textarea>
						<label for="edcontent" class="control-label">' . __('Content') . '</label>
						<i class="bar"></i>
					</div>
                </div>
                <div class="mb-5">
    				<a href="#galleryModal" data-toggle="modal" data-target="#galleryModal" class="control-label">
    				    <img src="' . _FOLDER_URL_ . 'img/' . _LOGO_ . '" id="imagePreview" alt="Imagine articol" data-folder="testimonials" data-default="' . _LOGO_ . '" height="100" />
    				    <input type="hidden" name="edimage" id="edimage" value="" />
                    </a>
					<i class="bar"></i>
                </div>
                <div class="form-group">
					<div class="input-group">
						<select id="edstatus" name="edstatus" class="form-control"><option value="1" selected>' . __('Active') . '</option><option value="2">' . __('Blocked') . '</option></select>
						<label for="edstatus" class="control-label">' . __('Status') . '</label>
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
</div>
<div class="modal fade" tabindex="-1" role="dialog" id="galleryModal">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">' . ('Image gallery') . '</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<i class="fal fa-times"></i>
				</button>
			</div>
			<div class="modal-body">
				<div class="container-fluid">
					<div class="row">
						<div class="col-12">
							<form action="" class="dropzone dz-clickable" id="gallery-dropzone">
								<div class="dz-message d-flex flex-column">
									<i class="fal fa-cloud-upload text-muted"></i>
									Drag &amp; Drop here or click
								</div>
							</form>
						</div>
					</div>
				</div>
				<div id="gallery" class="container-fluid mt-4">
					<div class="row">
						
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary">' . ('Close') . '</button>
				<button type="button" class="btn btn-primary" disabled>' . ('Use image') . '</button>
			</div>
		</div>
	</div>
</div>
<div class="modal fade p-0" tabindex="-1" role="dialog" id="galleryview">
	<div class="modal-dialog modal-full m-0" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title"></h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<i class="fal fa-times"></i>
				</button>
			</div>
			<div class="modal-body">
				<div class="container-fluid">
					<div class="row">
						<div class="col-12">
							<img src="" />
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>';
		return $page;
	}
}