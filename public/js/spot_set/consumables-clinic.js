

define(function (require) {
    var common = require('js/lib/common');
    var select = require('plugins/select2/select2.full.min');
    var _self;
    var main = {
        init: function () {
            _self = this;
            this.bindEvent();
        },
        bindEvent: function () {
            _self.initSelect();
            _self.choseConsumables();
        },
        initSelect: function () {
            $('.select2').select2();
        },
        choseConsumables: function () {
            $('body').off('change', '#consumablesclinic-consumables_id').on('change', '#consumablesclinic-consumables_id', function () {
                var id = $(this).val();
                var info = consumablesList[id];
                _self.initFormData(info);
            });
            if(error == 1){
            	var id = $('#consumablesclinic-consumables_id').val();
            	var info = consumablesList[id];
                _self.initFormData(info);
            }
        },
        initFormData: function (info) {
            $('#consumablesclinic-product_name').val(info.product_name);
            $('#consumablesclinic-en_name').val(info.en_name);
            $('#consumablesclinic-type').val(info.type);
            $('#consumablesclinic-specification').val(info.specification);
            $('#consumablesclinic-unit').val(info.unit);
            $('#consumablesclinic-meta').val(info.meta);
            $('#consumablesclinic-manufactor').val(info.manufactor);
            $('#consumablesclinic-tag_name').val(info.tag_name);
            $('#consumablesclinic-remark').val(info.remark);
        },
    };
    return main;
})