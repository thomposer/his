

define(function (require) {
    var common = require('js/lib/common');
//    var select = require('plugins/select2/select2.full.min');
    var _self;
    var main = {
        init: function () {
            _self = this;
            this.bindEvent();
        },
        bindEvent: function () {
            _self.initSelect();
            _self.choseCure();
        },
        initSelect: function () {
            $('.select2').select2();
        },
        choseCure: function () {
            $('body').off('change', '#cliniccure-cure_id').on('change', '#cliniccure-cure_id', function () {
                var id = $(this).val();
                var cure = cureList[id];
                _self.initFormData(cure);
            });
            if(error == 1){
            	var id = $('#cliniccure-cure_id').val();
            	var cure = cureList[id];
                _self.initFormData(cure);
            }
        },
        initFormData: function (cure) {
            $('#cliniccure-unit').val(cure.unit);
            $('#cliniccure-meta').val(cure.meta);
            $('#cliniccure-international_code').val(cure.international_code);
            $('#cliniccure-tag_name').val(cure.tag_name);
            $('#cliniccure-remark').val(cure.remark);
        },
    };
    return main;
})