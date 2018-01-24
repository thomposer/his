(function ($, window) {
    //初始化日历值
    var defaults = {
        contentHeight: 700,
        initDate: "",
        switchDayCb: function () {
        },
        refreshDate: function () {
        },
        thisWeekCb: function () {
        }
    };

    $.fn.easyhinCalendarConfig = function (options) {
        var args = Array.prototype.slice.call(arguments, 1); // for a possible method call
        var res = this; // what this function will return (this jQuery object by default)
        //将传入配置参数合并入默认值
        // var options = $.extend(defaults,options);
        this.each(function (i, _element) { // loop each DOM element involved
            var element = $(_element);
            var calendar = element.data('easyhinCalendar'); // get the existing calendar object (if any)
            var singleRes; // the returned value of this single method call
            // a method call
            if (typeof options === 'string') {
                //当传入参数为字符串且日历初始化后的缓存数据存在且为函数则执行
                if (calendar && $.isFunction(calendar[options])) {
                    singleRes = calendar[options].apply(calendar, args);
                    if (!i) {
                        res = singleRes; // record the first method call result
                    }
                    if (options === 'destroy') { // for the destroy method, must remove Calendar object data
                        element.removeData('easyhinCalendar');
                    }
                }
            }
            // a new calendar initialization
            else if (!calendar) { // don't initialize twice
                calendar = new Calendar(element, options);
                element.data('easyhinCalendar', calendar);
                calendar.render();
            }
        });
        return this;
    }
    // Recursively combines option hash-objects.
    // Better than `$.extend(true, ...)` because arrays are not traversed/copied.
    //
    // called like:
    //     mergeOptions(target, obj1, obj2, ...)
    //
    function mergeOptions(target) {
        function mergeIntoTarget(name, value) {
            if ($.isPlainObject(value) && $.isPlainObject(target[name]) && !isForcedAtomicOption(name)) {
                // merge into a new object to avoid destruction
                target[name] = mergeOptions({}, target[name], value); // combine. `value` object takes precedence
            } else if (value !== undefined) { // only use values that are set and not undefined
                target[name] = value;
            }
        }

        for (var i = 1; i < arguments.length; i++) {
            $.each(arguments[i], mergeIntoTarget);
        }

        return target;
    }

    function Calendar(element, instanceOptions) {
        var t = this;
        instanceOptions = instanceOptions || {};
        var options = mergeOptions({}, defaults, instanceOptions);
        t.options = options;

        // t.option = function() {

        // }
        t.updateDate = function (date) {
            updateDate(date);
        }
        t.render = function () {
            initialRender();
        }
        t.refresh = function () {
            refresh();
        }
        t.destroy = function () {
            destroy();
        }
        t.refreshDate = function (json) {
            refresh();
            refreshDate(json);
        }
        function refresh() {
            destroy();
            initialRender();
        }

        function destroy() {
            element.html("");
        }

        function updateDate() {

        }

        function initialRender() {
            element.addClass("eh eh-unthemed eh-outcontent");
            getWeekDate(t.options.initDate);
            viewCalendarTable();
            viewToolBar();
        }

        function viewToolBar() {
            var a = '', o = t.options.datejson, b = t.options.title, c = t.options.readDate, nowdate = initWeekDate(formatDate(new Date(nowYearMonthDate)));
            now = new Date(nowYearMonthDate);

            if (t.options.initDate != '') {
                var nowDay = t.options.initDate.substring(8, 10), //当前日
                    nowMonth = t.options.initDate.substring(5, 7), //当前月
                    nowYear = t.options.initDate.substring(0, 4); //当前年
                    var currentDay = t.options.initDate;
            } else {
                var nowDay = now.getDate(), //当前日
                        nowMonth = now.getMonth() + 1, //当前月
                        nowYear = now.getFullYear(); //当前年
                if (nowMonth >= 1 && nowMonth <= 9) {
                    nowMonth = "0" + nowMonth;
                }
                var currentDay = getNowFormatDate();
            }

            a +=
                '<div class="eh-toolbar add-padding">' +
                '<div class="eh-left">' +
                '<div class="eh-button-group">' +
                '<button data-date="' + o.prevDay + '" class="eh-prev-button eh-button  eh-update-button" type="button">' +
                    // '<span class="eh-icon eh-icon-left-single-arrow"></span>' +
                '<i class="fa fa-angle-left"></i>'+
                '</button>' +
                '<button data-date="' + o.nextDay + '" class="eh-next-button eh-button  eh-update-button" type="button">' +
                    // '<span class="eh-icon eh-icon-right-single-arrow"></span>' +
                '<i class="fa fa-angle-right"></i>'+
                '</button>' +
                '</div>';

            a += '<button data-date="' + o.today + '" class="eh-thisweek-button  eh-button eh-state-default eh-corner-left eh-corner-right" type="button">今天</button>';
            a += '<div class="col-sm-6 select-time">' +
                 '<div class="form-group canlender-border">' +
                 '<input readonly="true" type="text" class="form-control empty-time appointment-select-time-timepicker" placeholder=" ' + currentDay + ' ">' +
                 '</div>' + 
                 '</div>';
            a += '</div>' +
                '<div class="eh-center doctor-appointment-detail">' +
                '<h2>' + nowYear + '年' + nowMonth + '月' + nowDay + '日' + '医生预约详情' + '</h2>' +
                '</div>'
            ;
            // '<div class="eh-right">' +
            // '<button class="eh-agendaWeek-button eh-button eh-state-default eh-corner-left eh-corner-right eh-state-active" type="button">周</button>' +
            // '</div>' +
            if (c) {
                a +=
                    '<div class="eh-center">' +
                    '<h2>' + b + o.currentWeek[0] + '—' + o.currentWeek[6] + '</h2>' +
                    '</div>';
            }
            a +=
                '<div class="eh-clear"></div>' +
                '</div>';
            $(a).prependTo(element).find(".eh-update-button").click(function () {
                t.options.initDate = $(this).attr("data-date");
                refresh();
                t.options.switchDayCb();
            });
            element.find(".eh-thisweek-button").click(function () {

                t.options.initDate = $(this).attr("data-date");
                refresh();
                t.options.switchDayCb();
            });
            
            $('.appointment-time-timepicker').remove();
            $('.appointment-select-time-timepicker').datetimepicker({
                language: 'zh-CN',
                format: 'yyyy-mm-dd',
                minView: 2,
                initialDate: currentDay,
                autoclose: true,
                weekStart: 1,
                forceParse:false,
                pickerPosition: 'bottom-right appointment-time-timepicker',
            });
            $('.appointment-select-time-timepicker').bind('change', function () {
                if (!/^(\d{4})-(\d{1,2})-(\d{1,2})$/.test($(this).val())) {
                    showInfo('时间格式错误', '180px', 2);
                }else{
                    t.options.initDate = $(this).val();
                }
                refresh();
                t.options.switchDayCb();
            });
        }

        function viewCalendarTable() {
            var a = '';
            a +=
                '<div class="eh-view-container" style="">' +
                '<div class="eh-view eh-agendaWeek-view eh-agenda-view" style="">' +
                '<table>' +
                viewCalendarHead() +
                '<tbody>' +
                '<tr>' +
                '<td class="eh-widget-content">' +
                '<div class="eh-time-grid-container eh-scroller" >' +
                viewCalendarTime() +
                '</div>' +
                '</td>' +
                '</tr>' +
                '</tbody>' +
                '</table>' +
                '</div>' +
                '</div>';
            $(a).prependTo(element);
        }

        function viewCalendarHead() {
            var a = '', doctor = options.doctorInfo;
            a += '<thead>' +
                '<tr>' +
                '<td class="eh-widget-header">' +
                '<div class="eh-row eh-widget-header" style="">' +
                '<table>' +
                '<thead>' +
                '<tr>' +
                '<th class="eh-day-header eh-widget-header eh-appointment-doctor-date">时间</th>'+
                '<th style="width: 0.1px;background-color: #DFE0E4;"></th>';
            $.each(doctor, function (key, val) {
                a += '<th class="eh-day-header eh-widget-header"> ' + htmlEncodeByRegExp(val["doctor_name"]) + '</th>';
            });
            a +=
                '</tr>' +
                '</thead>' +
                '</table>' +
                '</div>' +
                '</td>' +
                '</tr>' +
                '</thead>';
            return a;
        }


        function viewCalendarTime() {
            var a = '', timeline = options.timeLine, doctor = options.doctorInfo;
            var nowdate = initWeekDate(formatDate(new Date(nowYearMonthDate))).today;
            a +=
                '<div class="eh-time-grid">' +
                '<div class="eh-slats doctor-eh-slats">' +
                '<table>' +
                '<tbody>';
            if (timeline.length != 0) {
                for (var i = 0; i < timeline.length; i++) {
                    var timearr = timeline[i].split(':');
                    for (var z = 0; z < 3; z++) {
                        a += '<tr>';
                        for (var j = 0; j < doctor.length; j++) {
                        //a += '<td>';
                        var time = timearr[1];


                            var minute = (timearr[1] * 1 + z * 10);
                            var hour = timearr[0] * 1;
                            if (minute >= 60) {
                                hour = hour * 1 + 1 * 1;
                                minute = minute - 60;
                            }
                            if (hour < 10) {
                                hour = '0' + hour;
                            }
                            if (minute < 10) {
                                minute = '0' + minute;
                            }

                            if(j == 0 && z == 0){
                                a += '<td rowspan="3" class="eh-axis eh-time eh-widget-content" style="width: 50px;">' +
                                    '<span>' + timeline[i] + '</span>' +
                                    '</td>';
                            }
                            if(j==0){
                                a += '<td style="width: 0.1px;background-color: #DFE0E4;"></td>';
                            }

                            a += '<td data-date="' + hour + ':' + minute + '" column_id="' + doctor[j]["doctor_id"] + '" column_doctor_name="' + htmlEncodeByRegExp(doctor[j]["doctor_name"]) + '" class="border-none eh-widget-content eh-appointment-set-daily-content eh-appointmet-doctor">' +
                                '</td>';

                        }
                        //a += '</td>';
                        a += '</tr>';
                    }

                }
            }
            a +=
                '</tbody>' +
                '</table>' +
                '</div>' +
                '</div>';
            return a;
        }

        function refreshDate(json) {
            var o = options.datejson, a = '', doctor = options.doctorInfo, a = '';
            now = new Date(nowYearMonthDate);

            if (t.options.initDate != '') {
                var nowDay = t.options.initDate.substring(8, 10), //当前日
                    nowMonth = t.options.initDate.substring(5, 7), //当前月
                    nowYear = t.options.initDate.substring(0, 4); //当前年
            } else {
                var nowDay = now.getDate(), //当前日
                    nowMonth = now.getMonth() + 1, //当前月
                    nowYear = now.getFullYear(); //当前年
                nowHour = now.getHours(); //当前时
                nowMinutes = now.getMinutes(); //当前分
            }

            for (var i = 0; i < doctor.length; i++) {
                var target = ".eh-appointment-set-daily-content[column_id='" + doctor[i]["doctor_id"] + "']";

                $(target).each(function () {
                    var date = $(this).attr("data-date"), column_id = $(this).attr("column_id"), data, data_date = [];

                    if (typeof(json.data[doctor[i]["doctor_id"]]) == "undefined") {
                        a = '<span class="eh-appointment-set-daily-add eh-appointment-set-daily-add-disable appointment-add appointment-detal">' +
                            '</span>';
                    } else if (t.options.initDate != '' && t.options.initDate < o.today) {
                        if (typeof(json.data[doctor[i]["doctor_id"]][date]) != "undefined" && json.data[doctor[i]["doctor_id"]][date]['status'] == '3') {
                            //$(target).addClass('has-appointment');
                            a = '<div class="iform-doctor_appoint"><span class="eh-appointment-set-daily-detail-cell eh-appointment-doctor-span"></span></div>';
                        } else {
                            a = '<span class="eh-appointment-set-daily-add eh-appointment-set-daily-add-disable appointment-add appointment-detal">' +
                                '</span>';
                        }
                    } else if (t.options.initDate <= o.today && date.substr(0, 2) < nowHour) {
                        if (typeof(json.data[doctor[i]["doctor_id"]][date]) != "undefined" && json.data[doctor[i]["doctor_id"]][date]['status'] == '3') {
                            //$(target).addClass('has-appointment');
                            a = '<div class="iform-doctor_appoint"><span class="eh-appointment-set-daily-detail-cell eh-appointment-doctor-span"></span></div>';
                        } else {
                            a = '<span class="eh-appointment-set-daily-add eh-appointment-set-daily-add-disable appointment-add appointment-detal">' +
                                '</span>';
                        }
                    } else if (t.options.initDate <= o.today && date.substr(0, 2) == nowHour && date.substr(3, 2) < nowMinutes) {
                        if (typeof(json.data[doctor[i]["doctor_id"]][date]) != "undefined" && json.data[doctor[i]["doctor_id"]][date]['status'] == '3') {
                            //$(target).addClass('has-appointment');
                            a = '<div class="iform-doctor_appoint"><span class="eh-appointment-set-daily-detail-cell eh-appointment-doctor-span"></span></div>';
                        } else {
                            a = '<span class="eh-appointment-set-daily-add eh-appointment-set-daily-add-disable appointment-add appointment-detal">' +
                                '</span>';
                        }
                    } else if (typeof(json.data[doctor[i]["doctor_id"]][date]) == "undefined") {
                        a = '<span class="eh-appointment-set-daily-add eh-appointment-set-daily-add-disable appointment-add appointment-detal">' +
                            '</span>';
                    } else if (json.data[doctor[i]["doctor_id"]][date]['status'] == '1') {

                        if(json.data[doctor[i]["doctor_id"]][date]["rowSpan"]){
                            $(this).attr('rowspan',json.data[doctor[i]["doctor_id"]][date]["rowSpan"]);
                            //$(this).removeClass('has-appointment');
                        }else {
                            $(this).hide();
                        }

                        a = '<a class="canAppoint" href="' + viewAppointmentCreatbyDoctor + '?date=' + nowYear + '-' + nowMonth + '-' + nowDay + ' ' + date + '&doctor_id=' + doctor[i]["doctor_id"] + '"  role="modal-remote" data-modal-size = "small">' +
                            '<div class="iform-docto"><span class="eh-mouseover iform-mouseover" style="display: none;line-height: '+ ((json.data[doctor[i]["doctor_id"]][date]["rowSpan"])*16+1) +'px;"><i class="fa fa-plus"></i>添加</span></div>' +
                            '</a> ';
                    } else if (json.data[doctor[i]["doctor_id"]][date]['status'] == '2') {
                        a = '<span class="eh-appointment-set-daily-add eh-appointment-set-daily-add-disable appointment-add appointment-detal">' +
                            '</span>';
                    } else if (json.data[doctor[i]["doctor_id"]][date]['status'] == '3') {
                        //$(target).addClass('has-appointment');
                        a = '<div class="iform-doctor_appoint"><span class="eh-appointment-set-daily-detail-cell eh-appointment-doctor-span"></span></div>';
                    }
                    $(this).html(a);
                    $('.iform-doctor_appoint').parent('td').addClass('has-appointment');
                   //if(json.data[doctor[i]["doctor_id"]][date]['status']){
                    //
                    //}
                })
            }

            $(".eh-appointment-set-daily-add-enable").unbind("click").click(function () {
                var $target = $(this).parents(".eh-appointment-set-daily-content");
                t.options.thisCell = $target;
                options.tableCellCb($target);
            });
            $('.disable').prevAll().addClass("font-color");
            $('.disable').prevAll().children('.eh-appointment-set-daily-detail-count').addClass("font-color");
            var parentDiv = $('.eh-mouseover').parent();
            var pencil = $('.pencil').parent();
            parentDiv.mouseover(function () {
                $(this).children('.eh-mouseover').show();
            });
            parentDiv.mouseout(function () {
                $(this).children('.eh-mouseover').hide();
            });
            pencil.mouseover(function () {
                $(this).children('.pencil').addClass('eh-appointment-set-daily-detail-edit-img eh-appointment-set-daily-add-enable');
            });
            pencil.mouseout(function () {
                $(this).children('.pencil').removeClass('eh-appointment-set-daily-detail-edit-img eh-appointment-set-daily-add-enable');
            });
        }

        function formatDate(date) {
            var myyear = date.getFullYear();
            var mymonth = date.getMonth() + 1;
            var myweekday = date.getDate();

            if (mymonth < 10) {
                mymonth = "0" + mymonth;
            }
            if (myweekday < 10) {
                myweekday = "0" + myweekday;
            }
            return (myyear + "-" + mymonth + "-" + myweekday);
        }

        function calendarHeadFormatDate(string) {
            var date = string.split("-");
            return "(" + date[1] + "/" + date[2] + ")";
        }

        //获得本周的开端日期
        function getWeekDate(date) {
            t.options.datejson = initWeekDate(date);
        }

        function initWeekDate(date) {
            var now;
            date ? now = new Date(date) : now = new Date(nowYearMonthDate);
            var nowDayOfWeek = now.getDay() - 1, //今天本周的第几天,按星期一为第一天
                nowDate = new Date(nowYearMonthDate),
                nowDay = now.getDate(), //当前日
                nowMonth = now.getMonth(), //当前月
                nowYear = now.getFullYear(); //当前年
            nowDayOfWeek < 0 ? nowDayOfWeek = 6 : "";
            var weekStartDate = formatDate(new Date(nowYear, nowMonth, nowDay - nowDayOfWeek)),
                weekEndDate = formatDate(new Date(nowYear, nowMonth, nowDay + (6 - nowDayOfWeek))),
                prevWeekStartDate = formatDate(new Date(nowYear, nowMonth, nowDay - nowDayOfWeek - 7)),
                nextWeekStartDate = formatDate(new Date(nowYear, nowMonth, nowDay + (6 - nowDayOfWeek + 1))),
                prevMonthStartDay = formatDate(new Date(nowYear, nowMonth - 1, 1)),
                nextMonthStartDay = formatDate(new Date(nowYear, nowMonth + 1, 1)),
                thisMonthStartDay = formatDate(new Date(nowYear, nowMonth, 1)),
                thisMonthEndDay = formatDate(new Date(nowYear, nowMonth + 1, 0)),
                prevDay = formatDate(new Date(nowYear, nowMonth, nowDay - 1)),
                nextDay = formatDate(new Date(nowYear, nowMonth, nowDay + 1));
            currentMonthStartDay = formatDate(new Date(nowDate.getFullYear(), nowDate.getMonth(), 1));
            currentMonthEndDay = formatDate(new Date(nowDate.getFullYear(), nowDate.getMonth() + 1, 0));
            currentWeek = [], currentMonth = [];
            for (var i = 0; i < 7; i++) {
                currentWeek.push(formatDate(new Date(nowYear, nowMonth, nowDay - nowDayOfWeek + i)));
            }
            for (var i = 1; i <= thisMonthEndDay.split("-")[2]; i++) {
                currentMonth.push(formatDate(new Date(nowYear, nowMonth, i), true));
            }
            datejson = {
                "weekStartDate": weekStartDate,
                "weekEndDate": weekEndDate,
                "thisMonthStartDay": thisMonthStartDay,
                "thisMonthEndDay": thisMonthEndDay,
                "prevMonthStartDay": prevMonthStartDay,
                "nextMonthStartDay": nextMonthStartDay,
                "prevWeekStartDate": prevWeekStartDate,
                "nextWeekStartDate": nextWeekStartDate,
                "today": formatDate(nowDate),
                "currentMonthStartDay": currentMonthStartDay,
                "currentMonthEndDay": currentMonthEndDay,
                "currentWeek": currentWeek,
                "currentMonth": currentMonth,
                "prevDay": prevDay,
                "nextDay": nextDay,
            }
            return datejson;
        }
        
        function getNowFormatDate() {
            var date = new Date();
            var seperator = "-";
            var month = date.getMonth() + 1;
            var strDate = date.getDate();
            if (month >= 1 && month <= 9) {
                month = "0" + month;
            }
            if (strDate >= 0 && strDate <= 9) {
                strDate = "0" + strDate;
            }
            var currentdate = date.getFullYear() + seperator + month + seperator + strDate;
            return currentdate;
        }
    }

    // });
})(jQuery, window);