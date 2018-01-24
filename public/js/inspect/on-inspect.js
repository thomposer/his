

define(function (require) {
    var template = require('template');
    var inspectSpecimenTpl = require('tpl/inspect/inspectSpecimen.tpl');
    var JsBarcode = require('js/lib/JsBarcode.all.min');
    var _self;
    var inspectSpecimen = '';
    var main = {
        init: function () {
            _self = this;
            this.bindEvent();
        },
        bindEvent: function () {
            template.config(escape, false);
            var isCommitted = false;//表单是否已经提交标识，默认为false
            $('#on-inspect').yiiAjaxForm({
                beforeSend: function () {
                    if (isCommitted == false) {
                        isCommitted = true;//提交表单后，将表单是否已经提交标识设置为true
                        return true;//返回true让表单正常提交
                    } else {
                        return false;//返回false那么表单将不提交
                    }
                },
                complete: function () {

                },
                success: function (data) {

                    if (data.errorCode == 0) {
                        inspectSpecimen = data.specimenNumberInfo;
                        _self.prinLabel();
                        if (isCommitted == true) {
                            $('#ajaxCrudModal').modal('hide');
                            $.pjax.reload({container: '#crud-datatable-pjax', cache: false, timeout: 5000});  //Reload
                        }
                    } else {
                        isCommitted = false;
                        showInfo(data.msg, '180px', 2);
                    }
                },
            });
            if (inspectList != '') {
                for (var o in inspectList) {
                    if (inspectList[o].deliver == 1) {
                        $("#inspect-oninspect label input[value=" + inspectList[o].id + "]").parent('label').append('<span class ="label-required">（外送）</span>');
                    }
                }
            }
        },
        prinLabel: function () {
            var arr = inspectSpecimen;
            var specimenText = template.compile(inspectSpecimenTpl)({
                list: arr,
            });
            $('#specimen-print-container').html(specimenText);
            $('#specimen-print-container .barcode').each(function () {
                var barcode = $(this).attr('barcode');
                if (barcode != '') {
                    $(this).JsBarcode(barcode, {
//                        format: "CODE128",
                        displayValue: false,
                        height: 200,
                        width: 6,
                    });
                }
            });
            setTimeout(function () {
                window.print();
            }, 500);
//            window.print();
        }
    };
    return main;
})