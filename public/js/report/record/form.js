/**
 * 
 */
define(function (require) {
    var template = require('template');
    var common = require('js/lib/common');
    var cropper = require('dist/cropper');
    var uploadFile = require('tpl/uploadModal.tpl');
    var upload = require('upload/main');
//    var cityPickerData = require('js/lib/city-picker.data');
//    var cityPicker = require('js/lib/city-picker');
    var appointment = require('tpl/appointment.tpl');
    var main = {
        init: function () {

            var uploadModal = template.compile(uploadFile)({
                title: '上传头像',
                url: uploadUrl,
            });
            $('#crop-avatar').append(uploadModal);
            $('.field-patient-username').on('keyup', function () {
                $(this).css('position', 'relative');
                var csrfToken = $('meta[name="csrf-token"]').attr('content');
                $.ajax({
                    type: 'get',
                    url: getPatients,
                    data: {
                        'patientName': $('#patient-username').val(),
                        '_csrf': csrfToken
                    },
                    success: function (json) {
                        $('.J-search-name').remove();
                        if (json.data.length >= 1) {

                            var appointmentModal = template.compile(appointment)({
                                list: json.data,
                                baseUrl: baseUrl,
                                cdnHost : cdnHost
                            });
                            $('.field-patient-username').append(appointmentModal);
                        }
                    },
                    error: function () {

                    },
                    dataType: 'json'

                });

            });
            $('body').on('click', '.J-name-search-submit', function () {
                var id = $(this).attr('id');
                window.location.href = updateUrl + '?id=' + id;
            });
            $('body').on('click', function () {
                $('.J-search-name').remove();
            });
            this.addClinicDepartment();
            this.activeForm();
            this.saveFormInfo();
        },
        addClinicDepartment: function () {

            $('.family-delete').hide();
            $('body').on('click', '.family-add', function () {

                var familyList = $('.family-list').html();
                $('#family').append('<div class ="family-list">' + familyList + '</div>');
                $('.family-add').hide();
                $('.family-delete').hide();
                $('.family-list').last().children().children().children('.form-group').children('a').show();

            });
            $('body').on('click', '.family-delete', function () {
                var len = $('.family-list').length;
                if (len == 2) {
                    $('.family-delete').hide();
                }
                $('.family-list').first().remove();
            });
            $('body').on('mouseover', '.patient-family_birthday', function () {
                $(this).datepicker({"autoclose": true, "format": "yyyy-mm-dd", "language": "zh-CN"});
            })
            $('body').on('click', '.patient-family_birthday', function () {
                $(this).on('changeDate', function (e) {
                    $(this).val(e.format());
                });

            })

        },
        activeForm: function () {
            var error = 0;
            $('body').on('focusout', '.patient-family_name', function () {

                var val = $(this).val();
                if (!val) {
                    $(this).parent().addClass('has-error');
                    $(this).siblings('.help-block').html('姓名不能为空。');
                    error++;
                } else {
                    $(this).parent().removeClass('has-error');
                    $(this).parent().addClass('has-success');
                    $(this).siblings('.help-block').html('');
                }
            });
            $('body').on('focusout', '.patient-family_relation', function () {

                var val = $(this).val();
                if (!val) {
                    $(this).parent().addClass('has-error');
                    $(this).siblings('.help-block').html('成员关系不能为空。');
                    error++;
                } else {
                    $(this).parent().removeClass('has-error');
                    $(this).parent().addClass('has-success');
                    $(this).siblings('.help-block').html('');
                }
            });
            $('body').on('focusout', '.patient-family_sex', function () {

                var val = $(this).val();
                if (!val) {
                    $(this).parent().addClass('has-error');
                    $(this).siblings('.help-block').html('性别不能为空。');
                    error++;
                } else {
                    $(this).parent().removeClass('has-error');
                    $(this).parent().addClass('has-success');
                    $(this).siblings('.help-block').html('');
                }
            });
            $('body').on('focusout', '.patient-family_birthday', function () {

                var val = $(this).val();
                if (!val) {
                    $(this).parent().addClass('has-error');
                    $(this).siblings('.help-block').html('出生日期不能为空。');
                    error++;
                } else {
                    $(this).parent().removeClass('has-error');
                    $(this).parent().addClass('has-success');
                    $(this).siblings('.help-block').html('');
                }
            });
            $('body').on('focusout', '.patient-family_iphone', function () {

                var val = $(this).val();
                var pattern = /^\d{11}$/;
                if (!val) {
                    $(this).parent().addClass('has-error');
                    $(this).siblings('.help-block').html('手机号码不能为空。');
                    error++;
                } else if (!pattern.exec(val)) {
                    $(this).parent().removeClass('has-success');
                    $(this).parent().addClass('has-error');
                    $(this).siblings('.help-block').html('手机号码是无效的。');
                    error++;
                } else {
                    $(this).parent().removeClass('has-error');
                    $(this).parent().addClass('has-success');
                    $(this).siblings('.help-block').html('');
                }
            });
            $('body').on('focusout', '.patient-family_card', function () {

                var val = $(this).val();
                var pattern = /^(\d{15}$|^\d{18}$|^\d{17}(\d|X|x))$/;
                if (val && !pattern.exec(val)) {
                    $(this).parent().removeClass('has-success');
                    $(this).parent().addClass('has-error');
                    $(this).siblings('.help-block').html('身份证号是无效的。');
                    error++;
                } else {
                    $(this).parent().removeClass('has-error');
                    $(this).parent().addClass('has-success');
                    $(this).siblings('.help-block').html('');
                }
            });
            return error;
        },
    };
    return main;
})