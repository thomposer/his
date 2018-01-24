define(function (require) {
    var template = require('template');
    var select = require('plugins/select2/select2.full.min');
    var patientInfoTpl = require('tpl/patientInfo.tpl');
    var allergyTpl = require('tpl/allergy.tpl');
    var common = require('js/lib/common');
    var appointment = require('tpl/appointment.tpl');
    var csrfToken = $('meta[name="csrf-token"]').attr('content');
    var _self;
    var familey_href = $('.family-add>a').attr('href');

    var main = {
        init: function () {
            _self = this;
            this.bindEvent();
        },
        bindEvent: function () {
//            _self.addPatientInfo();//患者个人信息卡片
//            $('.form-control').attr({'disabled': true});
//            $('[type=radio]').attr({'disabled': true});
//            $('.input-group-addon').off('click');
//            $('.input-group-addon').hide();
            $('#patient-address').attr('data-toggle', 'city-picker');
            $('.allergy-list').each(function () {
                $(this).find('.allergy').hide();
            });
            $('body').on('click', '.kv-file-download', function () {
                var src = $(this).parents('.file-thumbnail-footer').siblings('.kv-file-content').children('.kv-preview-data').attr('src');
                window.open(src);
            });

            $('body').on('click', '.kv-file-remove', function () {
                var url = $(this).data('url');
                var key = $(this).data('key');
                var isNew = $(this).data('new');
                if (url && key && isNew == 1) {
                    $.ajax({
                        cache: true,
                        type: "POST",
                        url: url,
                        data: {
                            key: key,
                        }, // 你的formid
                        dataType: 'json',
                        async: false,
                        success: function (data, textStatus, jqXHR) {
                            if (data.errorCode == 0) {

                            }

                        },
                        error: function () {
                            showInfo('系统异常,请稍后再试', '180px', 2);
                        }
                    });
                }
            });

            _self.selectTab();
            _self.toggleForm();
            _self.saveForm();
//            _self.addAllergy();
            _self.cancelBtn();
            _self.makeupType();
            _self.makeupTypeOk();
            _self.basicFind();
//            _self.recipeEdit();

            var patientId = getUrlParam('patientId');
            if (patientId) {
                $('#basic-pjax .form-control').attr({'disabled': true});
                $('[type=radio]').attr({'disabled': true});
                $('.input-group-addon').hide();
            }
            if (!patientId) {
                $('.basic-right-up-basic').hide();
                var target = '.basic-form-content-basic';
                $('' + target + ' .basic-btn').show();
                $('' + target + ' .form-control').removeAttr('disabled');
                $('' + target + ' [type=radio]').removeAttr('disabled');
                $('#patient-address').citypicker();
                $('.family-add>a').attr('disabled', "true");
                $('.family-add>a').removeAttr('href');
                $('.family-add>a').removeAttr('data-toggle');
                $('.family-add>a').removeAttr('role');
            }

            $('body').on('change', '#triageinfo-treatment_type', function () {
                var type = $(this).val();
                $('#triageinfo-treatment').val('');
                if (5 == type) {
                    $('.treatment_div').show();
                } else {
                    $('.treatment_div').hide();
                }
            });
        },
        basicFind: function () {
            $('.field-patient-username').bind('input propertychange', function () {
                $.ajax({
                    type: 'post',
                    url: getPatients,
                    data: {
                        'patientName': $('#patient-username').val()
                    },
                    success: function (json) {
                        $('.J-search-name').remove();
                        if (json.data.length >= 1) {
                            var appointmentModal = template.compile(appointment)({
                                list: json.data,
                                baseUrl: baseUrl,
                                cdnHost: cdnHost
                            });
                            $('.field-patient-username').append(appointmentModal);
                        }
                    },
                    error: function () {

                    },
                    dataType: 'json'

                });
            });
            $('.field-patient-iphone').bind('input propertychange', function () {
                $.ajax({
                    type: 'post',
                    url: getIphone,
                    data: {
                        'patientIphone': $('#patient-iphone').val()
                    },
                    success: function (json) {
                        $('.J-search-name').remove();
                        if (json.data.length >= 1) {
                            var appointmentModal = template.compile(appointment)({
                                list: json.data,
                                baseUrl: baseUrl,
                                cdnHost: cdnHost
                            });
                            $('.field-patient-iphone').append(appointmentModal);
                        }
                    },
                    error: function () {

                    },
                    dataType: 'json'

                });
            });
            $('body').on('click', '.J-name-search-submit', function () {
                var id = $(this).attr('id');
                window.location.href = createUrl + '?patientId=' + id;
                return;
            });

            $('body').on('click', function () {
                $('.J-search-name').remove();
            });
        },
        selectTab: function () {
            var hash = window.location.hash ? window.location.hash : '#basic';
            $('ul.nav-tabs>li>a').on('click', function () {
                var href = $(this).attr('href');
                window.location.hash = href;
                hash = href;
            });
            $('.nav-tabs').find('li').removeClass('active');
            $('a[href="' + hash + '"]').parent('li').addClass('active');
            $(hash).siblings('.tab-pane').removeClass('active');
            $(hash).addClass('active');
        },
        addPatientInfo: function () {
            var triageInfoModel = template.compile(patientInfoTpl)({
                list: triageInfo,
                baseUrl: baseUrl,
                cdnHost: cdnHost
            });
            $('#outpatient-patient-info').html(triageInfoModel);
        },
        toggleForm: function () {
            $('body').on('click', '.basic-right-up-basic', function () {
                _self.toggleFormInfo(this, 'basic');
            });
            $('body').on('click', '.basic-right-up-case', function () {
                _self.toggleFormInfo(this, 'case');
            });
            $('body').on('click', '.basic-right-up-other', function () {
                _self.toggleFormInfo(this, 'other');
            });
        },
        toggleFormInfo: function (obj, targeStr) {
            $(obj).hide();
            var target = '.basic-form-content-' + targeStr;
            $('' + target + ' .basic-btn').show();
            $('' + target + ' .form-control').removeAttr('disabled');
            $('' + target + ' [type=radio]').removeAttr('disabled');
            if (targeStr == 'case') {
                $('.allergy-list').each(function () {
                    $(this).find('.allergy').show();
                });
            }
            if (targeStr == 'basic') {
                $('#patient-address').citypicker();
            }
        },
        saveForm: function () {
            _self.saveFormInfo('basic-basic', 'basic-pjax', 'basic-submit');
            _self.saveFormInfo('basic-case', 'case-pjax', 'case-submit');
            _self.saveFormInfo('basic-other', 'other-pjax', 'other-submit');
        },
        saveFormInfo: function (formId, container, btnId) {
            $('body').off('click', '.' + btnId).on('click', '.' + btnId, function (e) {
                $('#' + formId).yiiAjaxForm({
                    beforeSend: function () {
                    },
                    complete: function () {

                    },
                    success: function (data) {

                        if (data.errorCode == 0) {
                            showInfo(data.msg, '180px');
                        } else {
                            showInfo(data.msg, '300px', 2);
                        }
                        if (data.errorCode == 0) {
                            //$.pjax.reload({container: '#' + container});  //Reload
                            if (formId == 'basic-basic') {
                                $('.family-add>a').removeAttr('disabled');
                                $('.family-add>a').attr('href', familey_href + '&id=' + data.id);
                                $('.family-add>a').attr('data-toggle', 'tooltip');
                                $('.family-add>a').attr('role', 'modal-remote');
                                $('.ump-create-case>button').removeAttr('disabled');
                            }
                            $.pjax({url: data.url, container: '#' + container,cache: false, timeout: 5000});
                        }
                    },
                });
            })

        },
        addAllergy: function () {
            $('body').on('click', "input[name='TriageInfo[has_allergy_type]'][value=1]", function () {
                $('#allergy-list').show();
            });
            $('body').on('click', "input[name='TriageInfo[has_allergy_type]'][value=2]", function () {
                $('#allergy-list').hide();
            })
            var len = $('.allergy-list').length;
            if (len == 1) {
                $('.allergy-delete').hide();
            }
            $('body').on('click', '.allergy-add', function () {
                var allergyList = template.compile(allergyTpl)({model: 'TriageInfo'});
//                console.log(allergyList);
                $('#allergy-list').append('<div class ="allergy-list">' + allergyList + '</div>');
                $('.allergy-add').hide();
                $('.allergy-delete').hide();
                $('.allergy-list').last().children().children('.form-group').children('a').show();

            });
            $('body').off('click', '.allergy-delete').on('click', '.allergy-delete', function () {
                var len = $('.allergy-list').length;
                if (len == 2) {
                    $('.allergy-delete').hide();
                }
                $('.allergy-list').first().remove();
            });
        },
        cancelBtn: function () {
            _self.cancelBtnInfo('btn-cancel-basic', 'basic-pjax');
            _self.cancelBtnInfo('btn-cancel-case', 'case-pjax');
            _self.cancelBtnInfo('btn-cancel-other', 'other-pjax');
        },
        cancelBtnInfo: function (btnId, container) {
            $('body').on('click', '.' + btnId, function (e) {
                $.pjax.reload({container: '#' + container});  //Reload
            });
        },
        makeupType: function () {
            $('.makeup-type').click(function (e) {
                e.preventDefault();
                modal.open(this);
            });
        },
        makeupTypeOk: function () {
            $('body').on('click', '.makeup_type_ok', function (e) {
                e.preventDefault();
                var patientId = getUrlParam('patientId');
                var makeupType = $("input[name='makeup_type']:checked").val();
                modal.open(this, 'patientId=' + patientId + '&makeupType=' + makeupType);
            });
        },
    };
    return main;
})