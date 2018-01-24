define(function (require) {
    var common = require('js/lib/common');
    var main = {
        init: function () {
            main.bind();
        },
        bind: function () {
            $('.card-center input').attr({disabled: true});
            $('.input-disable').attr({disabled: true});
            if (f_status == 2) {
                $('.form-control').attr({disabled: true});
            }
            jsonFormInit = $("form").serialize();  //为了表单验证
        }
    };

    return main;
});

