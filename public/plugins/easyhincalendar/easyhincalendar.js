(function ($, window) {
    //初始化日历值
    var defaults = {
        contentHeight: 700,
        initDate: "",
        spiltLength: spiltLength,
        switchWeekCb: function () {},
        thisWeekCb: function () {}
    };
    $.fn.easyhinCalendar = function (options) {

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
            var a = '', colseAppointmentStatus = options.colseAppointmentStatus, baseUrl = options.baseUrl, o = t.options.datejson, b = t.options.title, c = t.options.readDate, nowdate = initWeekDate(formatDate(new Date(nowYearMonthDate)));
            var position = options.position ? 'eh-right' : 'eh-left';
            var title = title ? title : title = '预约信息';
            a +=
                    '<div class="eh-toolbar">';
            if (options.position) {
                a += '<div class="eh-left">' +
                        '<h2>' + title + calendarHeadFormatDateLeft(o.currentWeek[0]) + '—' + calendarHeadFormatDateLeft(o.currentWeek[6]) + '</h2>' +
                        '</div>';
            }
            a +=
                    '<div class="' + position + '">' +
                    '<div class="eh-button-group">' +
                    '<button data-date="' + o.prevWeekStartDate + '" class="eh-prev-button eh-button  eh-update-button" type="button">' +
                    // '<span class="eh-icon eh-icon-left-single-arrow"></span>' +
                    // '<img class="" alt="" src="' + baseUrl + '/public/img/common/icon_last_page.png">' +
                    '<i class="fa fa-angle-left"></i>'+
                    '</button>';
            var re = new RegExp("-","g");//正则替换时间格式
            var selectStartTime = options.datejson.currentWeek[0].replace(re,'.');
            var selectEndTime = options.datejson.currentWeek[6].replace(re,'.');
            a += '<div class="appointment-select-time-range">' +
                '<div class="form-group canlender-border">' +
                '<input readonly="true" type="text" class="form-control empty-time schedule-list" value="" placeholder="' + selectStartTime + '-' + selectEndTime + '">' +
                '</div>' +
                '</div>';
            a+=
                    '<button data-date="' + o.nextWeekStartDate + '" class="eh-next-button eh-button  eh-update-button" type="button">' +
                    // '<span class="eh-icon eh-icon-right-single-arrow"></span>' +
                    // '<img class="" alt="" src="' + baseUrl + '/public/img/common/icon_next_page.png">' +
                    '<i class="fa fa-angle-right"></i>'+
                    '</button>' +
                    '</div>';
            if (o.weekStartDate == nowdate.weekStartDate) {
                a += '<button class="eh-thisweek-button  eh-button eh-state-default eh-corner-left eh-corner-right" type="button" disabled="disabled">本周</button>';
            } else {
                a += '<button data-date="' + o.today + '" class="eh-thisweek-button  eh-button eh-state-default eh-corner-left eh-corner-right" type="button">本周</button>';
            }
            a += '</div>';
            if (colseAppointmentStatus) {

             
                a += '<button  class=" btn-default close-appointment"  role="modal-remote" data-modal-size="large" href="' + closeAppointmentSwitch + '" >关闭预约</button>';
                a += '<div class="back-history btn-group pull-right " id="J-select-box">';
                a += '<a href="'+appointmentDetail+'"  class="btn btn-group-left">';
                a += '<span data-pjax="0">';
                a += '按医生展示' ;
                a += '</span>';
                a += '</a>';
                a += '<a href="'+appointmentIndex+'" class="btn btn-group-right active ">';
                a += '<span data-pjax="0">';
                a += '按时间段展示' ;
                a += '</span>';
                a += '</a>';
                a += '</div>';
            }
            // '<div class="eh-right">' +
            // '<button class="eh-agendaWeek-button eh-button eh-state-default eh-corner-left eh-corner-right eh-state-active" type="button">周</button>' +
            // '</div>' +
            if (c) {
                a +=
                        '<div class="eh-center">' +
                        '<h2>' + b + o.currentWeek[0] + '—' + o.currentWeek[6] + '</h2>' +
                        '</div>';
            }
            a += '<div class="eh-clear"></div>';
            a += '</div>';

            $(a).prependTo(element).find(".eh-update-button").click(function () {
                t.options.initDate = $(this).attr("data-date");
                refresh();
                t.options.switchWeekCb();
            });
            element.find(".eh-thisweek-button").click(function () {
                t.options.initDate = $(this).attr("data-date");
                refresh();
                t.options.switchWeekCb();
                t.options.thisWeekCb();
            });
            if (true) {
                $('.schedule-range-list').remove();
                $('.schedule-list').datetimepicker({
                    language:'zh-CN',
                    format: 'yyyy-mm-dd',
                    minView: 2,
                    initialDate: options.datejson.currentWeek[0],
                    autoclose:true,
                    weekStart:1,
                    forceParse:false,
                    pickerPosition:'bottom-right schedule-range-list'
                });
                $(document).off('mousedown');
                $('.schedule-list').on('mousedown', function (e) {    // Clicked outside the datetimepicker, hide it
                    if($('.schedule-range-list').attr('style').indexOf('display: block') == (-1)){
                        $('.schedule-range-list').show();
                    }else {
                        setTimeout(function () {
                            $('.schedule-range-list').hide();
                        },200);
                    }
                });
                $('.schedule-list').bind('change',function(){
                    if (!/^(\d{4})-(\d{1,2})-(\d{1,2})$/.test($(this).val())) {
                        showInfo('时间格式错误', '180px', 2);
                    }else{
                        t.options.initDate = $(this).val();
                    }
                    refresh();
                    t.options.switchWeekCb();
                    t.options.thisWeekCb();
                });
            }

        }
        function calendarHeadFormatDateLeft(string) {
            var date = string.split("-");
            return date[0] + '年' + date[1] + '月' + date[2] + '日';
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
                    viewCalendarToday() +
                    '<div class="eh-time-grid-container eh-scroller" style="border-radius: 12px;">' +
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
            var a = '', week = options.datejson.currentWeek;
            var weekHead = ['周一', '周二', '周三', '周四', '周五', '周六', '周日'];
            //var nowdate = initWeekDate(formatDate(new Date())).today;
            var nowdate = nowYearMonthDate;
            a += '<thead>' +
                    '<tr>' +
                    '<td class="eh-widget-header">' +
                    '<div class="eh-row eh-widget-header" style="border-right-width: 1px;">' +
                    '<table>' +
                    '<thead>' +
                    '<tr>' +
                    '<th class="eh-axis eh-widget-header appointment-table-header" style="width: 85px !important;"></th>';
            var type = '';
            var doctor_id = '';
            if (document.getElementById("appointmentsearch-type") || document.getElementById("appointmentsearch-doctor_id")) {
                type = document.getElementById("appointmentsearch-type").value;
                doctor_id = document.getElementById("appointmentsearch-doctor_id").value;
            } else {
                doctor_id = options.doctorId;
            }
            $.each(week, function (key, val) {
                var classHighLight = (nowdate==val)? 'eh-today-highlight-tip' : '';
                a += '<th class="eh-day-header eh-widget-header eh-mon appointment-table-header '+classHighLight+'"><a href="#" role="modal-remote" data-modal-size = "large" "contenttype" = "application/x-www-form-urlencoded" class="header_click" data-url="' + options.viewAppointmentMessage + '?time=' + val + '&appointment_type=' + '&entrance=' + options.entrance + '&header_type=1&date_formate=1' + '"  >';
                if (nowdate == val) {
                    a += '<div class="eh-title-highlight "></div> ' + weekHead[key] + calendarHeadFormatDate(val) + '<div class="eh-title-normal-appoint"></div>';
                } else {
                    a += '<div class="eh-title-normal-appoint"></div> ' + weekHead[key] + calendarHeadFormatDate(val) + '<div class="eh-title-normal-appoint"></div>';
                }
                a += '</a></th>'

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

        function viewCalendarToday() {
            var a = '', weekline = options.datejson.currentWeek;
            //var nowdate = initWeekDate(formatDate(new Date())).today;
            var nowdate = nowYearMonthDate;
            a +=
                    '<div class="eh-day-grid">' +
                    '<div class="eh-row eh-week eh-widget-content" style="border-right-width: 1px;height:62px">' +
                    '<div class="eh-bg">' +
                    '<table>' +
                    '<tbody>' +
                    '<tr>' +
                    '<td rowspan="2" class="eh-axis eh-widget-content" style="width: 85px;border-right: 1px solid #F7F9FC;">' +
                    '<span>预约</span>' +
                    '</td>';
            for (var i = 0; i < weekline.length; i++) {
                if (nowdate == weekline[i]) {
                    a += '<td class="eh-today-highlight eh-day-total eh-day eh-widget-content eh-mon eh-past eh-today-highlight-tip" data-date="' + weekline[i] + '">' +
                            '<span class="eh-day-moning eh-day-span">上午<span>0</span>人</span>' +
                            '<span class="eh-day-afternoon eh-day-span">下午<span>0</span>人</span>' +
                            '</td>';
                } else {
                    a += '<td class=" eh-day-total eh-day eh-widget-content eh-mon eh-past" data-date="' + weekline[i] + '">' +
                            '<span class="eh-day-moning eh-day-span">上午<span>0</span>人</span>' +
                            '<span class="eh-day-afternoon eh-day-span">下午<span>0</span>人</span>' +
                            '</td>';
                }

            }
            a += '</tr>' +
                    '</tbody>' +
                    '</table>' +
                    '</div>' +
                    '</div>' +
                    '</div>' +
                    '<hr class="eh-widget-header">';
            return a;
        }
        function viewCalendarTime() {
            var a = '', spiltLength = options.spiltLength, timeLineSpilt = options.timeLineSpilt, timeline = options.timeLine, weekline = options.datejson.currentWeek;
            var closeTimeLine = options.closeTimeLine;
            var lineHeight = 30 / spiltLength;
            var closeClass = '';
            var style = '';
            var title = "";
            var nowdatecontent = "";
            //var nowdate = initWeekDate(formatDate(new Date())).today;
            var nowdate = nowYearMonthDate;
            a +=
                    '<div class="eh-time-grid">' +
                    '<div class="eh-slats">' +
                    '<table>' +
                    '<tbody>';
            if (timeline.length != 0) {
                for (var i = 0; i < timeline.length; i++) {
                    a += '<tr>' +
                            '<td class="eh-axis eh-time eh-widget-content" style="width: 85px;">' +
                            '<span>' + timeline[i] + '</span>' +
                            '</td>';
                    for (var j = 0; j < weekline.length; j++) {
                        if (closeTimeLine[weekline[j]] != 'undefined') {
                            if (nowdate == weekline[j]) {
                                nowdatecontent = 'eh-today-highlight eh-widget-content eh-daily-content eh-today-highlight-tip';
                            } else {
                                nowdatecontent = 'eh-widget-content eh-daily-content';
                            }
                            a += '<td data-date="' + weekline[j] + ' ' + timeline[i] + '" class="' + nowdatecontent + '">';
                            a += '<div class="parent-close">';
                            a += '<div class="appointment-people-num">';
                            a += '</div>';
                            for (var h = 0; h < timeLineSpilt[timeline[i]].length; h++) {
                                style = "style='height:" + lineHeight + "px'";
                                var indexKey = weekline[j] + ' ' + timeLineSpilt[timeline[i]][h];
                                if ($.inArray(indexKey, closeTimeLine[weekline[j]]) !== -1) {
                                    closeClass = 'closeTimeClass';
                                    title = "预约时间已关闭";
                                } else {
                                    closeClass = '';
                                    title = '';
                                }
                                if (nowdate == weekline[j]) {
                                    a += '<div data-date="' + weekline[j] + ' ' + timeLineSpilt[timeline[i]][h] + '" title="' + title + '"   data-toggle="tooltip"  data-placement="auto left" class=" ' + closeClass + ' "  ' + style + '><span class="" ></span></div>';
                                } else {
                                    a += '<div data-date="' + weekline[j] + ' ' + timeLineSpilt[timeline[i]][h] + '" title="' + title + '"   data-toggle="tooltip"  data-placement="auto left" class="eh-widget-content eh-appointment-set-daily-content  ' + closeClass + ' "  ' + style + '><div class=""><span class=""   title="' + title + '" data-toggle="tooltip" data-placement="auto left" ></span></div></div>';
                                }
                            }
                        }
                        a += '</div>';
                        a += '</td>';

                    }
                    a += '</tr>';
                }
            }
            a +=
                    '</tbody>' +
                    '</table>' +
                    '</div>' +
                    '</div>';
            return a;
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
                    thisMonthEndDay = formatDate(new Date(nowYear, nowMonth + 1, 0));
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
                "currentMonth": currentMonth
            }
            return datejson;
        }
    }
    // });  
})(jQuery, window);