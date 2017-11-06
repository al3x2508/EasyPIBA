var jsonPage = 'Pages';
$(function() {
	var loadMenu = function() {
		$('#available').empty();
		$('#nestable').empty();
		$.getJSON('json/' + jsonPage, {menu: 1, language: $('.select2').val()}, function (data) {
			$.each(data, function (key, val) {
				if (val.menu_text) {
					if (val.menu_order == 0) {
						$('#available').append(
							$("<li></li>").attr("id", "list_" + val.id).data("id", val.id).append(
								$("<div></div>").append($("<span></span>").addClass("disclose").append($("<span></span>"))).append(document.createTextNode((val.menu_text)))
							)
						)
					}
					else {
						if(val.menu_parent == 0) $('#nestable').append(
							$("<li></li>").attr("id", "list_" + val.id).data("id", val.id).append(
								$("<div></div>").append($("<span></span>").addClass("disclose").append($("<span></span>"))).append(document.createTextNode((val.menu_text)))
							)
						);
						else {
							if($('#list_' + val.menu_parent).children('ol').length == 0) $('#list_' + val.menu_parent).append($('<ol></ol>'));
							$('#list_' + val.menu_parent).children('ol').eq(0).append(
								$("<li></li>").attr("id", "list_" + val.id).data("id", val.id).append(
									$("<div></div>").append($("<span></span>").addClass("disclose").append($("<span></span>"))).append(document.createTextNode((val.menu_text)))
								)
							);
						}
					}
				}
			});
		});
	};
	loadMenu();
	$("#available, #nestable").nestedSortable({
		forcePlaceholderSize: true,
		handle: 'div',
		helper: 'clone',
		items: 'li',
		placeholder: 'placeholder',
		tolerance: 'pointer',
		toleranceElement: '> div',
		connectWith: ".sortable",
		isTree: true,
		expandOnHover: 700,
		startCollapsed: true
	}).disableSelection();
	$('#customSave').click(function() {
		$.post("act/" + jsonPage, {menu: $('#nestable').nestedSortable('toArray', {startDepthCount: 0}), language: $('.select2').val()}, function() {
			loadMenu();
		});
	});
	$('.select2').select2({
		width: '200px'
	}).on('select2:select', function () {
		loadMenu();
	});
});