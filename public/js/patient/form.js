

define(function (require) {
    var template = require('template');
    var select = require('plugins/select2/select2.full.min');
    var patientInfoTpl = require('tpl/patientInfoTop.tpl');
    var allergyTpl = require('tpl/allergy.tpl');
    var common = require('js/lib/common');
    var uploadFile = require('tpl/uploadModal.tpl');
    var bootstrap_timepicker = require('plugins/timepicker/bootstrap-timepicker.min');
//    var cityPickerData = require('js/lib/city-picker.data');
//    var cityPicker = require('js/lib/city-picker');
    var cropper = require('dist/cropper');
    var upload = require('upload/main');
    var _self;
    var main = {
        init: function () {
            _self = this;
            this.bindEvent();
            this.hidePlaceHolder();
            this.addPrintDom();
        },
        addPrintDom: function () {
            var a = '';
            a += '<div id="growth_print" class="common-print-container" style="display: none;"></div>';
            $('.wrapper').after(a);
        },
        hidePlaceHolder: function () {
            //隐藏placeholder “法定监护人”备注
            $('#patient-card').removeAttr('placeholder');
            $('#patientsubmeter-other_faiths').removeAttr('placeholder');
            $('#patientsubmeter-other_languages').removeAttr('placeholder');
            $('#patientsubmeter-other_guardian').removeAttr('placeholder');
        },
        init_basic: function () {  // 编辑初始化页面
            var langSelect = $('#patientsubmeter-languages'), fatSelect = $('#patientsubmeter-faiths');
            5 == langSelect.val() ? $('#languages_input_div').show() : 1; //  其他时显示文本框
            6 == fatSelect.val() ? $('#faiths_input_div').show() : 1;
            if (5 == langSelect.val() && fatSelect.val()) { // 同时为其他时换行
                main.changeClass(true);
            }
            $('#patientsubmeter-other_guardian').removeAttr('placeholder');

        },
        bind_basic: function () {  // 绑定患者信息页面中各种事件
            $('#patient-card').attr('placeholder', '请输入身份证号');
            $('#patientsubmeter-other_faiths').attr('placeholder', '请输入宗教信仰');
            $('#patientsubmeter-other_languages').attr('placeholder', '请输入第一语言');

            $(".timepicker").timepicker({
                showInputs: false,
                showMeridian: false,
                minuteStep: 10,
                defaultTime: '00:00'
            });

            $(".select2").select2(); // 初始化搜索选择框

            var langSelect = $('#patientsubmeter-languages'), fatSelect = $('#patientsubmeter-faiths');
            langSelect.change(function () {
                5 == $(this).val() ? $('#languages_input_div').show() : $('#languages_input_div').hide(); //选择为其他时显示文本框
                if (5 == $(this).val() && 6 == $('#patientsubmeter-faiths').val()) { //同时为其他时换行
                    main.changeClass(true);
                } else {
                    main.changeClass(false);
                }
            });

            fatSelect.change(function () {
                6 == $(this).val() ? $('#faiths_input_div').show() : $('#faiths_input_div').hide();  //选择为其他时显示文本框
                if (6 == $(this).val() && 5 == $('#patientsubmeter-languages').val()) { //同时为其他时换行
                    main.changeClass(true);
                } else {
                    main.changeClass(false);
                }
            });

            $('.basic-submit').unbind('click').click(function () {  //  保存时 不是其他 清空其他框中的内容
                var langSelect = $('#patientsubmeter-languages'), fatSelect = $('#patientsubmeter-faiths');
                5 != langSelect.val() ? $('#patientsubmeter-other_languages').val('') : 1;
                6 != fatSelect.val() ? $('#patientsubmeter-other_faiths').val('') : 1;
            });

            $('.box-detail').unbind('click').click(function () {
                $('.box-detail').each(function () {
                    $(this).removeClass('box-active');
                });
                $(this).addClass('box-active');
            });

            $('.btn-box-tool').unbind('click').click(function () {
                if ($(this).attr('data-value') == 0 || $(this).attr('data-value') === undefined) {
                    $(this).parents('.none_radius').addClass('box-open');
                    $(this).attr('data-value', 1);
                } else {
                    $(this).parents('.none_radius').removeClass('box-open');
                    $(this).attr('data-value', 0);
                }
            });

        },
        changeClass: function (is_newline) {  // 需要换行时增加格子
            if (is_newline) {
                $('#languages_input_div').removeClass('col-sm-4').addClass('col-sm-8');
                $('#faiths_input_div').removeClass('col-sm-4').addClass('col-sm-8');
            } else {
                $('#languages_input_div').removeClass('col-sm-8').addClass('col-sm-4');
                $('#faiths_input_div').removeClass('col-sm-8').addClass('col-sm-4');
            }
        },
        bindEvent: function () {

            this.bind_basic();
            this.init_basic();

            _self.addPatientInfo();//患者个人信息卡片

            $('body').on('click', '.avatar-save', function () {
                var avatar = document.getElementById('avatarInput');
                var filename = avatar.value;
                var fileExtension = filename.substring(filename.lastIndexOf('.') + 1).toLowerCase();
                console.log(fileExtension);
                if (!filename && fileExtension != 'jpg' && fileExtension != 'png' && fileExtension != 'jpeg' && fileExtension != 'gif') {
                    showInfo('请上传正确的图片格式', '180px', 2);
                    return false;
                }
            });

            var uploadModal = template.compile(uploadFile)({
                title: '上传头像',
                url: uploadUrl,
                patientId: patientId,
            });

            $('#crop-avatar').append(uploadModal);

            $('.outpatient-update .form-control').attr({'disabled': true});
            $('.outpatient-update [type=radio]').attr({'disabled': true});
            $('.outpatient-update [type=checkbox]').attr({'disabled': true});
            $("[name=defaultSpot]:checkbox").attr({'disabled': false});
//            $('.input-group-addon').off('click');
            $('#patient-address').attr('data-toggle', 'city-picker');
            // $('.input-group-addon').hide();
            $('.allergy-list').each(function () {
                $(this).find('.allergy').hide();
            });
            _self.toggleForm();
            _self.saveForm();
            _self.addAllergy();
            _self.cancelBtn();
            $('body').off('click', '.kv-file-download').on('click', '.kv-file-download', function () {
                //console.log('2222222222');
                var src = $(this).parents('.file-thumbnail-footer').siblings('.kv-file-content').children('.kv-preview-data').attr('src');
                window.open(src);
                //console.log(src);
            });
            _self.selectTab();
        },
        addPatientInfo: function () {
            var triageInfoModel = template.compile(patientInfoTpl)({
                list: triageInfo,
                baseUrl: baseUrl,
                allergy: allergy,
                is_load: 1,
                hideDiagnosisInfo: true,
                cdnHost: cdnHost,
                apiGrowthViewUrl: apiGrowthViewUrl
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
            $('body').on('click', '.basic-right-up-child', function () {
                $('#patientsubmeter-other_guardian').attr('placeholder', '“法定监护人”备注');
                _self.toggleFormInfo(this, 'child');
            });
        },
        toggleFormInfo: function (obj, targeStr) {
            $(obj).hide();
            var target = '.basic-form-content-' + targeStr;
            $('' + target + ' .basic-btn').show();
            $('' + target + ' .form-control').removeAttr('disabled');
            $('' + target + ' [type=radio]').removeAttr('disabled');
            $('' + target + ' [type=checkbox]').removeAttr('disabled');
            if (targeStr == 'case') {
                $('.allergy-list').each(function () {
                    $(this).find('.allergy').show();
                });
            }
            if (targeStr == 'basic') {
                $('#patient-address').citypicker();
                this.bind_basic();
            }
        },
        saveForm: function () {
            _self.saveFormInfo('basic-basic', 'basic-pjax', 'basic-submit');
            _self.saveFormInfo('basic-case', 'case-pjax', 'case-submit');
            _self.saveFormInfo('basic-other', 'other-pjax', 'other-submit');
            _self.saveFormInfo('basic-child', 'child-pjax', 'child-submit');
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
                            if (data.url) {
                                window.location.href = data.url;
                            } else {
                                $.pjax.reload({container: '#' + container, cache: false, timeout: 5000});  //Reload
                            }
                        }

                    },
                });
            })

        },
        addAllergy: function () {
            $('body').on('click', "input[name='Patient[has_allergy_type]'][value=1]", function () {
                $('#allergy-list').show();
            });
            $('body').on('click', "input[name='Patient[has_allergy_type]'][value=2]", function () {
                $('#allergy-list').hide();
            })
            var len = $('.allergy-list').length;
            if (len == 1) {
                $('.allergy-delete').hide();
            }
            $('body').on('click', '.allergy-add', function () {
                var allergyList = template.compile(allergyTpl)({model: 'Patient'});
//                console.log(allergyList);
//                $('#allergy-list').append('<div class ="allergy-list">' + allergyList + '</div>');
                $('.allergy-add').hide();
                $('.allergy-delete').hide();
                $('.allergy-list').last().children().children('.form-group').children('a').show();

            });
            $('body').off('click', '.allergy-delete').on('click', '.allergy-delete', function () {
                var len = $('.allergy-list').length;
                if (len == 2) {
                    $('.allergy-delete').hide();
                }
//                $('.allergy-list').first().remove();
            });
        },
        cancelBtn: function () {
            _self.cancelBtnInfo('btn-cancel-basic', 'basic-pjax');
            _self.cancelBtnInfo('btn-cancel-case', 'case-pjax');
            _self.cancelBtnInfo('btn-cancel-other', 'other-pjax');
            _self.cancelBtnInfo('btn-cancel-child', 'child-pjax');
        },
        cancelBtnInfo: function (btnId, container) {
            $('body').on('click', '.' + btnId, function (e) {
                $.pjax.reload({container: '#' + container});  //Reload
            });
        },
        selectTab: function () {
            var hash = window.location.hash && window.location.hash.split('_', 1)[0] || '#basic';
            $('ul.nav-tabs>li>a').on('click', function () {
                var href = $(this).attr('href');
                window.location.hash = href;
                hash = href;
            });
            $('a[href="' + hash + '"]').click();
            $('.nav-tabs').find('li').removeClass('active');
            $('a[href="' + hash + '"]').parent('li').addClass('active');
            $(hash).siblings('.tab-pane').removeClass('active');
            $(hash).addClass('active');
            var patientRecordId = getUrlParam('recordId');
            if (patientRecordId) {
                $('#showDetail' + patientRecordId).click();
            }
            
            
            
        },
        
    };
    return main;
})