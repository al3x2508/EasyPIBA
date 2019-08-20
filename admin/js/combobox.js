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
            this.element.autocomplete(options);
            this._on(this.element, {
                autocompleteselect: function(event, ui) {
                    if(typeof ui.item.disabled !== 'undefined' && ui.item.disabled) {
                        event.preventDefault();
                        event.stopPropagation();
                        $(this.element).val("").attr('data-original-title', ui.item.value).attr('title', ui.item.value).tooltip('_fixTitle').tooltip({title: ui.item.value}).tooltip("show");
                    }
                    else {
                        $(this.element).attr('data-original-title', '').attr('title', '').tooltip('_fixTitle').tooltip({title: ''}).tooltip("show");
                        $(this.element).closest('.form-group').removeClass('has-error').find('.help-block').remove();
                        if (typeof _self.options.callback === "function") _self.options.callback(ui.item.id);
                    }
                },
                autocompletechange: "_removeIfInvalid"
            });
        },
        _removeIfInvalid: function( event, ui ) {
            event.preventDefault();
            event.stopPropagation();
            if(ui.item && typeof ui.item.disabled !== 'undefined' && ui.item.disabled) this.element.data('uiAutocomplete').selectedItem = null;
            if (ui.item || this.element.val() === '') return;
            var value = this.element.val();
            this.element.val("").attr('data-original-title', value + " " + jsstrings.novaluereturn).attr('title', value + " " + jsstrings.novaluereturn).tooltip({delay: { "show": 500, "hide": 100 }}).tooltip("show");
            this._delay(function() {
                this.element.tooltip("hide").attr("title", "");
            }, 2500);
        }
    });
})(jQuery);