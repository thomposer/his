define(function(require) {
    var calendarset = require('public/plugins/easyhincalendar/easyhinappointment');
    var bootstrap_timepicker = require('plugins/timepicker/bootstrap-timepicker.min');
    var template = require('template');
    var common = require('js/lib/common');
    viewJson = {
        "success": true,
        "errorCode": 0,
        "msg": "",
        "data": [
            {
                "date": "2017-05-08",
                "usedAppointmentCount": 3,
                "maxAppointmentCount": 4,
                "appointment": [
                    {
                        "status": 0,
                        "usedAppointmentCount": 1,
                        "maxAppointmentCount": 4,
                        "doctorName": "吴琴",
                        "doctorId": "128"
                    },
                    {
                        "status": 1,
                        "usedAppointmentCount": 3,
                        "maxAppointmentCount": 4,
                        "doctorName": "吴琴",
                        "doctorId": "129"
                    }
                ]
            },
            {
                "date": "2017-05-09",
                "usedAppointmentCount": 3,
                "maxAppointmentCount": 4,
                "appointment": [
                    {
                        "status": 1,
                        "usedAppointmentCount": 3,
                        "maxAppointmentCount": 4,
                        "doctorName": "吴琴",
                        "doctorId": "128"
                    },
                    {
                        "status": 1,
                        "usedAppointmentCount": 3,
                        "maxAppointmentCount": 4,
                        "doctorName": "吴琴",
                        "doctorId": "129"
                    }
                ]
            },
            {
                "date": "2017-05-10",
                "usedAppointmentCount": 3,
                "maxAppointmentCount": 4,
                "appointment": [
                    {
                        "status": 1,
                        "usedAppointmentCount": 3,
                        "maxAppointmentCount": 4,
                        "doctorName": "吴琴",
                        "doctorId": "128"
                    },
                    {
                        "status": 1,
                        "usedAppointmentCount": 3,
                        "maxAppointmentCount": 4,
                        "doctorName": "吴琴",
                        "doctorId": "129"
                    }
                ]
            },
            {
                "date": "2017-05-11",
                "usedAppointmentCount": 3,
                "maxAppointmentCount": 4,
                "appointment": [
                    {
                        "status": 1,
                        "usedAppointmentCount": 3,
                        "maxAppointmentCount": 4,
                        "doctorName": "吴琴",
                        "doctorId": "128"
                    },
                    {
                        "status": 1,
                        "usedAppointmentCount": 3,
                        "maxAppointmentCount": 4,
                        "doctorName": "吴琴",
                        "doctorId": "129"
                    }
                ]
            },
            {
                "date": "2017-05-12",
                "usedAppointmentCount": 3,
                "maxAppointmentCount": 4,
                "appointment": [
                    {
                        "status": 1,
                        "usedAppointmentCount": 3,
                        "maxAppointmentCount": 4,
                        "doctorName": "吴琴",
                        "doctorId": "128"
                    },
                    {
                        "status": 1,
                        "usedAppointmentCount": 3,
                        "maxAppointmentCount": 4,
                        "doctorName": "吴琴",
                        "doctorId": "129"
                    }
                ]
            },
            {
                "date": "2017-05-13",
                "usedAppointmentCount": 3,
                "maxAppointmentCount": 4,
                "appointment": [
                    {
                        "status": 1,
                        "usedAppointmentCount": 3,
                        "maxAppointmentCount": 4,
                        "doctorName": "吴琴",
                        "doctorId": "128"
                    },
                    {
                        "status": 1,
                        "usedAppointmentCount": 3,
                        "maxAppointmentCount": 4,
                        "doctorName": "吴琴",
                        "doctorId": "129"
                    }
                ]
            },
            {
                "date": "2017-05-14",
                "usedAppointmentCount": 3,
                "maxAppointmentCount": 4,
                "appointment": [
                    {
                        "status": 1,
                        "usedAppointmentCount": 3,
                        "maxAppointmentCount": 4,
                        "doctorName": "吴琴",
                        "doctorId": "128"
                    },
                    {
                        "status": 1,
                        "usedAppointmentCount": 3,
                        "maxAppointmentCount": 4,
                        "doctorName": "吴琴",
                        "doctorId": "129"
                    }
                ]
            }
        ],
        "total": [
            {
                "usedAppointmentCount": 3,
                "maxAppointmentCount": 4,
                "doctorName": "吴琴",
                "doctorId": "128"
            },
            {
                "usedAppointmentCount": 3,
                "maxAppointmentCount": 4,
                "doctorName": "吴琴",
                "doctorId": "129"
            }
        ]
    };
    var main = {
        thisCell:null,
        nowYearMonthDate:nowYearMonthDate,
        init: function() {
            main.initAppointmentRecord();
            main.bind();
        },
        bind:function(){
          $('.search_button').unbind('click').click(function(){
              main.getAppointmentSetInfo();
          });
        },
        initAppointmentRecord: function() {
            var now = new Date(nowYearMonthDate);
            var nowDayOfWeek = now.getDay() - 1; //今天本周的第几天,按星期一为第一天
            nowDayOfWeek < 0 ? nowDayOfWeek = 6 : "";
            var	currentWeekStartDay = formatDate(new Date(now.getFullYear(), now.getMonth(),now.getDate() - nowDayOfWeek));
            var	currentWeekEndDay = formatDate(new Date(now.getFullYear(), now.getMonth(), now.getDate() + (6 - nowDayOfWeek)));
            $.ajax({
                url: apiAppointmentGetAppointmentDetail,
                data: {
                    'startDate': currentWeekStartDay,
                    'endDate': currentWeekEndDay,
                    'doctorId': $("#appointmentsearch-doctor_id").val(),
                    'type': $("#appointmentsearch-type").val()  //服务类型
                },
                type: "post",
                dataType: 'json',
                success: function(json, response) {
                    if (json.errorCode == 0) {
                        var data = main.serializeAppointmentGridConfig(json);
                        var configDate = {
                            tableTitle:' ',
                            headChange: true,
                            column: data.column,
                            row: data.row,
                            datajson: data.data,
                            readOnly: false,
                            addPlus:true,//是否要mouseover “+添加”字样
                            switchWeekCb: function() {
                                main.getAppointmentSetInfo();
                            },
                            tableCellCb: function(thisCell) {
                                main.thisCell = thisCell;
                            }
                        };
                        $('#schedule_grid').easyhinGrid(configDate);
                        $('#schedule_grid').easyhinGrid('refreshCellData', data.occupation); //刷新职业列数据

                    } else {
                        showInfo("失败！" + json.msg, "300px");
                    }
                },
                error: function(x) {

                }
            });
        },
        serializeAppointmentGridConfig:function(json){
            var data = json.data,
                total = json.total,
                totalUsedAppointmentCount = 0,
                totalMaxAppointmentCount = 0,
                result = {},
                column = [{
                    "name":"医生",
                    "data-source":"doctor_name",
                    "readonly":true,
                    "appoint-num":"1人/10人" //1人/10人
                }],
                row = [],
                rowOb = {},
                gridDate = [];
            day = ["周一","周二","周三","周四","周五","周六","周日"];

            for(var i = 0;i < data.length;i++){

                var column_head = {},
                    dateNewMate = data[i].date,
                    appoitNum = '',
                    //已预约人数的URL
                    head_href = (data[i].usedAppointmentCount == 0) ? '' : ' role="modal-remote" data-modal-size = "large" href=' +apiAppointmentMessage + '?time=' + dateNewMate + '&header_type=1&entrance=1&date_formate=2&doctor_id=' + json.doctorId;
                    head_tag = (data[i].usedAppointmentCount == 0) ? 'span' : 'a';

                    appoitNum = '<'+head_tag+' class="appoint-num"   '+head_href+ '>'+ data[i].usedAppointmentCount + '人</'+head_tag+'>';
                    appoitNum += ' / '+data[i].maxAppointmentCount+'人';

                    appointment = data[i].appointment;
                    totalUsedAppointmentCount += data[i].usedAppointmentCount;
                    totalMaxAppointmentCount += data[i].maxAppointmentCount;
                column_head['name'] = day[i]+calendarHeadFormatDate(dateNewMate);
                column_head['appoint-num'] = appoitNum;
                column_head['data-source'] = dateNewMate;

                column.push(column_head);

                for(var j = 0;j < appointment.length;j++){

                    var schedule = appointment[j],gridCell = {};
                    var href = (schedule.usedAppointmentCount == 0) ? ' class="grey"' : ' class="appointment-info-left appointment-info-hover" role="modal-remote" data-modal-size = "large"'+ 'href=' + apiAppointmentMessage + '?time=' + dateNewMate + '&doctor_id=' + schedule.doctorId + '&header_type=1&entrance=1&isDoctor=1';
                    var tag =  (schedule.usedAppointmentCount == 0) ? 'span class="appointment-info-left"':'a';

                    //最大预约人数的URL
                    var max_href = (schedule.maxAppointmentCount == 0 ) ? '' : ' class="appointment-info-right appointment-info-hover" role="modal-remote" data-modal-size = "large" href=' +apiAppointmentDoctorTimeList + '?date=' + dateNewMate + '&doctorId=' + schedule.doctorId + '&doctorName=' + htmlEncodeByRegExp(schedule.doctorName);
                    var max_tag = (schedule.maxAppointmentCount == 0 ) ? 'span class="appointment-info-right"' : 'a';
                        gridCell['data-row-source'] = schedule.doctorId;
                        gridCell['data-column-source'] = dateNewMate;
                        gridCell['status'] = schedule.status;
                        gridCell['appointmentStatus'] = schedule.appointmentStatus;
                        gridCell['usedAppointmentCount'] = schedule.usedAppointmentCount;
                        gridCell['content'] = '<'+tag + href + '>'+schedule.usedAppointmentCount + '</'+tag+'>/ '+ '<'+ max_tag + max_href +'>' +schedule.maxAppointmentCount + '' + '</' + max_tag + '>';
                        gridDate.push(gridCell);
                    rowOb[schedule.doctorId] = {
                        'doctorName': total[j].doctorName,
                        'usedAppointmentCount': total[j].usedAppointmentCount,
                        'maxAppointmentCount': total[j].maxAppointmentCount
                    };


                }
            }

            for(v in rowOb){
                var row_head = {};
                row_head['name'] = rowOb[v].doctorName; //医生名字
                var doctorHref = (rowOb[v].usedAppointmentCount == 0) ? ' class="grey"' : ' role="modal-remote" data-modal-size = "large" '+ 'href=' + apiAppointmentMessage + '?time=' + data[0].date + '&endDate=' + data[6].date + '&doctor_id=' + v + '&header_type=1&entrance=1&isDoctor=1';
                var doctorTag =  (rowOb[v].maxAppointmentCount == 0) ? 'span':'a';
                row_head['content'] =  '<' + doctorTag + ' class="appoint-num"   ' + doctorHref + '>' + rowOb[v].usedAppointmentCount + '人</' + doctorTag + '>/' + rowOb[v].maxAppointmentCount + '人',
                row_head['data-source'] = v;  //doctorId
                row.push(row_head);
            }


            // 对第一个方格的处理
            column[0]['appoint-num'] = totalUsedAppointmentCount + ' 人 / '+ totalMaxAppointmentCount + '人';

            result['column'] = column;//[{ name="职业",data-source="occupation",readonly=true},{ name="周一(05/02)",data-source="2016-05-02"}]
            result['row'] = row;//[{ name="王晓辉",data-source="11"},{name="刘医生",data-source="12"}]
            result['data'] = gridDate;//[{ data-row-source="12",data-column-source="2016-05-04",data-source=1,content='全天班'}]

            return result;
        },
        getAppointmentSetInfo:function(){
            var datejson = $('#schedule_grid').data('easyhinCalendar').options.datejson;
            $.ajax({
                url: apiAppointmentGetAppointmentDetail,
                data: {
                    'startDate':datejson.currentWeek[0],//预约起始时间
                    'endDate':datejson.currentWeek[6],//结束时间
                    'doctorId': $("#appointmentsearch-doctor_id").val(),
                    'type': $("#appointmentsearch-type").val() //服务类型
                },
                type: "post",
                dataType: 'json',
                success: function (json, response) {

                    if (json.errorCode == 0) {
                        var data = main.serializeAppointmentGridConfig(json);
                        var configDate = {
                            tableTitle:' ',
                            headChange: true,
                            column: data.column,
                            row: data.row,
                            datajson : data.data,
                            initDate :datejson.currentWeek[0],
                            readOnly: false,
                            addPlus:true,//是否要mouseover “+添加”字样
                            switchWeekCb: function() {
                                main.getAppointmentSetInfo();
                            },
                            tableCellCb: function(thisCell) {
                                main.thisCell = thisCell;

                            }
                        };
                        $('#schedule_grid').easyhinGrid('refreshGrid',configDate);
                        $('#schedule_grid').easyhinGrid('refreshCellData', data.occupation); //刷新职业列数据

                    } else {
                        showInfo("失败！" + json.msg, "300px");
                    }
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
    return "("+date[1]+"."+date[2]+")";
}

