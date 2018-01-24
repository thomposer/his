define(function (require) {
    var template = require('public/plugins/easyhincalendar/easyhincalendar');
    var closeAppointment = require('public/js/make_appointment/closeAppointment');

    var main = {
        init: function () {
            $('#calendar').easyhinCalendar({
                baseUrl: baseUrl,
                closeAppointment: closeAppointmentSwitch,
                colseAppointmentStatus: colseAppointmentStatus,
                timeLineSpilt: timeLineSpilt,
                spiltLength: spiltLength,
                timeLine: timeLine, //时间间隔
                entrance: entrance, //入口区分
                closeTimeLine: closeTimeLine, //时间间隔
                nowYearMonthDate: nowYearMonthDate, //当前时间
                //closeAppointmentTime: closeAppointmentTime,//时间间隔
                switchWeekCb: function () {
                    main.getAppointmentIndex();
                },
                title: " ", //title显示的内容
                readDate: false, //是否显示日期
                position: position,
                viewAppointmentMessage: viewAppointmentMessage,
                doctorId: doctorId,
            });
            main.getAppointmentIndex();
            $('.search_button button[type="button"]').on('click', function () {
                main.getAppointmentIndex();
            })
        },
        getAppointmentIndex: function () {
            var datejson = $('#calendar').data('easyhinCalendar').options.datejson;
            var type = '';
            if (document.getElementById("appointmentsearch-type") || document.getElementById("appointmentsearch-doctor_id")) {
                var type = document.getElementById("appointmentsearch-type").value;
                var doctor_id = document.getElementById("appointmentsearch-doctor_id").value;
            } else {
                var doctor_id = doctorId;
            }
            $.ajax({
                url: appointIndex,
                data: {
                    //'appointment_type':appointment_type,//预约类型
                    'type': type, //服务类型ID
                    'doctor_id': doctor_id, //选择医生ID
                    'start_date': datejson.weekStartDate, //预约周起始时间
                    'end_date': datejson.weekEndDate, //结束时间
                },
                type: "post",
                dataType: "json",
                success: function (json, response) {
                    $(".eh-day-total").each(function () {
                        var date = $(this).attr('data-date'),
                                daydate = json.appoint_daily_total[date];
                        try {
                            if (daydate && daydate['amCount']) {

                                $(this).find(".eh-day-moning span").text(daydate['amCount']);
                                $(this).find(".eh-day-moning").children(":first").addClass("eh-day-span-value");
                            } else {

                                $(this).find(".eh-day-moning span").text('0');
                                $(this).find(".eh-day-moning span").removeClass('eh-day-span-value');
                            }
                            if (daydate && daydate['pmCount']) {
                                $(this).find(".eh-day-afternoon span").text(daydate['pmCount']);
                                $(this).find(".eh-day-afternoon").children(":first").addClass("eh-day-span-value");
                            } else {

                                $(this).find(".eh-day-afternoon span").text('0');
                                $(this).find(".eh-day-afternoon span").removeClass('eh-day-span-value');
                            }
                        } catch (x) {

                        }
                    });
                    $(".eh-daily-content").each(function () {
                        var date = $(this).attr('data-date');
                        if (json.appoint_daily_detail[date]) {

                            var content = '<a role="modal-remote" data-modal-size="large" href="' + viewAppointmentMessage + '?time=' + date + '&appointment_type=' + '&type=' + type + '&entrance=' + entrance + '&doctor_id=' + doctor_id + '&header_type=2' + '"  ><span class=\"appointment-style\">' + json.appoint_daily_detail[date] + '人</span></a>';

                            $(this).find('.appointment-people-num').html(content);
                        } else {
                            $(this).find('.appointment-people-num').html('');
                        }
                    });
                },
                error: function (x) {

                }
            });
            $('.header_click').on('click', function () {
                main.doModal(this);
            })
        },
        doModal: function (obj) {
            var type = '';
            if (document.getElementById("appointmentsearch-type") || document.getElementById("appointmentsearch-doctor_id")) {
                var type = document.getElementById("appointmentsearch-type").value;
                var doctor_id = document.getElementById("appointmentsearch-doctor_id").value;
            } else {
                var doctor_id = doctorId;
            }
            var url= $(obj).attr('data-url');
            $(obj).attr('data-url',url+'&type='+type+'&doctor_id='+doctor_id);
            return false;;
            modal.open(obj, data)
        }


    };

    return main;
});