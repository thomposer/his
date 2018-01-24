define(function (require) {
    var claendarset = require('public/plugins/easyhincalendar/easyhinschedule');
    var bootstrap_timepicker = require('plugins/timepicker/bootstrap-timepicker.min');
    var template = require('template');
    var nav_template = require('public/js/lib/template-native');
    var common = require('js/lib/common');
    var appointmentTimeConfig = require('tpl/appointmentTimeConfigRow.tpl');
    var _self;
    var main = {
        thisCell: null,
        init: function () {
            _self = this;
            main.initScheduleRecord();
            main.bind();
            $('#ajaxCrudModal').css({"overflow-y": "auto"});
        },
        bind: function () {

            $('body').on('click', '.appointment-use-template', function () {
                var doctor_id = $(this).attr('doctor_id');
                var date = $(this).attr('date');
                var appointment_times = $(this).attr('appointment_times');
                var data = main.serializeAppointmentTemplate(appointment_times, doctor_id, date);

                main.submitAppointmentTimeTemplate(data, doctor_id, date);
            });

            $('#ajaxCrudModal').on('hidden.bs.modal', function () {
                $("body").removeClass('modal-open-appointment-template');
            });

            $('body').on('click', '.use-appointment-template-btn', function () {
                if (setScheduleModal != undefined && setScheduleModal != null) {
                    setScheduleModal.hide();
                    $("body").addClass('modal-open-appointment-template');
                }
            });

            $('body').on('click', '#time-template-cancel-btn', function () {
                if (setScheduleModal != undefined && setScheduleModal != null) {
                    setScheduleModal.show();
                }
            });

            $('body').on('click', '.eh-appointment-template-a', function () {
                showInfo("请先设置医生预约服务类型", "300px", 2);
                return;
            });

            _self.showMoreInfo();
        },
        serializeScheduleGridConfig: function (json) {
            var data = json, result = {},
                    column = [{
                            "name": "员工",
                            "data-source": "doctor_name",
                            "readonly": true
                        }],
                    row = [],
                    rowOb = {},
                    gridDate = [];
            day = ["周一", "周二", "周三", "周四", "周五", "周六", "周日"];
            for (var i = 0; i < data.length; i++) {
                var column_head = {},
                        date = data[i].date,
                        scheduls = data[i].scheduls;
                column_head['name'] = day[i] + calendarHeadFormatDate(date);
                column_head['data-source'] = date;
                column.push(column_head);
                for (var j = 0; j < scheduls.length; j++) {
                    var schedule = scheduls[j];
                    if (schedule.schedule_time) {
                        var gridCell = {};
                        gridCell['data-row-source'] = schedule.doctor_id;
                        gridCell['data-column-source'] = schedule.schedule_time;
                        gridCell['data-source'] = schedule.schedule_id;
                        gridCell['content'] = schedule.shift_name;
                        gridDate.push(gridCell);
                    }
                    rowOb[schedule.doctor_id] = [schedule.doctor_name, JSON.stringify(schedule.type)];
                }
            }
            for (v in rowOb) {
                var row_head = {};
                row_head['name'] = rowOb[v][0];
                row_head['data-source'] = v;
                row_head['data-doctor-type'] = rowOb[v][1];
                row.push(row_head);
            }
            var occupation = [];
            result['column'] = column;//[{ name="职业",data-source="occupation",readonly=true},{ name="周一(05/02)",data-source="2016-05-02"}]
            result['row'] = row;//[{ name="王晓辉",data-source="11"},{name="刘医生",data-source="12"}]
            result['data'] = gridDate;//[{ data-row-source="12",data-column-source="2016-05-04",data-source=1,content='全天班'}]
            return result;
        },
        appointmentConfigRowHtml: function (data, canServeTypes) {
            var html = '', data = data || [];
            if (data.length != 0) {
                for (var i = 0; i < data.length; i++) {
                    if (i > 0) {
                        data[i].removeTitleState = 1;
                    }
                    data[i].canServeTypes = canServeTypes;

                    html += nav_template.compile(appointmentTimeConfig)(data[i]);
                }
            } else {
                html = nav_template.compile(appointmentTimeConfig)({'canServeTypes': canServeTypes});
            }
            return html;
        },
        appointmentConfigRow: function () {
            $('.clinic-add').hide();
            $('.clinic-add').last().show();
            var len = $('.appointConfig').length;
//            if (len == 1) {
//                $('.clinic-delete').hide();
//            }
//            $('.clinic-delete').show();
            $(".appointConfig .btn").unbind("click").click(function () {
                $('.clinic-add').hide();
                if ($(this).hasClass("clinic-add")) {
                    var data = [{removeTitleState: 1}];
                    var canServeTypes = JSON.parse($('#setScheduleContent').attr('data-can-serve-types'));

                    $(this).parents(".modal-content-details").append(main.appointmentConfigRowHtml(data, canServeTypes));
                    $(this).prev().show();
//                    $(this).hide();
                }
                if ($(this).hasClass("clinic-delete")) {
                    $(this).parents(".appointConfig").remove();
                }
                $('.clinic-add').last().show();
                main.appointmentConfigRow();
            });
            $('.timepicker').datetimepicker({
                startDate: timeConfig.begin_time,
                endDate: timeConfig.end_time,
                language: 'zh-CN',
                startView: 1,
                minuteStep: 10,
                initialDate: '00:00',
                format: 'hh:ii',
                minView: 0,
                maxView: 1,
                formatViewType: 'time',
                autoclose: true
            });
            // $(".clinic-add").hide().last().show();


        },
        initScheduleRecord: function () {
            var now = new Date(nowYearMonthDate);
            var nowDayOfWeek = now.getDay() - 1; //今天本周的第几天,按星期一为第一天
            nowDayOfWeek < 0 ? nowDayOfWeek = 6 : "";
            var currentWeekStartDay = formatDate(new Date(now.getFullYear(), now.getMonth(), now.getDate() - nowDayOfWeek));
            var currentWeekEndDay = formatDate(new Date(now.getFullYear(), now.getMonth(), now.getDate() + (6 - nowDayOfWeek)));
            $.ajax({
                url: apiSchedulingIndex,
                data: {
                    'start_date': currentWeekStartDay,
                    'end_date': currentWeekEndDay,
                    'name': $("#schedulingsearch-user_id").val(),
                    'department_id': $("#schedulingsearch-schedule_id").val()
                },
                type: "post",
                success: function (json, response) {
                    var json = JSON.parse(json);

                    if (json.errorCode == 0) {
                        var data = main.serializeScheduleGridConfig(json.data);
                        var configDate = {
                            column: data.column,
                            row: data.row,
                            datajson: data.data,
                            readOnly: false,
                            headChange: true,
                            timeConfig: true,
                            topRadius: false,
                            copyWeek: true,
                            copyWeekConfig: copyWeekConfig,
                            tableTitle: " ",
                            appointmentTimeTemplateUrl: appointmentTimeTemplateUrl,
                            appointmentTimeTemplateListUrl: appointmentTimeTemplateListUrl,
                            switchWeekCb: function () {
                                main.getAppointmentSetInfo();
                            },
                            tableCellCb: function (thisCell) {
                                main.thisCell = thisCell;
//                                $("#set_schedule_select").val(thisCell.attr('data-source'));
                                main.viewScheduleList(thisCell);
                            }
                        };
                        $('#schedule_grid').easyhinGrid(configDate);
                        $('#schedule_grid').easyhinGrid('refreshDate', data.data);
//                        $('#schedule_grid').easyhinGrid('refreshCellData', data.occupation); //刷新职业列数据

                    } else {
                        showInfo("失败！" + json.msg, "300px");
                    }
                },
                error: function (x) {

                }
            });
        },
        getAppointmentSetInfo: function () {
            var datejson = $('#schedule_grid').data('easyhinCalendar').options.datejson;
            $.ajax({
                url: apiSchedulingIndex,
                data: {
                    'start_date': datejson.currentWeek[0], //预约起始时间
                    'end_date': datejson.currentWeek[6], //结束时间
                    'name': $("#schedulingsearch-user_id").val(),
                    'department_id': $("#schedulingsearch-schedule_id").val()
                },
                type: "post",
                success: function (json, response) {
                    var json = JSON.parse(json);
                    if (json.errorCode == 0) {
                        var data = main.serializeScheduleGridConfig(json.data);
                        var configDate = {
                            column: data.column,
                            row: data.row,
                            datajson: data.data,
                            initDate: datejson.currentWeek[0],
                            readOnly: false,
                            headChange: true,
                            timeConfig: true,
                            topRadius: false,
                            copyWeek: true,
                            copyWeekConfig: copyWeekConfig,
                            tableTitle: " ",
                            appointmentTimeTemplateUrl: appointmentTimeTemplateUrl,
                            appointmentTimeTemplateListUrl: appointmentTimeTemplateListUrl,
                            switchWeekCb: function () {
                                main.getAppointmentSetInfo();
                            },
                            tableCellCb: function (thisCell) {
                                main.thisCell = thisCell;
                                main.viewScheduleList(thisCell);
                            }
                        };
                        $('#schedule_grid').easyhinGrid('refreshGrid', configDate);
                        $('#schedule_grid').easyhinGrid('refreshDate', data.data);
//                        $('#schedule_grid').easyhinGrid('refreshCellData', data.occupation); //刷新职业列数据

                    } else {
                        showInfo("失败！" + json.msg, "300px");
                    }
                },
                error: function (x) {

                }
            });
        },
        setScheduleRecord: function (thisCell, data) {
            // showValidity($("#set_schedule_select"),"请选择一个对应班次!");
            // return false;
            $.ajax({
                url: scheduleSchedulingAddScheduling,
                data: data,
                type: "post",
                success: function (json, response) {
                    var json = JSON.parse(json);
                    if (json.errorCode == 0) {
                        setScheduleModal.hide();
                        $('#schedule_grid').easyhinGrid('refreshThisCellData', data);
                        if (json.schedule || json.clear == 1) {
                            showInfo("保存成功!", "200px");
                        } else if (json.schedulePermison == 2) {
                            showInfo("保存成功,注意该医生没有设置排班", "310px");
                        } else {
                            main.setUserSchedule(data, json.scheduleConf, json.scheduleConfPermison);
                        }
                    } else {
                        showInfo(json.msg, "300px", 2);
                    }
                },
                error: function (x) {

                }
            });
        },
        viewScheduleList: function (thisCell) {

            var canServeTypes = JSON.parse(thisCell.attr('data-doctor-type'));
            if (canServeTypes == undefined || canServeTypes.length == 0) {
                showInfo("请先设置医生预约服务类型", "300px", 2);
                return;
            }

            var data = [];
            var dataSelectType = JSON.parse(thisCell.attr('data-select-type'));

            for (var i = 0; i < dataSelectType.length; i++) {
                var item = dataSelectType[i];
                var a = {};
                var time = item['shift_name'].split("-");
                a.begin_time = time[0];
                a.end_time = time[1];
                a.selectedIDs = item['typeIdList'];
                data.push(a);
            }


            var thisFirstCell = thisCell.siblings().eq(0);
            var doctorName = thisFirstCell.find('span').text();
            var nowDate = thisCell.attr('data-column-source');
            var date = nowDate;
            var doctorId = thisCell.attr('data-row-source');
            var dateSplit = nowDate.split("-");
            nowDate = dateSplit[1] + "/" + dateSplit[2];
            var templateUrl = appointmentTimeTemplateListUrl + '?doctor_id=' + doctorId + '&date=' + date + '&type=1';
            var extender = '<a role="modal-remote" class="use-appointment-template-btn " href="' + templateUrl + '"><div class="use-appointment-template-box">模板管理</div></a>';
            extender += main.appointmentConfigRowHtml(data, canServeTypes);
            setScheduleModal = new easyhinModal({
                "id": "setSchedule",
                "title": "预约设置(" + doctorName + '-' + nowDate + ')',
                "confirmText": "保存",
                "extender": extender,
                "confirmCb": function () {
                    var verification = true;

                    $(".appointConfig").each(function () {

                        var $beginTime = $(this).find(".appoint-begin-time"),
                                $endTime = $(this).find(".appoint-end-time");
                        if ($beginTime.val() == '') {
                            showValidity($beginTime, "开始时间不能为空！");
                            verification = false;
                            return;
                        } else {
                            showValidity($beginTime);
                        }
                        if ($endTime.val() == '') {
                            showValidity($endTime, "结束时间不能为空！");
                            verification = false;
                            return;
                        } else {
                            showValidity($endTime);
                        }
                        if ($beginTime.val() >= $endTime.val()) {
                            showValidity($endTime, "结束时间必须大于开始时间！");
                            verification = false;
                            return;
                        } else {
                            showValidity($endTime);
                        }


                    });

                    if (verification) {
                        var sendData = main.serializeAppointmentConfig(setScheduleModal.thisModal, thisCell);
                        main.setScheduleRecord(thisCell, sendData);
                    }

                },
                "showCb": function () {

                }
            });
            setScheduleModal.show();

            $('#setScheduleContent').attr("data-can-serve-types", JSON.stringify(canServeTypes));

            main.appointmentConfigRow();

        },
        serializeAppointmentConfig: function ($modal, $td) {
            var dataDoctorType = JSON.parse($td.attr('data-doctor-type'));
            var doctorId = $td.attr("data-row-source"),
                    date = $td.attr("data-column-source"),
                    data = {}, daily_detail = [];
            $modal.find(".appointConfig").each(function () {
                var a = {};
                a.start_date = $(this).find(".appoint-begin-time").val();
                a.end_date = $(this).find(".appoint-end-time").val();

                var serveTypes = [];
                var serveTypeName = [];
                $(this).find('input[name="appointServeType"]:checked').each(function () {
                    serveTypes.push($(this).val());
                    for (var i = 0; i < dataDoctorType.length; i++) {
                        if (dataDoctorType[i].typeIdList == $(this).val()) {
                            serveTypeName.push(dataDoctorType[i].typeNameList);
                        }
                    }

                });
                a.serve_types = serveTypes;
                a.serveTypeNames = serveTypeName;

                daily_detail.push(a);
            });
            data.doctorId = doctorId;
            data.date = date;
            data.dailyDetail = daily_detail;
            data.reportDataType = 1;//数据上报区分类型
            return data;
        },
        serializeAppointmentTemplate: function (appointment_times, doctor_id, date) {

            var appointmentCell = main.getAppointmentCell(doctor_id, date);
            main.thisCell = appointmentCell;
            var doctorServe = JSON.parse($(appointmentCell).attr('data-doctor-type'));
            var serveTypes = [];
            var serveTypeNames = [];
            if (doctorServe != undefined && (doctorServe instanceof Array) && doctorServe.length > 0) {
                for (var i = 0; i < doctorServe.length; i++) {
                    var serve = doctorServe[i];
                    serveTypes.push(serve['typeIdList']);
                    serveTypeNames.push(serve['typeNameList']);
                }
            }
            var data = {};
            var daily_detail = [];
            if (appointment_times != null && appointment_times != undefined && appointment_times.length > 0) {
                var dataArr = appointment_times.split(',');
                for (var i = 0; i < dataArr.length; i++) {
                    var v = dataArr[i];
                    var timeArr = v.split('-');
                    var timeItem = {};
                    timeItem.start_date = timeArr[0];
                    timeItem.end_date = timeArr[1];
                    timeItem.serve_types = serveTypes;
                    timeItem.serveTypeNames = serveTypeNames;
                    daily_detail.push(timeItem);
                }
            }

            data.doctorId = doctor_id;
            data.date = date;
            data.dailyDetail = daily_detail;
            data.reportDataType = 2;//数据上报区分类型
            return data;

        },
        submitAppointmentTimeTemplate: function (data, doctor_id, date) {
            $.ajax({
                url: scheduleSchedulingAddScheduling,
                data: data,
                type: "post",
                success: function (json, response) {
                    var json = JSON.parse(json);
                    if (json.errorCode == 0) {
//                        var popWin = new ModalRemote('#ajaxCrudModal');
//                        popWin.hide();
//                        popWin = null;
                        modal.hide();
                        $('#schedule_grid').easyhinGrid('refreshThisCellData', data);
                        if (json.schedule || json.clear == 1) {
                            showInfo("保存成功!", "200px");
                        } else if (json.schedulePermison == 2) {
                            showInfo("保存成功,注意该医生没有设置排班", "310px");
                        } else {
                            main.setUserSchedule(data, json.scheduleConf, json.scheduleConfPermison);
                        }
                    } else {
                        showInfo(json.msg, "300px", 2);
                    }
                },
                error: function (x) {

                }
            });
        },
        getAppointmentCell: function (doctor_id, appointment_date) {
            return $(".eh-grid td[data-column-source ='" + appointment_date + "'][data-row-source = '" + doctor_id + "'] ").eq(0);
        },
        setUserSchedule: function (data, scheduleConf, scheduleConfPermison) {
            var thisFirstCell = main.thisCell.siblings().eq(0);
            var doctorName = thisFirstCell.find('span').text();
            var nowDate = main.thisCell.attr('data-column-source');
            var a = '';
            a += '<div style="width:80%;margin:0 auto">';
            a += '<div class="schedule-conf-title">预约时间段保存成功，设置医生排班</div>';
            a += '<select  class="form-control" id="set_schedule_select">';
            a += '<option value="0">';
            a += '选择班次';
            a += '</option>';
            for (var i = 0; i < scheduleConf.length; i++) {
                a += '<option value="' + scheduleConf[i].id + '" >';
                a += scheduleConf[i].shift_name;
                a += '</option>';
            }
            a += '</select>';
            if (scheduleConfPermison == 1) {
                a += '<a href="' + scheduleSchedulingIndex + '" target="_blank" class="scheduel-conf">班次配置</a>';
            }
            a += '</div>';
            setScheduleModal = new easyhinModal({
                "id": "setUserSchedule",
                "title": "排班配置(" + doctorName + '-' + nowDate + ')',
                "confirmText": "保存",
                "extender": a,
                "confirmCb": function () {
                    main.saveScheduleConf();
                },
                "showCb": function () {

                }
            });
            setScheduleModal.show();
        },
        saveScheduleConf: function () {
            var schedule_id = $("#set_schedule_select").val();
            if (!schedule_id) {
                var schedule_id = 0;
            }
            var date = main.thisCell.attr('data-column-source');
            var worker_id = main.thisCell.attr('data-row-source');
            $.ajax({
                url: addScheduleConfUrl,
                data: {
                    date: main.thisCell.attr('data-column-source'),
                    worker_id: main.thisCell.attr('data-row-source'),
                    schedule_id: schedule_id,
                    reportDataType: 1
                },
                type: "post",
                success: function (json, response) {
                    var json = JSON.parse(json)
                    if (json.errorCode == 0) {
//                        if (schedule_id != 0) {
//                            main.thisCell.attr("data-source", schedule_id).html('<span class="eh-appointment-set-daily-detail-time reception-span">' + htmlEncodeByRegExp(schedule_value) + '</span>');
//                        } else {
//                            main.thisCell.removeAttr("data-source").html(main.viewScheduleSelect());
//                        }
                        setScheduleModal.hide();
                        showInfo("保存成功!", "300px");
                    } else {
                        showInfo("系统异常，保存失败！" + json.msg, "300px");
                    }
                },
                error: function (x) {

                }
            });
        },
        // 当有tooltip超过外部盒子高度产生滚动条时，对外部盒子进行加高,使其不产生滚动条
        // 解决预约时间设置界面tooltip问题【此处待优化】
        showMoreInfo:function(){
            $('body').on('mouseenter','.eh-appointment-set-daily-detail',function(e){
                var me = $(this);
                var wrapperEle = $('.appointment-index');//出现滚动条的元素
                var oldHeight = wrapperEle.height();//元素原先的高度
                var hideHeight = wrapperEle[0].scrollHeight-wrapperEle[0].clientHeight;//出现滚动条时隐藏了的高度
                var tooltipEle = $(this).siblings('.tooltip');
                if(tooltipEle.length > 0 && hideHeight > 0){//当存在tooltip及有垂直滚动条时
                    $('.appointment-index').height(oldHeight);//先还原高度
                    $('.appointment-index').height(function(i,h){//元素高度进行加高
                        return h+hideHeight;
                    });
                    tooltipEle.mouseenter(function(){
                        me.mouseenter();
                    });
                    tooltipEle.mouseleave(function(){
                        me.mouseleave();
                    });
                }
                
            });
        },
    };

    return main;
});
function calendarHeadFormatDate(string) {
    var date = string.split("-");
    return "(" + date[1] + "/" + date[2] + ")";
}

