/**
 *
 */
define(function (require) {
    var template = require('template');
    var cropper = require('dist/cropper');
    var common = require('js/lib/common');
    var uploadFile = require('tpl/uploadModal.tpl');
    var select = require('plugins/select2/select2.full.min');
    var upload = require('upload/main');
    var appointment = require('tpl/appointment.tpl');
    var familyTpl = require('tpl/family.tpl');
    var bootstrap_timepicker = require('plugins/timepicker/bootstrap-timepicker.min');
    var once = 0;
    var detail = 0;
    var appointmentType = 0;
    var firstDepartment = 0;//首次加载
    var main = {
        init: function () {
            var nowurl = window.location.href;
            $('.show-hide-btn-nurse').click(function(){//展开更多，收起
            	var hide = $('.other-info').hasClass('hide');
            	if(hide == true){
            		$('.other-info').removeClass('hide');
            		$(this).html('收起信息    <i class="fa fa-angle-up" aria-hidden="true"></i>');
            	}else{
            		$('.other-info').addClass('hide');
            		$(this).html('展开更多    <i class="fa fa-angle-down" aria-hidden="true"></i>');

            	}
            }).click();
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
            });

            $('#crop-avatar').append(uploadModal);

            $(".timepicker").timepicker({
                showInputs: false,
                showMeridian: false,
                minuteStep: 10,
                defaultTime: '00:00',
            });

            if ($('#patient-hourmin').val() == '00:00') {
                $('#patient-hourmin').val('');
            }

            jsonFormInit = $("form").serialize();  //为了表单验证

            $('.field-patient-username').bind('input propertychange', function () {
                $(this).css('position', 'relative');
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
                $(this).css('position', 'relative');
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
                window.location.href = createUrl + '?id=' + id;
            });
            $('body').on('click', function () {
                $('.J-search-name').remove();
            });

            $('body').on("input propertychange",'.patient-family_card',function () {
                main.getBirthday($(this));
            });
            $('body').on("input propertychange",'.patient-family_relation',function () {
                main.getRelationSex($(this));
            });
            main.activeForm();
            main.addClinicDepartment();
            main.initShiftBtn();
            main.bindEvent();
            main.selectDepartment();
            main.saveInfo();

        },
        initShiftBtn: function () {
            var len = $('.family-list').length;
            if (len >= 2) {
                $(".family-delete").show();
                $(".family-add").hide();
                $('.family-add').last().show();
            } else {
                $(".family-delete").first().hide();
            }
        }

        ,
        addClinicDepartment: function () {
            $('body').on('click', '.family-add', function () {
//                var familyList = $('.family-list').html();
                var familyList = familyTpl;

                $('#family').append(familyList);
                $('.family-add').hide();
                $('.family-delete').show();
                $(".family-delete").first().show();
                $('.family-add').last().show();

            });
            $('body').on('click', '.family-delete', function () {
                $(this).parents('.family-list').remove();
                var len = $('.family-list').length;
                $('.family-add').hide();

                if (len == 1) {
                    $('.family-delete').hide();
                }

                $('.family-add').last().show();
            });

            $('body').on('mouseover', '.patient-family_birthday', function () {
                $(this).datepicker({
                    "autoclose": true,
                    "format": "yyyy-mm-dd",
                    "language": "zh-CN",
                    "inline": false
                });
            })
            $('body').on('click', '.patient-family_birthday', function () {
                $(this).on('changeDate', function (e) {
//                    $(this).val(e.format());
                    $('.patient-family_birthday').focusin().focusout();
                });
            });

        }
        ,
        validateFamily: function () {
            $('.patient-family_relation').each(function () {

                var relationVal = $(this).val();
                var family_name = $(this).parents('.family-list').find('.patient-family_name').val();
                var family_sex = $(this).parents('.family-list').find('.patient-family_sex').val();
                var family_birthday = $(this).parents('.family-list').find('.patient-family_birthday').val();
                var family_iphone = $(this).parents('.family-list').find('.patient-family_iphone').val();
                var family_card = $(this).parents('.family-list').find('.patient-family_card').val();
                // var nowDate = main.getDate();
                if (relationVal || family_name || family_sex || family_birthday || family_iphone || family_card) {
                    var pattern = /^\d{11}$/;
                    var pattern_card = /^(\d{15}$|^\d{18}$|^\d{17}(\d|X|x))$/;
                    if (relationVal == '') {
                        var parentObj = $(this).parent();
                        parentObj.removeClass('has-success');
                        parentObj.addClass('has-error');
                        parentObj.find('.help-block').html('成员关系不能为空。');
                    } else {
                        $(this).parent('.field-patient-family_relation').removeClass('has-error');
                        $(this).parent('.field-patient-family_relation').addClass('has-success');
                        $(this).parent('.field-patient-family_relation').find('.help-block').html('');
                    }
                    if (family_name == '') {
                        $(this).parents('.family_relation').siblings('.family_name').children('.field-patient-family_name').removeClass('has-success');
                        $(this).parents('.family_relation').siblings('.family_name').children('.field-patient-family_name').addClass('has-error');
                        $(this).parents('.family_relation').siblings('.family_name').find('.help-block').html('姓名不能为空。');
                    } else {
                        $(this).parents('.family_relation').siblings('.family_name').children('.field-patient-family_name').removeClass('has-error');
                        $(this).parents('.family_relation').siblings('.family_name').children('.field-patient-family_name').addClass('has-success');
                        $(this).parents('.family_relation').siblings('.family_name').find('.help-block').html('');

                    }
                    if (family_sex == '') {
                        $(this).parents('.family_relation').siblings('.family_sex').children('.field-patient-family_sex').removeClass('has-success');
                        $(this).parents('.family_relation').siblings('.family_sex').children('.field-patient-family_sex').addClass('has-error');
                        $(this).parents('.family_relation').siblings('.family_sex').find('.help-block').html('性别不能为空。');

                    } else {
                        $(this).parents('.family_relation').siblings('.family_sex').children('.field-patient-family_sex').removeClass('has-error');
                        $(this).parents('.family_relation').siblings('.family_sex').children('.field-patient-family_sex').addClass('has-success');
                        $(this).parents('.family_relation').siblings('.family_sex').find('.help-block').html('');


                    }
                    /*if (family_birthday == '') {
                     $(this).parents('.one').siblings('.second').find('.field-patient-family_birthday').removeClass('has-success');
                     $(this).parents('.one').siblings('.second').find('.field-patient-family_birthday').addClass('has-error');
                     $(this).parents('.one').siblings('.second').find('.field-patient-family_birthday').children('.help-block').html('出生日期不能为空。');
                     
                     } else */
                    if (main.checkDate(family_birthday) == 1) {
                        $(this).parents('.one').siblings('.second').find('.field-patient-family_birthday').removeClass('has-success');
                        $(this).parents('.one').siblings('.second').find('.field-patient-family_birthday').addClass('has-error');
                        $(this).parents('.one').siblings('.second').find('.field-patient-family_birthday').children('.help-block').html('出生日期不能大于当前时间。');

                    } else {
                        $(this).parents('.one').siblings('.second').find('.field-patient-family_birthday').removeClass('has-error');
                        $(this).parents('.one').siblings('.second').find('.field-patient-family_birthday').addClass('has-success');
                        $(this).parents('.one').siblings('.second').find('.field-patient-family_birthday').children('.help-block').html('');

                    }
                    if (family_iphone == '') {
                        $(this).parents('.one').siblings('.second').find('.field-patient-family_iphone').removeClass('has-success');
                        $(this).parents('.one').siblings('.second').find('.field-patient-family_iphone').addClass('has-error');
                        $(this).parents('.one').siblings('.second').find('.field-patient-family_iphone').children('.help-block').html('手机号码不能为空。');

                    } else if (!pattern.exec(family_iphone)) {
                        $(this).parents('.one').siblings('.second').find('.field-patient-family_iphone').removeClass('has-success');
                        $(this).parents('.one').siblings('.second').find('.field-patient-family_iphone').addClass('has-error');
                        $(this).parents('.one').siblings('.second').find('.field-patient-family_iphone').children('.help-block').html('手机号码是无效的。');

                    } else {
                        $(this).parents('.one').siblings('.second').find('.field-patient-family_iphone').removeClass('has-error');
                        $(this).parents('.one').siblings('.second').find('.field-patient-family_iphone').addClass('has-success');
                        $(this).parents('.one').siblings('.second').find('.field-patient-family_iphone').children('.help-block').html('');

                    }
                    if (family_card && !pattern_card.exec(family_card)) {
                        $(this).parents('.one').siblings('.second').find('.field-patient-family_card').removeClass('has-success');
                        $(this).parents('.one').siblings('.second').find('.field-patient-family_card').addClass('has-error');
                        $(this).parents('.one').siblings('.second').find('.field-patient-family_card').children('.help-block').html('身份证号是无效的。');

                    } else {
                        $(this).parents('.one').siblings('.second').find('.field-patient-family_card').removeClass('has-error');
                        $(this).parents('.one').siblings('.second').find('.field-patient-family_card').addClass('has-success');
                        $(this).parents('.one').siblings('.second').find('.field-patient-family_card').children('.help-block').html('');

                    }


                } else {
                    $(this).parent('.field-patient-family_relation').removeClass('has-error');
                    $(this).parent('.field-patient-family_relation').addClass('has-success');
                    $(this).parent('.field-patient-family_relation').find('.help-block').html('');

                    $(this).parents('.family_relation').siblings('.family_name').children('.field-patient-family_name').removeClass('has-error');
                    $(this).parents('.family_relation').siblings('.family_name').children('.field-patient-family_name').addClass('has-success');
                    $(this).parents('.family_relation').siblings('.family_name').find('.help-block').html('');

                    $(this).parents('.family_relation').siblings('.family_sex').children('.field-patient-family_sex').removeClass('has-error');
                    $(this).parents('.family_relation').siblings('.family_sex').children('.field-patient-family_sex').addClass('has-success');
                    $(this).parents('.family_relation').siblings('.family_sex').find('.help-block').html('');
                    $(this).parents('.one').siblings('.second').find('.field-patient-family_birthday').removeClass('has-error');
                    $(this).parents('.one').siblings('.second').find('.field-patient-family_birthday').addClass('has-success');
                    $(this).parents('.one').siblings('.second').find('.field-patient-family_birthday').children('.help-block').html('');
                    $(this).parents('.one').siblings('.second').find('.field-patient-family_iphone').removeClass('has-error');
                    $(this).parents('.one').siblings('.second').find('.field-patient-family_iphone').addClass('has-success');
                    $(this).parents('.one').siblings('.second').find('.field-patient-family_iphone').children('.help-block').html('');
                }


            });
        }
        ,
        checkDate: function (beginTime) {
            var mydate = new Date();
            var myyear = mydate.getFullYear();
            var mymonth = mydate.getMonth() + 1;
            var myweekday = mydate.getDate();
            var nowDate = myyear + '-' + mymonth + '-' + myweekday;
            var startTime = new Date(beginTime.replace("-", "/").replace("-", "/"));
            var end = new Date(nowDate.replace("-", "/").replace("-", "/"));
            if (startTime > end) {
                return 1;
            }
            return 0;
        }
        ,
        activeForm: function () {
            $('body').on('focusout', '.patient-family_name', function () {
                var relation = $(this).parents('.family_name').siblings('.family_relation').find('.patient-family_relation').val();
                if (relation == '' || relation == null) {
                    $(this).parent().removeClass('has-error');
                    $(this).parent().addClass('has-success');
                    $(this).siblings('.help-block').html('');
                    return false;
                }
                var val = $(this).val();
                if (!val) {
                    $(this).parent().removeClass('has-success');
                    $(this).parent().addClass('has-error');
                    $(this).siblings('.help-block').html('姓名不能为空。');
                } else {
                    $(this).parent().removeClass('has-error');
                    $(this).parent().addClass('has-success');
                    $(this).siblings('.help-block').html('');
                }
            });

            $('body').on('focusout', '.patient-family_sex', function () {
                var relation = $(this).parents('.family_sex').siblings('.family_relation').find('.patient-family_relation').val();
                if (relation == '' || relation == null) {
                    $(this).parent().removeClass('has-error');
                    $(this).parent().addClass('has-success');
                    $(this).siblings('.help-block').html('');
                    return false;
                }
                var val = $(this).val();
                if (!val) {
                    $(this).parent().removeClass('has-success');
                    $(this).parent().addClass('has-error');
                    $(this).siblings('.help-block').html('性别不能为空。');
                } else {
                    $(this).parent().removeClass('has-error');
                    $(this).parent().addClass('has-success');
                    $(this).siblings('.help-block').html('');
                }
            });

            $('body').on('focusout', '.patient-family_card', function () {
                var relation = $(this).parents('.second').siblings('.one').find('.patient-family_relation').val();
                if (relation == '' || relation == null) {
                    $(this).parent().removeClass('has-error');
                    $(this).parent().addClass('has-success');
                    $(this).siblings('.help-block').html('');
                    return false;
                }
                var val = $(this).val();
                var pattern = /^(\d{15}$|^\d{18}$|^\d{17}(\d|X|x))$/;
                if (val && !pattern.exec(val)) {

                    $(this).parent().removeClass('has-success');
                    $(this).parent().addClass('has-error');
                    $(this).siblings('.help-block').html('身份证号是无效的。');
                } else {
                    $(this).parent().removeClass('has-error');
                    $(this).parent().addClass('has-success');
                    $(this).siblings('.help-block').html('');
                }
            });
            $('body').on('focusout', '.patient-family_iphone', function () {
                var relation = $(this).parents('.second').siblings('.one').find('.patient-family_relation').val();
                if (relation == '' || relation == null) {
                    $(this).parent().removeClass('has-error');
                    $(this).parent().addClass('has-success');
                    $(this).siblings('.help-block').html('');
                    return false;
                }
                var val = $(this).val();
                var pattern = /^\d{11}$/;
                if (!val) {
                    $(this).parent().removeClass('has-success');
                    $(this).parent().addClass('has-error');
                    $(this).siblings('.help-block').html('手机号码不能为空。');
                } else if (!pattern.exec(val)) {
                    $(this).parent().removeClass('has-success');
                    $(this).parent().addClass('has-error');
                    $(this).siblings('.help-block').html('手机号码是无效的。');
                } else {
                    $(this).parent().removeClass('has-error');
                    $(this).parent().addClass('has-success');
                    $(this).siblings('.help-block').html('');
                }
            });
            $('body').on('change', '.patient-family_relation', function () {
                var val = $(this).val();
                if (val == '' || val == null) {

                    $(this).parents('.family_relation').siblings('.family_name').children('.field-patient-family_name').removeClass('has-error');
                    $(this).parents('.family_relation').siblings('.family_name').children('.field-patient-family_name').addClass('has-success');
                    $(this).parents('.family_relation').siblings('.family_name').find('.help-block').html('');

                    $(this).parents('.family_relation').siblings('.family_sex').children('.field-patient-family_sex').removeClass('has-error');
                    $(this).parents('.family_relation').siblings('.family_sex').children('.field-patient-family_sex').addClass('has-success');
                    $(this).parents('.family_relation').siblings('.family_sex').find('.help-block').html('');
                    $(this).parents('.one').siblings('.second').find('.field-patient-family_birthday').removeClass('has-error');
                    $(this).parents('.one').siblings('.second').find('.field-patient-family_birthday').addClass('has-success');
                    $(this).parents('.one').siblings('.second').find('.field-patient-family_birthday').children('.help-block').html('');
                    $(this).parents('.one').siblings('.second').find('.field-patient-family_iphone').removeClass('has-error');
                    $(this).parents('.one').siblings('.second').find('.field-patient-family_iphone').addClass('has-success');
                    $(this).parents('.one').siblings('.second').find('.field-patient-family_iphone').children('.help-block').html('');
                    $(this).parents('.one').siblings('.second').find('.field-patient-family_card').removeClass('has-error');
                    $(this).parents('.one').siblings('.second').find('.field-patient-family_card').addClass('has-success');
                    $(this).parents('.one').siblings('.second').find('.field-patient-family_card').children('.help-block').html('');


                } else {
                    $(this).parents('.family_relation').children('.field-patient-family_relation').removeClass('has-error');
                    $(this).parents('.family_relation').children('.field-patient-family_relation').addClass('has-success');
                    $(this).parents('.family_relation').find('.help-block').html('');
                }
            });
        }
        ,
        bindEvent: function () {

            $(".select2").select2(); // 初始化搜索选择框

            // 编辑初始化页面
            var langSelect = $('#patientsubmeter-languages'), fatSelect = $('#patientsubmeter-faiths');
            5 == langSelect.val() ? $('#languages_input_div').show() : 1; //  其他是显示文本框
            6 == fatSelect.val() ? $('#faiths_input_div').show() : 1;
            if (5 == langSelect.val() && fatSelect.val()) { // 同时为其他时换行
                main.changeClass(true);
            }

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

            $('#reportForm').click(function () {  //  保存事 不是其他 清空其他框中的内容
                var langSelect = $('#patientsubmeter-languages'), fatSelect = $('#patientsubmeter-faiths');
                5 != langSelect.val() ? $('#patientsubmeter-other_languages').val('') : 1;
                6 != fatSelect.val() ? $('#patientsubmeter-other_faiths').val('') : 1;
            });

        }
        ,
        changeClass: function (is_newline) {  // 需要换行时增加格子
            if (is_newline) {
                $('#languages_input_div').removeClass('col-sm-4').addClass('col-sm-8');
                $('#faiths_input_div').removeClass('col-sm-4').addClass('col-sm-8');
            } else {
                $('#languages_input_div').removeClass('col-sm-8').addClass('col-sm-4');
                $('#faiths_input_div').removeClass('col-sm-8').addClass('col-sm-4');
            }
        },
        selectDepartment: function () {

            //医生下拉框值改变时
            $('#report-doctor_id').change(function () {
            	main.getDoctorDepartment($(this).val());
                jsonFormInit = $("form").serialize();  //为了表单验证
            });

        },
        getDoctorDepartment : function(id){
        	$.ajax({
                type: 'post',
                url: apiAppointmentGetDoctorDepartment,
                data: {
                    'id': id,
                    'entrance' : 1,
                },
                success: function (json) {
                    var a = '<option value="">请选择</option>';
//                    if (firstDepartment == 0 && action == 'update' && error == 0) {
//                        a += '<option value="' + second_department_id + '" selected>' + secondDepartmentName + '</option>';
//                    }
                    var result = json.data;
                    var count = json.count;
                        if (count >= 1) {
                            $.each(result, function (key, val) {
                                a += '<optgroup label="' + val.name + '">';
                                console.log(val.children);
                                $.each(val.children, function (key, value) {
                                	if(count == 1){
                                    	$('.field-report-second_department_id').removeClass('has-error').addClass('has-success');
                                    	$('.field-report-second_department_id .help-block').html('');
                                        a += '<option value="' + value['id'] + '" selected>' + htmlEncodeByRegExp(value['name']) + '</option>';
                                    }else{
                                		a += '<option value="' + value['id'] + '">' + htmlEncodeByRegExp(value['name']) + '</option>';
                                	}
                                })
                                a += '</optgroup>';
                            });
                  

                        }
                   
                    $('#report-second_department_id').html(a);

                    firstDepartment = 1;
                    jsonFormInit = $("form").serialize();  //为了表单验证
                },
                error: function () {

                },
                dataType: 'json'

            });
        },
        setAppointmentDisabled: function () {
        	$('#report-second_department_id').attr({'disabled':true});
            $('#report-doctor_id').attr({'disabled': true});
            $('#report-type').attr({'disabled': true});
        },
        saveInfo: function () {
            var isCommitted = false;//表单是否已经提交标识，默认为false
            $('body').off('click', '#reportForm').on('click', '#reportForm', function (e) {
                main.validateFamily();
                $('#patient-username').parent().removeClass('has-error');
                $('#patient-username').siblings('.help-block').html('')
                $('#patient-hourmin').parent().removeClass('has-error');
                $('#patient-hourmin').siblings('.help-block').html('');
                var errorLen = $('.has-error').length;
                var username = $("#patient-username").val();

                if (errorLen >= 1) {
                    e.preventDefault();
                    return false;
                }

                $('#report-patient').yiiAjaxForm({
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
                        	window.location.href = indexUrl;
                        } else {
                            showInfo(data.msg,'300px',2);
                        }
                        isCommitted = false;
                    },
                });
            });
        },
        //根据身份证号获取出生日期
         getBirthday:function(obj){
             var card = obj.val();
             var target=obj.parents('.family_card').siblings('.family_birthday').find('.patient-family_birthday')
             var res = CertificateNoParse(card);
             if(res){
                 target.val(res.birthday);
             }
         },
        //根据关系获取性别
         getRelationSex:function(obj){
             var relation = obj.val();
             console.log(relation);
             var target=obj.parents('.family_relation').siblings('.family_sex').find('.patient-family_sex');
             var male = ['1','4','8'];
             var female =['2','5','9'];
             if($.inArray(relation,male) !=-1){
                 target.val(1);
             }else if($.inArray(relation,female) !=-1){
                 target.val(2);
             }else{
                 target.val('');
             }
         }

    };
    return main;
})