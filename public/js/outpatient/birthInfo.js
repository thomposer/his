define(function (require) {
    var template = require('template');
    var jqprint = require('js/lib/jquery.jqprint-0.3');
    var birthInfoTpl = require('tpl/outpatient/birthInfo.tpl');
    var _self;
    var inspectSpecimen = '';
    var main = {
        init: function () {
            _self = this;
            this.bindEvent();
        },
        bindEvent: function () {
            _self.saveAndPrint();
            _self.saveForm();
//            if (inspectList != '') {
//                for (var o in inspectList) {
//                    if (inspectList[o].deliver == 1) {
//                        $("#inspect-oninspect label input[value=" + inspectList[o].id + "]").parent('label').append('<span class ="label-required">（外送）</span>');
//                    }
//                }
//            }
        },
        saveAndPrint: function () {
            $('body').off('click', '.save-birth-more').on('click', '.save-birth-more', function (e) {
                $('.save_type').val(2);
                $('.save-birth').click();
            })
        },
        saveForm: function () {
            template.config(escape, false);
                var isCommitted = false;//表单是否已经提交标识，默认为false
                $('#bornInfoForm').yiiAjaxForm({
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
                        var saveType = $('.save_type').val();
                        if (data.errorCode == 0) {
                            $('#ajaxCrudModal').modal('hide');
                            if (saveType == 2) {//只是保存
                                var birthInfo = data.birthInfo;
                                var triageInfo = data.triageInfo;
                                var spotInfo = data.spotInfo;
                                var allergy = data.allergy;
                                var spotConfig = data.spotConfig;
                                _self.printInfo(birthInfo, triageInfo, spotInfo,allergy,spotConfig);
                            }
                        } else {
                            isCommitted = false;
                            showInfo(data.msg, '220px', 2);
                        }
                    },
                });
        },
        printInfo: function (birthInfo, triageInfo, spotInfo,allergy,spotConfig) {
            console.log(triageInfo,'triageInfo');
            var logo_img = '';
            if(spotConfig.logo_shape == 1){
                logo_img = "clinic-img"
            }else{
                logo_img = "clinic-img-long"
            }
            var birthInfoHtml = template.compile(birthInfoTpl)({
                baseUrl: baseUrl,
                cdnHost: cdnHost,
                triageInfo: triageInfo,
                spotInfo: spotInfo,
                allergy: allergy,
                birthInfo: birthInfo,
                spotConfig : spotConfig,
                logo_img : logo_img,
            });
            $('#birth-info-print').html(birthInfoHtml);
            $('.birth-info-show').jqprint();
        }
    };
    return main;
})