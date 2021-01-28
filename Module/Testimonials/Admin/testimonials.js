var jsonPage = 'Testimonials',
	aoColumns = [
		{ "mData": "id" },
		{ "mData": "name" },
		{ "mData": "company" },
		{ "mData": "short" },
		{ "mData": function (e) {
			return $('#statusf option[value="' + e.status + '"]').text();
		} },
		{ "mData": function() {
			return "<span class=\"actions btn btn-sm btn-outline-primary fal fa-edit\" title=\"" + jsstrings.edit + "\"></span>";
		} }
	];
Dropzone.autoDiscover = false;
$(function() {
	CKEDITOR.replace('edcontent', {
		allowedContent: true
	});
	uploader = new Dropzone("#gallery-dropzone", {
		url: adminfolder + "act/Media",
		paramName: "edimage",
		acceptedFiles: "image/*",
		previewTemplate: "<div class=\"col-12 col-md-4\">\n" +
			"\t\t\t\t\t\t\t<div class=\"img-thumbnail\">\n" +
			"\t\t\t\t\t\t\t\t<a href=\"#\">\n" +
			"\t\t\t\t\t\t\t\t\t<img src=\"#\" class=\"img-fluid\" />\n" +
			"\t\t\t\t\t\t\t\t\t<i class=\"fal fa-search-plus\"></i>\n" +
			"\t\t\t\t\t\t\t\t</a>\n" +
			"\t\t\t\t\t\t\t\t<span class=\"dz-remove btn btn-outline-danger fal fa-trash\" data-toggle=\"modal\" data-target=\"#confirm_delete\"></span>" +
			"\t\t\t\t\t\t\t</div>\n" +
			"\t\t\t\t\t\t</div>",
		previewsContainer: $('#gallery').children('.row').eq(0)[0],
		success: function (e, s) {
			$(e.previewElement).find('a').eq(0).attr('href', s.entity.folder + s.entity.filename).data('folder', s.entity.folder).data('filename', s.entity.filename);
			$(e.previewElement).find('img').eq(0).attr('src', s.entity.folder + s.entity.filename);
			$(e.previewElement).find(".dz-remove").eq(0).data("actid", s.entity.id).data('page', 'Media');
		}
	});

	$("#gallery").on('click', '.img-thumbnail', function (e) {
		if ($(e.target).hasClass('fa-trash')) {
			return;
		}
		e.preventDefault();
		e.stopPropagation();
		var _current = $(this).children('a').eq(0);
		if ($(e.target).hasClass('fa-search-plus')) {
			$("#gallery a.active").each(function () {
				if ($(this)[0] != _current[0]) $(this).removeClass('active');
			});
			_current.addClass('active');
			$("#galleryview").find(".modal-title").eq(0).text(_current.data('filename'));
			$("#galleryview").find("img").eq(0).attr("src", _current.attr("href"));
			$("#galleryview").modal('show');
			$("#galleryModal").find('.modal-footer').eq(0).children('.btn-primary').eq(0).prop('disabled', $("#gallery a.active").length == 0);
		} else {
			$("#gallery a.active").each(function () {
				if ($(this)[0] != _current[0]) $(this).removeClass('active');
			});
			_current.toggleClass('active');
			$("#galleryModal").find('.modal-footer').eq(0).children('.btn-primary').eq(0).prop('disabled', $("#gallery a.active").length == 0);
		}
	});
	$("#galleryModal").find('.modal-footer').eq(0).children('.btn-primary').eq(0).click(function () {
		var imgID = $("#gallery a.active img").eq(0).data('id'),
			imageFolder = $("#gallery a.active").eq(0).data('folder'),
			imageFilename = $("#gallery a.active").eq(0).data('filename');
		addPhoto({id: imgID, folder: imageFolder, filename: imageFilename});
		$("#galleryModal").modal('hide');
	});
	$("#galleryModal").find('.modal-footer').eq(0).children('.btn-secondary').eq(0).click(function () {
		$("#galleryModal").modal('hide');
	});
	loadGalleryImages();
});
function loadData(aoData) {
	var filters = {};
	$(aoData).each(function(i, val) {
		if(val.name == 'sEcho') filters.secho = val.value;
		else if(val.name == 'iDisplayStart') filters.start = val.value;
		else if(val.name == 'iDisplayLength') filters.length = val.value;
	});
	filters.filters = {};
	if($("#idf").val()!='') filters.filters['id'] = $("#idf").val();
	if($("#namef").val()!='') filters.filters['name'] = $("#namef").val();
	if($("#statusf").val()!='-1') filters.filters['status'] = $("#statusf").val();
	return filters;
}
function loadGalleryImages() {
	$("#gallery").children('.row').eq(0).empty();
	$.ajax({
		url: $("#mainjs").data('admindir') + 'json/Media',
		data: {
			length: 999
		},
		type: 'POST',
		dataType: 'json',
		success: function (data) {
			$.each(data, function (i, v) {
				if (v.type === 1) {
					$("#gallery").children('.row').eq(0).append(
						$("<div></div>").addClass("col-12 col-md-4").append(
							$("<div></div>").addClass("img-thumbnail").append(
								$("<a></a>").attr("href", folder + "uploads/" + v.filename).data('folder', (v.thumbfolder || "uploads/")).data('filename', v.filename).append(
									$("<img />").attr("src", folder + (v.thumbfolder || "uploads/") + v.filename).addClass("img-fluid").data('id', v.id)
								).append(
									$("<i></i>").addClass("fal fa-search-plus")
								)
							).append(
								$("<span></span>").addClass("dz-remove btn btn-outline-danger fal fa-trash").attr("data-toggle", "modal").attr("data-target", "#confirm_delete").data("actid", v.id).data('page', 'Media')
							)
						)
					)
				}
			});
		}
	});
}
function addPhoto(value) {
	$("#imagePreview").attr('src', folder + value.folder + value.filename);
	$("#edimage").val(value.filename).data('imgname', value.filename);
}