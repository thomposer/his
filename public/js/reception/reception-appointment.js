define(function(require) {
    var claendarset = require('public/plugins/easyhincalendar/easyhinschedule');
    var common = require('js/lib/common');
    var main = {
        thisCell:null,
        nowYearMonthDate:nowYearMonthDate,
        init: function() {
            main.initScheduleRecord();
        },
        searchToolBar:function () {
            $("#search").click(function () {
                var username = $("#appointmentsearch-username").val();
                var selectOption = $("#appointmentsearch option:selected").val();
                main.getAppointmentSetInfo(username,selectOption);
            });
        },
        serializeScheduleGridConfig:function(json){
            var data = json,result = {},
                column = [{
                    "name":"员工",
                    "data-source":"doctor_name",
                    "readonly":true
                },
                    {
                        "name":"科室",
                        "data-source":"department_name",
                        "readonly":true
                    }],
                row = [],
                rowOb = {},
                department_nameCell = {},//第二列职业数据
                gridDate = [];
            day = ["周一","周二","周三","周四","周五","周六","周日"];
            for(var i = 0;i < data.length;i++){
                var column_head = {},
                    date = data[i].date,
                    scheduls = data[i].scheduls;
                column_head['name'] = day[i]+calendarHeadFormatDate(date);
                column_head['data-source'] = date;
                column.push(column_head);
                for(var j = 0;j < scheduls.length;j++){
                    var schedule = scheduls[j];
                    if(schedule.schedule_time){
                        var gridCell = {};
                        gridCell['data-row-source'] = schedule.doctor_id;
                        gridCell['data-column-source'] = schedule.schedule_time;
                        gridCell['data-source'] = schedule.schedule_id;
                        gridCell['content'] = schedule.appointment_num;
                        gridDate.push(gridCell);
                    }
                    if(i == 0){
                        if(null != schedule.department_name){
                            if(undefined != department_nameCell[schedule.doctor_id]){
                                department_nameCell[schedule.doctor_id] = schedule.department_name+'，'+department_nameCell[schedule.doctor_id];
                            }else{
                                department_nameCell[schedule.doctor_id] = schedule.department_name;
                            }
                        }
                    }

                    rowOb[schedule.doctor_id] = schedule.doctor_name;
                }
            }
            for(v in rowOb){
                var row_head = {};
                row_head['name'] = rowOb[v];
                row_head['data-source'] = v;
                row.push(row_head);
            }
            var department_name = [];
            for(v in department_nameCell){
                var occupa = {};
                occupa['data-row-source'] = v;
                occupa['data-column-source'] = 'department_name';
                occupa['content'] = department_nameCell[v];
                department_name.push(occupa);
            }
            result['column'] = column;//[{ name="职业",data-source="department_name",readonly=true},{ name="周一(05/02)",data-source="2016-05-02"}]
            result['row'] = row;//[{ name="王晓辉",data-source="11"},{name="刘医生",data-source="12"}]
            result['data'] = gridDate;//[{ data-row-source="12",data-column-source="2016-05-04",data-source=1,content='全天班'}]
            result['department_name'] = department_name;
            return result;
        },
        initScheduleRecord: function() {
            var now = new Date(nowYearMonthDate);
                        var nowDayOfWeek = now.getDay() - 1; //今天本周的第几天,按星期一为第一天
                        nowDayOfWeek < 0 ? nowDayOfWeek = 6 : "";
			var	currentWeekStartDay = formatDate(new Date(now.getFullYear(), now.getMonth(),now.getDate() - nowDayOfWeek));
			var	currentWeekEndDay = formatDate(new Date(now.getFullYear(), now.getMonth(), now.getDate() + (6 - nowDayOfWeek)));
            $.ajax({
                url: apiAppointmentWorkstationIndex,
                data: {
                    'start_date': currentWeekStartDay,
                    'end_date': currentWeekEndDay,
                    'name': $("#schedulingsearch-user_id").val(),
                    'department_id': $("#schedulingsearch-schedule_id").val()
                },
                type: "post",
                dataType:"json",
                success: function(json, response) {
                    if (json.errorCode == 0) {
                        var data = main.serializeScheduleGridConfig(json.data);
                        var configDate = {
                            column: data.column,
                            row: data.row,
                            datajson: data.data,
                            readOnly: false,
                            headChange:true,
                            tableTitle:" ",
                            dataDepartment:json.department_list,
                            redCss:true,//true代表存在红色的人数样式
                            switchWeekCb: function() {
                                main.getAppointmentSetInfo();
                            },
                            tableCellCb: function(thisCell) {
                                // setScheduleModal.show();
                                main.thisCell = thisCell;
                                $("#set_schedule_select").val(thisCell.attr('data-source'));
                            }
                        };
                        $('#schedule_grid').easyhinGrid(configDate);
                        // $('#schedule_grid').easyhinGrid('refreshData', data.data);
                        $('#schedule_grid').easyhinGrid('refreshCellData', data.department_name); //刷新职业列数据
                        main.searchToolBar();

                    } else {
                        showInfo("失败！" + json.msg, "300px");
                    }
                },
                error: function(x) {

                }
            });
  
        },
        getAppointmentSetInfo:function(name,department_id){
            name?name:'';
            department_id?department_id:'';
            var datejson = $('#schedule_grid').data('easyhinCalendar').options.datejson;
            $.ajax({
                url: apiAppointmentWorkstationIndex,
                data: {
                    'start_date':datejson.currentWeek[0],//预约起始时间
                    'end_date':datejson.currentWeek[6],//结束时间
                    'name': name,
                    'department_id': department_id,

                },
                type: "post",
                dataType:"json",
                success: function (json, response) {

                    if (json.errorCode == 0) {
                        var data = main.serializeScheduleGridConfig(json.data);
                        var configDate = {
                            column: data.column,
                            row: data.row,
                            datajson : data.data,
                            initDate :datejson.currentWeek[0],
                            readOnly: false,
                            headChange:true,
                            tableTitle:" ",
                            dataDepartment:json.department_list,
                            redCss:true,//true代表存在红色的人数样式
                            switchWeekCb: function() {
                                main.getAppointmentSetInfo(name,department_id);
                            },
                            tableCellCb: function(thisCell) {
                                setScheduleModal.show();
                                main.thisCell = thisCell;
                                $("#set_schedule_select").val(thisCell.attr('data-source'));
                            }
                        };
                        $('#schedule_grid').easyhinGrid('refreshGrid',configDate);
                        // $('#schedule_grid').easyhinGrid('refreshData', data.data);
                        $('#schedule_grid').easyhinGrid('refreshCellData', data.department_name); //刷新职业列数据
                        $("#appointmentsearch-username").val(name);
                        $("#appointmentsearch").val(department_id);

                    } else {
                        showInfo("失败！" + json.msg, "300px");
                    }
                    main.searchToolBar();
                },
                error: function (x) {

                }
            });
        }
    };

    return main;
});
function calendarHeadFormatDate(string){
    var date = string.split("-");
    return "("+date[1]+"/"+date[2]+")";
}

