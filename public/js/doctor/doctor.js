define(function (require) {
    var claendarset = require('public/plugins/easyhincalendar/easyhinschedule');
//    var template = require('template');
    var common = require('js/lib/common');
    var main = {
        thisCell: null,
        nowYearMonthDate: nowYearMonthDate,
        init: function () {
            main.initScheduleRecord();
            main.searchDoctor();
            main.userJump();
        },
        bind: function () {
            var today = main.curentTime();
            $('[data-column-source=doctor_name]').addClass('table-header');
            $('.eh-widget-header').find('th').eq(0).addClass('table-header');
            $('[data-source=' + nowYearMonthDate + ']').addClass('today-bg');
        },
        serializeScheduleGridConfig: function (json) {
            var data = json, result = {},
                column = [{
                    "name": "日期",
                    "data-source": "doctor_name",
                    "readonly": true
                }
                ],
                row = [],
                rowOb = {},
                occupationCell = {}, //第二列职业数据
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
                    occupationCell[schedule.doctor_id] = schedule.occupation;
                    rowOb[schedule.doctor_id] = schedule.doctor_name;
                }
            }
            for (v in rowOb) {
                var row_head = {};
                row_head['name'] = rowOb[v];
                row_head['data-source'] = v;
                row.push(row_head);
            }
            var occupation = [];
            for (v in occupationCell) {
                var occupa = {};
                occupa['data-row-source'] = v;
                occupa['data-column-source'] = 'occupation';
                occupa['content'] = occupationCell[v];
                occupation.push(occupa);
            }
            result['column'] = column;//[{ name="职业",data-source="occupation",readonly=true},{ name="周一(05/02)",data-source="2016-05-02"}]
            result['row'] = row;//[{ name="王晓辉",data-source="11"},{name="刘医生",data-source="12"}]
            result['data'] = gridDate;//[{ data-row-source="12",data-column-source="2016-05-04",data-source=1,content='全天班'}]
            result['occupation'] = occupation;
            return result;
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
                },
                type: "post",
                dataType: 'json',
                success: function (json, response) {
                    if (json.errorCode == 0) {
                        var data = main.serializeScheduleGridConfig(json.data);
                        var configDate = {
                            column: data.column,
                            row: data.row,
                            // colseAppointmentStatus:true,
                            datajson: data.data,
                            readOnly: true,
                            addPlus: false,//是否要mouseover “+添加”字样
                            tableTitle: "排班",
                            switchWeekCb: function () {
                                main.getAppointmentSetInfo();
                            },
                            tableCellCb: function (thisCell) {
                                //setScheduleModal.show();
                                main.thisCell = thisCell;
                                $("#set_schedule_select").val(thisCell.attr('data-source'));
                            }
                        };
                        $('#schedule_grid').easyhinGrid(configDate);
                        $('#schedule_grid').easyhinGrid('refreshCellData', data.occupation); //刷新职业列数据
                        main.bind();

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
                dataType: 'json',
                success: function (json, response) {
                    if (json.errorCode == 0) {
                        var data = main.serializeScheduleGridConfig(json.data);
                        var configDate = {
                            column: data.column,
                            row: data.row,
                            datajson: data.data,
                            initDate: datejson.currentWeek[0],
                            readOnly: true,
                            // colseAppointmentStatus:true,
                            tableTitle: "排班",
                            switchWeekCb: function () {
                                main.getAppointmentSetInfo();
                            },
                            tableCellCb: function (thisCell) {
                                //setScheduleModal.show();
                                main.thisCell = thisCell;
                                $("#set_schedule_select").val(thisCell.attr('data-source'));
                            }
                        };
                        $('#schedule_grid').easyhinGrid('refreshGrid', configDate);

                        $('#schedule_grid').easyhinGrid('refreshCellData', data.occupation); //刷新职业列数据
                        main.bind();

                    } else {
                        showInfo("失败！" + json.msg, "300px");
                    }
                },
                error: function (x) {

                }
            });
        },
        searchDoctor: function () {
            $('#doctor_search').click(function () {
                var doctor_name = $('.search-input').val();
                if (doctor_name == '') {
                    showInfo('请输入内容', '120px', 2);
                    return false;
                }
                var $this = $(this);
                var url = $(this).data('url');
                var method = $(this).data('request-method');
                var data = {doctor_name: doctor_name,};
                $.ajax({
                    url: url,
                    method: method,
                    data: JSON.stringify(data),
                    async: false,
                    error: function (response) {
                        showInfo('操作失败', '120px', 2);
                    },
                    success: function (response) {
                        if (response.errorCode) {
                            showInfo('无搜索结果', '120px', 2, '抱歉');
                        } else {
                            modal.open($this, JSON.stringify(data));
                        }
                    },
                    contentType: false,
                    cache: false,
                    processData: false
                });

            });
        },
        userJump: function () {
            $('body').on('click', '.thumb-doctor', function () {
                if (canJump == 1) {
                    window.location.href = $(this).data('url');
                } else {
                    showInfo('如有疑问，请联系管理员', '180px', 2, '暂无权限');
                }

            })
        },
        curentTime: function () {
            var now = new Date(nowYearMonthDate);
            var y = now.getFullYear();
            var m = now.getMonth() + 1;//获取当前月份的日期
            var d = now.getDate();
            if (m < 10) {
                var new_m = '0' + m;
            } else {
                var new_m = m;
            }
            var curentTime = y + "-" + new_m + "-" + d;
            return curentTime;

        }
    };

    return main;
});
function calendarHeadFormatDate(string) {
    var date = string.split("-");
    return "(" + date[1] + "/" + date[2] + ")";
}

