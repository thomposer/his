/**
 * 
 */

define(function (require) {
    var template = require('template');
    var common = require('js/lib/common');
    var cropper = require('dist/cropper');
    var uploadFile = require('tpl/uploadModal.tpl');
    var departmentTpl = require('tpl/department.tpl');
    var upload = require('upload/main');
    var csrfToken = $('meta[name="csrf-token"]').attr('content');
    var main = {
        init: function () {
            var uploadModal = template.compile(uploadFile)({
                title: '上传头像',
                url: uploadUrl,
            });
            $('#crop-avatar').append(uploadModal);
            jsonFormInit = $("form").serialize();
            this.addClinicDepartment();
            this.bindEvent();
            this.validateClinic();
            this.initShiftBtn();
        },
        initShiftBtn: function () {
            var len = $('.clinic-department').length;
            if (len >= 2) {
                $(".clinic-delete").show();
                $(".clinic-add").hide();
                $('.clinic-add').last().show();
            } else {
                $(".clinic-delete").first().hide();
            }
        },
        bindEvent: function () {
            $('body').on('click', '.avatar-save', function () {
                var avatar = document.getElementById('avatarInput');
                var filename = avatar.value;
                var fileExtension = filename.substring(filename.lastIndexOf('.') + 1).toLowerCase();
                console.log(fileExtension);
                if (!filename && fileExtension != 'jpg' && fileExtension != 'png' && fileExtension != 'jpeg' && fileExtension != 'gif') {
                    showInfo('请上传正确的图片格式', '180px',2);
                    return false;
                }
            });
//            $("body").on("change", '.user-clinic_id', function () {
//                main.selectRoom($(this));
//            });

        },
        selectRoom: function (clinic) {
            var clinic_id = clinic.val();
            if (clinic_id === '') {
                main.viewSelect(clinic, '');
                return '';
            }
            $.ajax({
                type: 'post',
                url: apiSecondDepartmentSelect,
                dataType: 'json',
                data: {
                    clinic_id: clinic_id,
                    '_csrf': csrfToken
                },
                success: function (json) {
                    var data = json.data;
                    main.viewSelect(clinic, data);
                },
                error: function () {

                }
            });
        },
        viewSelect: function (obj, data) {
            if (data == '') {
                var time = '<option value="">请选择</option>';
            } else {
                var time = '<option value="">请选择</option>';
                $.each(data, function (key, val) {
                    time += '<optgroup label="' + key + '">';
                    $.each(val, function (key, val) {
                        time += '<option value="' + val['id'] + '">' + htmlEncodeByRegExp(val['name']) + '</option>'
                    })
                    time += '</optgroup>';
                });
            }
            obj.parents('.clinic-department').find(".field-user-department select[name='User[department][]']").html(time);
        },
        addClinicDepartment: function () {
            $('body').on('click', '.clinic-add', function () {
                var clinicDepartment = template.compile(departmentTpl)({
                    department: onceDepartmentSelect,
                    secondDepartment: {}
                });
                $('#clinic-department').append('<div class ="row clinic-department">' + clinicDepartment + '</div>');
                $('.clinic-add').hide();
                $(".clinic-delete").first().show();
                $('.clinic-add').last().show();

            });
            $('body').on('click', '.clinic-delete', function () {

                $(this).parents('.clinic-department').remove();
                $('.clinic-add').hide();

                var len = $('.clinic-department').length;
                if (len == 1) {
                    $('.clinic-delete').hide();
                }

                $('.clinic-add').last().show();
            });

        },
        validateClinic: function () {
            $('body').on('change', '.user-clinic_id', function () {
                var brotherInfo = $(this).siblings('.help-block').html();
                if ($(this).val && brotherInfo == '选择诊所不能为空') {
                    $(this).parent().removeClass('has-error').addClass('.has-success');
                    $(this).siblings('.help-block').html('');
                }
                /*var userClinic = $(this).val();
                var count = 0;
                var that = $(this);
                var equal;
                $('.user-clinic_id').each(function () {
                    if (userClinic == $(this).val()) {
                        count++;
                    }
                });
                if (userClinic && count > 1) {
                    that.val('');
                    showInfo('请选择不同的诊所', '200px', 2);
//                    that.parents('.col-sm-4').next('.col-sm-4').find('#user-department').html('');
                    return false;
                } else {
                    main.selectRoom($(this));
                }*/
                main.selectRoom($(this));
                main.validateClinicInfo();
            });
            $('.user-clinic_id').each(function () {
                var val = $(this).val();
                var tipsInfo = $(this).siblings('.help-block');
                var tipsHtml = tipsInfo.html();
                if (val != '' && (tipsHtml != '选择诊所不能重复' || tipsHtml == '所属诊所不能为空。')) {
                    $(this).parent().removeClass('has-error').addClass('.has-success');
                    tipsInfo.html('');
                }
            });
            $('body').on('change', '.user-department_id', function () {
                var userClinic = $(this).parent().parent().prev().children().children().eq(1).val();
                var userDepartment = $(this).val();
                 var count = 0;
                 var that = $(this);
                 $('.user-department_id').each(function () {
                     if (userDepartment == $(this).val() && userClinic == $(this).parent().parent().prev().children().children().eq(1).val()) {
                        count++;
                     }
                 });
                 if (userDepartment && count > 1) {
                 that.val('');
                 showInfo('操作失败 请选择不同的科室', '200px', 2);
                 return false;
                 }
            });
            main.validateDepartmentInfo(); // 诊所下科室重复时清楚多余的报错信息
        },
        validateClinicInfo: function () {
            $('.user-clinic_id').each(function () {
                var val = $(this).val();
                var tipsInfo = $(this).siblings('.help-block');
                var tipsHtml = tipsInfo.html();
                console.log(val,'val');
                console.log(tipsHtml,'tipsInfo');
                if (val) {
                    $(this).parent().removeClass('has-error').addClass('.has-success');
                    tipsInfo.html('');
                }
            })
        },
        validateDepartmentInfo: function () {
            var departments = $('.user-department_id');
            var i = -1;
            var departs = {};
            //var index = new Array();
            $('.user-clinic_id').each(function () {
                i++;
                var clinic = $(this).val();
                var depart = departments.eq(i).val()?departments.eq(i).val():0;
                var val = clinic+','+depart;
                if(departs[val] == null){ // 根据键取值，没有则不是重复
                    departments.eq(i).parent().removeClass('has-error').addClass('.has-success');
                    departments.eq(i).siblings('.help-block').html('');
                }
                /*else {
                    departments.eq(index[0]).parent().removeClass('has-error').addClass('.has-success');
                    departments.eq(index[0]).siblings('.help-block').html('');
                    index = new Array();
                    index.push(i);
                }*/
                departs[val] = depart;  //在对象中赋值
            });
        }
    };
    return main;
})