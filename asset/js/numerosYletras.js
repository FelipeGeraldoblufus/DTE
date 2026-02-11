(function($) {
    $.fn.soloNumeros = function(p) {
        p = $.extend({
            root: "0123456789",
            permitir: "",
            permit_keycode: [225, 193, 201, 205, 211, 218, 233, 237, 243, 250]
        }, p);
        return this.each(
                function() {
                    $(this).on({
                        keypress: function(e) {
                            var key = e.which,
                                keye = e.keyCode,
                                tecla = String.fromCharCode(key).toLowerCase(),
                                letras = p.root + ((p.permitir!=undefined&&p.permitir!=null&&p.permitir!="")?p.permitir:"");
                            if (!existe_en_array(key, p.permit_keycode)) {
                                if (letras.indexOf(tecla) == -1 && keye != 9 && (key == 37 || keye != 37) && (keye != 39 || key == 39) && keye != 8 && (keye != 46 || key == 46) || key == 161) {
                                    e.preventDefault();
                                }
                            }
                        }
                    });
                }
        );
    };
    $.fn.soloLetras = function(p) {
        p = $.extend({
            root: "abcdefghijklmnñopqrstuvwxyzáéíóú",
            permitir: ""
        }, p);
        return this.each(function() {
            $(this).soloNumeros(p);
        });
    };
    $.fn.numerosYLetras = function(p) {
        p = $.extend({
            root: "abcdefghijklmnñopqrstuvwxyzáéiou0123456789",
            permitir: ""
        }, p);
        return this.each(function() {
            $(this).soloNumeros(p);
        });
    };
})(jQuery);
function existe_en_array(what, where) {
    var a = false;
    for (var i = 0; i < where.length; i++) {
        if (what == where[i]) {
            a = true;
            break;
        }
    }
    return a;
}