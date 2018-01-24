define(function (require) {
    var allergy = require('js/triage/allergy');
    var firstCheck = require('js/outpatient/firstCheck');
    var template = require('template');
    var prinkTpl = require('tpl/outpatient/orthodonticsReturnvisit.tpl');
    var _self;
    var main = {
        init: function () {
            allergy.initAllergyBtn();
            firstCheck.initFirstCheckBtn();
            this.bindEvent();
            _self = this;
        },
        bindEvent: function () {
            if (1 == orthodonticsReturnvisitHasSave) {
                console.log(22222);
                $('#orthodonticsReturnvisitForm .form-control').attr({'disabled': true});
                $('#orthodonticsReturnvisitForm [type=radio]').attr({'disabled': true});
                $('#orthodonticsReturnvisitForm .btn-case-template').hide();
                $('#orthodonticsReturnvisitForm .btn-from-delete-add').hide();
                $('#orthodonticsReturnvisitForm .input-group-btn-custom').hide();
                $('#orthodonticsReturnvisitForm .kv-file-remove').hide();
                console.log($('#orthodonticsReturnvisitForm .kv-file-remove'));
            }
            
            $('#orthodonticsReturnvisitForm .orthodontics-returnvisit-print').unbind('click').click( function () {
                var recordId = $(this).attr('data-value');
                _self.orthodonticsReturnvisitPrint(recordId);
            });
            
            $('#orthodonticsReturnvisitForm').yiiAjaxForm({
                beforeSend: function () {
                    var status = 0;
                    if ($('#orthodonticsReturnvisitForm input[name="OrthodonticsReturnvisitRecord[hasAllergy]"]:checked').val() == 2) {
                        var parentObj = $('#orthodonticsReturnvisitForm').find('.allergy-form');
                        status = allergy.allergyValidity(parentObj);
                    }

                    $('#orthodonticsReturnvisitForm .select-first-check').each(function () {
                        var val = $(this).val();
                        if (val == 1) {
                            var contentId = $(this).parents('.first-check-type').siblings('.first-check-text').find('.CheckCodeSel').val();
                            if (contentId == 0) {
                                $(this).parents('.first-check-type').siblings('.first-check-text').find('.first-check-right').addClass('has-error').find('.help-block:last').text('初步诊断不能为空');
                                status = 1;
                            }
                        } else {
                            var contentText = $(this).parents('.first-check-type').siblings('.first-check-text').find('.first-check-custom').val();
                            if (contentText == '') {
                                $(this).parents('.first-check-type').siblings('.first-check-text').find('.first-check-right').addClass('has-error').find('.help-block:last').text('初步诊断不能为空');
                                status = 1;
                            }
                        }
                    });
                    if (status) {
                        return false;
                    }
                },
                complete: function () {


                },
                success: function (data) {
                    if (data.errorCode != 0) {
                        showInfo(data.msg, '200px', 2);
                    }else{
                        showInfo(data.msg, '200px');
                         window.location.reload();
                    }
                },
            });
        },
        orthodonticsReturnvisitPrint: function (record_id) {
            $.ajax({
                type: 'post',
                url: getOrthodonticsReturnvisitRecord,
                data: {
                    'record_id': record_id
                },
                dataType: 'json',
                success: function (json) {
                    if (json['errorCode'] == 0) {
                        var spotInfo = json['data']['spotInfo'];
                        var spotConfig = json['data']['spotConfig'];
                        var userInfo = json['data']['userInfo'];
                        var baseInfo = json['data']['baseInfo'];
                        var firstCheck = json['data']['firstCheck'];
                        var allergy = json['data']['allergy'];
                        var logo_img = '';
                        if (spotConfig.logo_shape == 1) {
                            logo_img = "clinic-img"
                        } else {
                            logo_img = "clinic-img-long"
                        }
                        var prinkRecordInfoModel = template.compile(prinkTpl)({
                            spotInfo: spotInfo,
                            spotConfig: spotConfig,
                            logo_img: logo_img,
                            record_id: record_id,
                            userInfo: userInfo,
                            firstCheck: firstCheck,
                            baseInfo: baseInfo,
                            allergy: allergy,
                            cdnHost: cdnHost,
                            baseUrl: baseUrl,
                        });
                        $('#orthodontics-print').html(prinkRecordInfoModel);
                        $('#orthodontics-print' + record_id + 'myshow').jqprint();
                    }
                },
                error: function () {
                },
            });
        },
    };
    return main;
});

