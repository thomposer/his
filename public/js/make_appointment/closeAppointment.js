define(function (require) {
    var template = require('template');
    var common = require('js/lib/common');
    //var bootstrap_timepicker = require('plugins/timepicker/bootstrap-datetimepicker');
    var closeAppointmentTpl = require('tpl/closeAppointment.tpl');
    var main = {
        init: function () {
            main.addCloseAppointment();
            main.bind();
        },
        bind: function () {
            $("body").on('click', '.btn-save', function () {
                var close_appointment = {};
                var close_info = [];
                var csrfToken = $('meta[name="csrf-token"]').attr('content');
                $('.row.appointConfig').each(
                        function () {
                            var a = {};
                            a.close_begin_time = $(this).find("input[name^='begin_time']").val();
                            a.close_end_time = $(this).find("input[name^='end_time']").val();
                            a.close_reason = $(this).find("input[name^='close_reason']").val();
                            close_info.push(a);
                        }
                );
                close_appointment.close_info = close_info;
                var vSt = 1;
                $(".appointConfig").each(function () {
                    var $beginTime = $(this).find(".appointment-config-begin-time"),
                            $endTime = $(this).find(".appointment-config-end-time");
                    if ($beginTime.val() == '') {
                        showCloseValidity($beginTime, "开始时间不能为空！");
                        vSt = 2;
                    } else {
                        showCloseValidity($beginTime);
                    }
                    if ($endTime.val() == '') {
                        showCloseValidity($endTime, "结束时间不能为空！");
                        vSt = 2;
                    } else {
                        showCloseValidity($endTime);
                    }
                    if ($beginTime.val() >= $endTime.val()) {
                        showCloseValidity($endTime, "结束时间必须大于开始时间！");
                        vSt = 2;
                    } else {
                        showCloseValidity($endTime);
                    }
                });
                if (vSt == 2) {
                    return;
                }
                $.ajax({
                    url: closeAppointmentSwitch,
                    data: {
                        close_appointment: close_appointment,
                        _csrf: csrfToken
                    },
                    type: "post",
                    dataType: "json",
                    success: function (json, response) {
                        if (json.errorCode == 0) {
                            window.location.reload();
                            showInfo(json.msg, '300px', 1);
                        }
                        if (json.errorCode == 1011) {
                            showInfo(json.msg, '300px', 2);
                        }
                        if (json.errorCode == 1012) {
                            showInfo(json.msg, '300px', 2);
                        }
                        if (json.errorCode == 1014) {
                            showInfo(json.msg, '300px', 2);
                        }
                        if (json.errorCode == 1015) {
                            showInfo(json.msg, '300px', 2);
                        }
                        if (json.errorCode == 1016) {
                            showInfo(json.msg, '300px', 2);
                        }
                    },
                    error: function (x) {

                    }
                });
            });
        },
        addCloseAppointment: function () {
            var length = $('.appointConfig').length;
            if (length == 1) {
                setTimeout("$('.clinic-delete').hide()", 0);
            }
            $('#appointConfig .appointConfig').last().find('.form-group a').css('display', 'inline-block');
            $('body').on('click', '.clinic-add', function () {
                console.log(main.getRect($(this)), 'ddd');
                var pos = 'bottom-right';
                if (main.getRect($(this)) < 200) {
                    pos = 'top-right';
                }
                var acbtLen = $('.appointment-config-begin-time').length;
                var key = acbtLen + 1;
                var clinicDepartment = template.compile(closeAppointmentTpl)({
                    selectbeginId: 'appointment-config-begin-time' + key,
                    selectendId: 'appointment-config-end-time' + key,
                });
                $('#appointConfig').append(clinicDepartment);
                $('.form_datetime').datetimepicker({
                    showInputs: true,
                    autoclose: true,
                    language: 'zh-CN',
                    minuteStep: 10,
                    allowInputToggle: true,
                    pickerPosition: pos,
                    clientOptions: {
                        allowInputToggle: true,
                    }
                });
                $('.clinic-add').hide();
                $('.clinic-delete').show();
                $('.clinic-delete').last().show();
                $('.clinic-add').last().show();

            });
            //$('#datetimepicker').datetimepicker('hide');
            $("body").on('focus', '.form_datetime', function () {
                $(this).datetimepicker({
                    showInputs: true,
                    autoclose: true,
                    language: 'zh-CN',
                    minuteStep: 10,
                    allowInputToggle: true,
                    clientOptions: {
                        autoclose: true,
                        allowInputToggle: true

                    }
                });
            });
            $("body").on('click', '.form_datetime', function () {
                $(this).datetimepicker({
                    showInputs: true,
                    autoclose: true,
                    language: 'zh-CN',
                    minuteStep: 10,
                    allowInputToggle: true,
                    clientOptions: {
                        //autoclose : true,
                        allowInputToggle: true

                    }
                });
            });

            $('body').on('click', '.clinic-delete', function () {
                var len = $('.appointConfig').length;
                $(this).parents(".appointConfig").remove();
                //console.log(len);
                if (len == 2) {
                    $('.clinic-delete').show();
                    $('.clinic-add').show();
                } else {
                    $('.clinic-delete').last().show();
                    $('.clinic-add').last().show();
                }

            });

        },
        getRect: function (elements) {
            var x = $(window).height() - elements.height() - (elements.offset().top - $(document).scrollTop());
            return x;
        },
    };

    return main;
});


