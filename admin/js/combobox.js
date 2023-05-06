(function($) {
	$.widget("ui.combobox", {
		_create: function() {
			var _self = this,
				options = $.extend({}, this.options, {
					minLength: 0,
					callback: '',
					source: function(request, response, element) {
						if (request.term.length < _self.options.minLength) response([]);
						else {
							if (typeof _self.options.source === "function") _self.options.source(request, response, $(_self.element));
							else if (typeof _self.options.source === "string") {
								$.ajax({
									url: _self.options.source,
									data: request,
									dataType: "json",
									success: function(data, status) {
										response(data);
									},
									error: function() {
										response([]);
									}
								});
							}
						}
					}
				});
			this.element.autocomplete(options).uitooltip({tooltipClass: "ui-state-highlight"});
			this._on(this.element, {
				autocompleteselect: function(event, ui) {
					$(this.element).closest('.form-group').removeClass('has-error').find('.help-block').remove();
					if(typeof _self.options.callback === "function") _self.options.callback(ui.item.id);
				},
				autocompletechange: "_removeIfInvalid"
			});
		},
		_removeIfInvalid: function( event, ui ) {
			event.preventDefault();
			event.stopPropagation();
			if (ui.item || this.element.val() === '') return;
			var value = this.element.val();
			this.element
				.val("")
				.attr("title", value + " " + jsstrings.novaluereturn)
				.uitooltip("open");
			this._delay(function() {
				this.element.uitooltip("close").attr("title", "");
			}, 2500);
		}
	});
})(jQuery);