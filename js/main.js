var jsstrings = {};
function blurfocus($el, dataAndEvents) {
    if ($el.val() && "" !== $el.val().trim() || dataAndEvents) {
        $el.closest(".input-hoshi").addClass("input-filled");
    } else {
        $el.closest(".input-hoshi").removeClass("input-filled");
    }
}
$(document).ready(function() {
   $(".language").click(function() {
       document.cookie = "language=" + $(this).data('language') + "; path=/";
   });
    $.getJSON("/js/en.json", function(data) {
        jsstrings = data;
        var jslang = document.documentElement.lang;
        if (jslang != 'en') {
            $.getJSON("/js/" + jslang + ".json", function (data) {
                $.extend(jsstrings, data);
            });
        }
    });
});
$(window).on('load', function() {
    $(".input__field").focus(function() {
        blurfocus($(this), true);
    }).blur(function() {
        blurfocus($(this));
    }).each(function() {
        blurfocus($(this));
    });
});
