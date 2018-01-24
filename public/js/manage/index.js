define(function (require) {
    var common = require('js/lib/common');
    var csrfToken = $('meta[name="csrf-token"]').attr('content');
    var main = {
        init: function () {
            this.bindEvent();
        },
        bindEvent: function () {
            var obj = $('.addClinic');
            var data = {};
            modal.open(obj, data);
        }

    };
    return main;
})