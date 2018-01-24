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
            _self.choseCheck();
        },
        initSelect: function () {
            $('.select2').select2();
        },
        choseCheck: function () {
            $('body').off('change', '#checklistclinic-check_id').on('change', '#checklistclinic-check_id', function () {
                var id = $(this).val();
                var checkListClinic = checkList[id];
                _self.initFormData(checkListClinic);
            })
        },
        initFormData: function (checkListClinic) {
            console.log(checkListClinic);
            $('#checklistclinic-unit').val(checkListClinic.unit);
            $('#checklistclinic-meta').val(checkListClinic.meta);
            $('#checklistclinic-international_code').val(checkListClinic.international_code);
            $('#checklistclinic-tagname').val(checkListClinic.tagName);
            $('#checklistclinic-remark').val(checkListClinic.remark);
        }
    };
    return main;
})