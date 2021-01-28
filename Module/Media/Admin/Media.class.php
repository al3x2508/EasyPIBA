<?php
namespace Module\Media\Admin;

class Media
{
    public function __construct()
    {
        $this->title = __('Media');
        $this->h1 = __('Media');
        $this->js = array(
            '../vendor/datatables/datatables/media/js/jquery.dataTables.min.js',
            '../vendor/datatables/datatables/media/js/dataTables.bootstrap4.js',
            '../vendor/drmonty/datatables-responsive/js/dataTables.responsive.min.js',
            'js/jsall.js',
            'Module/Media/Admin/media.js'
        );
        $this->css = array(
            '../vendor/datatables/datatables/media/css/dataTables.bootstrap4.min.css',
            '../vendor/drmonty/datatables-responsive/css/dataTables.responsive.min.css',
            'Module/Media/Admin/media.css'
        );
        $this->content = '<div class="card">
	<div class="card-header"><h3 class="card-title">' . __('Media') . '</h3></div>
	<div class="card-body">
		<table id="data_table" class="table table-bordered table-hover">
			<thead>
				<tr><th>#<br /><input type="text" id="idf" class="tableFilter form-control" size="2"></th><th>' . __('Filename') . '</th><th>' . __('Preview') . '</th><th>' . __('Actions') . '</th></tr>
			</thead>
			<tbody>
			</tbody>
		</table>
	</div>
	<div class="card-footer">
	    <div class="btn-toolbar">
			<button id="add" class="btn btn-primary btn-sm"><i class="fa fa-plus"></i> ' . __('Add media') . '</button>
		</div>
	</div>
</div>
<div id="ppEdit" class="modal dialog">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">Media</h4>
				<button type="button" class="close" data-dismiss="modal" aria-label="' . __('Close') . '">
					<i class="fa fa-times"></i>
				</button>
			</div>
			<div class="modal-body" id="edtable">
				<form action="" method="post" id="imageUploadForm">
					<div class="form-group">
						<label for="edimage">' . __('Image') . '</label>
						<input type="file" class="form-control" id="edimage" name="edimage" placeholder="' . __('Image') . '" />
					</div>
				</form>
				<img id="imagePreview" src="" data-folder="" data-default="' . _LOGO_ . '" />
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default pull-left" data-dismiss="modal">' . __('Close') . '</button>
				<button type="button" class="btn btn-primary" id="save">' . __('Save') . '</button>
			</div>
		</div>
	</div>
</div>
<div id="preview" class="modal dialog">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-body">
                <img id="imgPreview" src="" />
            </div>
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