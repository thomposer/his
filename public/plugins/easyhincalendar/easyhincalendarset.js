(function ($, window) {
    //初始化日历值
    var defaults = {
        column: {
            departName: [],
            departId: [],
        },
        copyWeekConfig: "",
        selectOptionState: false,
        contentHeight: 700,
        readOnly: false,
        header: {
            backBtnUrl: "javascript:void(0)"
        },
        initDate: "",
        switchWeekCb: function () {},
        thisWeekCb: function () {},
        tableCellCb: function (x) {}
    };
    $.fn.easyhinCalendarSet = function (options) {
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
    function mergeOptions(target) {//依赖于jquery-ui.js
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
        // var options = mergeOptions({}, defaults, instanceOptions);
        var options = $.extend({}, defaults, instanceOptions);
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
        t.refreshDate = function (json) {
            refresh();
            refreshDate(json);
        }
        t.destroy = function () {
            destroy();
        }
        t.refreshCellData = function (data) {
            refreshCellData(data);
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
            var initDate = t.options.initDate;

            viewCalendarTable();
            viewToolBar();
        }

        function viewToolBar() {
            var a = '', o = t.options.datejson, b = t.options.selectOptionState;
            if (b) {
                var data_date = o.prevWeekStartDate;
                var end_date = o.nextWeekStartDate;
                var wording = "本周";
                var titleStartTime = calendarHeadFormatDate(o.thisWeekStartDay);
                var titleEndTime = calendarHeadFormatDate(o.thisWeekEndDay);
                var startTime = o.currentWeekStartDay;
            } else {
                var data_date = o.prevMonthStartDay;
                var end_date = o.nextMonthStartDay;
                var wording = "本月";
                var titleStartTime = calendarHeadFormatDate(o.thisMonthStartDay);
                var titleEndTime = calendarHeadFormatDate(o.thisMonthEndDay);
                var startTime = o.currentMonthStartDay;
            }

            a +=
                    '<div class="eh-toolbar">' +
                    '<div class="eh-left">' +
                    '<div class="eh-button-group">' +
                    '<button data-date="' + data_date + '" class="eh-prev-button eh-button  eh-update-button" type="button">' +
                    // '<span class="eh-icon eh-icon-left-single-arrow"></span>' +
                    '<img class="" alt="" src="' + baseUrl + '/public/img/common/icon_last_page.png">' +
                    '</button>' +
                    '<button data-date="' + end_date + '" class="eh-next-button eh-button  eh-update-button" type="button">' +
                    // '<span class="eh-icon eh-icon-right-single-arrow"></span>' +
                    '<img class="" alt="" src="' + baseUrl + '/public/img/common/icon_next_page.png">' +
                    '</button>' +
                    '</div>';
            if (o.thisMonthStartDay == o.currentMonthStartDay) {
                a += '<button class="eh-thisweek-button  eh-button eh-state-default eh-corner-left eh-corner-right " type="button" >' + wording + '</button>';
            } else {
                a += '<button data-date="' + startTime + '" class="eh-thisweek-button  eh-button eh-state-default eh-corner-left eh-corner-right" type="button">' + wording + '</button>';
            }

            a +=
                    '</div>' +
                    // '<div class="eh-right">' +
                    // '<button class="eh-agendaWeek-button eh-button eh-state-default eh-corner-left eh-corner-right eh-state-active" type="button">周</button>' +
                    // '</div>' +
                    '<div class="eh-center">' +
                    '<h2>' + titleStartTime + '—' + titleEndTime + '</h2>' +
                    '</div>' +
                    '<div class="eh-right">' +
                    // '<span class="eh-toolbar-back-btn">' +
                    // '<a href="'+t.options.header.backBtnUrl+'">< 返回</a>' +
                    // '</span>'+
                    '<div id="J-select-box" class="btn-group">' +
                    '<span class="btn btn-group-left">' +
                    '<a>' +
                    '<i class=" commonSearchStatus btn-background">' +
                    '</i>' +
                    '按周设置' +
                    '</a>' +
                    '</span>' +
                    '<span class="btn btn-group-right ">' +
                    '<a>' +
                    '</i>' +
                    '按月设置' +
                    '</a>' +
                    '</span>' +
                    '</div>';
            if (b) {
                a +=
                        '<div  class="btn-group">' +
                        '<span class="btn btn-group-left active copy-last-week">' +
                        '<a data-confirm-message="此操作会覆盖当前设置，请谨慎操作！" data-delete="" data-confirm-title="系统提示" data-toggle="tooltip" role="modal-remote" data-request-method="post" data-pjax="1" aria-label="复制上周" title=""  data-original-title="复制上周" data-url="' + copyWeekConfig + '?prevWeekStartDate=' + data_date + '" data-request-method="post">' +
                        '<i class=" commonSearchStatus btn-background">' +
                        '</i>' +
                        '复制上周' +
                        '</a>' +
                        '</span>' +
                        '</div>';
            }

            a +=
                    '</div>' +
                    '<div class="eh-clear"></div>' +
                    '</div>';
            $(a).prependTo(element).find(".eh-update-button").click(function () {
                t.options.initDate = $(this).attr("data-date");

                // refresh();
                getWeekDate(t.options.initDate);
                t.options.switchWeekCb();
            });
            element.find(".eh-thisweek-button").click(function () {
                t.options.initDate = $(this).attr("data-date");
                // refresh();
                getWeekDate(t.options.initDate);
                t.options.switchWeekCb();
                t.options.thisWeekCb();
            });
            if (b) {
                $('#J-select-box').find('.btn-group-left').addClass('active');
            } else {
                $('#J-select-box').find('.btn-group-right').addClass('active');
            }
        }

        function viewCalendarTable() {
            var a = '';
            a +=
                    '<div class="eh-view-container" style="">' +
                    '<div class="eh-view eh-agendaWeek-view eh-agenda-view eh-table-scroller" style="width: 100%;">' +
                    '<table>' +
                    viewCalendarHead() +
                    '<tbody>' +
                    '<tr>' +
                    '<td class="eh-widget-content">' +
                    // viewCalendarToday() +
                    '<div class="eh-time-grid-container" style=" border-radius: 12px;">' +
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
            var a = '', column = t.options.column;
            a += '<thead>' +
                    '<tr>' +
                    '<td class="eh-widget-header">' +
                    '<div class="eh-row eh-widget-header" style="">' +
                    '<table>' +
                    '<thead>' +
                    '<tr>' +
                    '<th class="eh-day-header eh-widget-header eh-appointment-header-date">日期</th>';
            for (var i = 0; i < column.departName.length; i++) {
                a += '<th column_id="' + column.departId[i] + '" class="eh-day-header eh-widget-header new-width">' + column.departName[i] + '</th>';
            }
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
            var a = '', column = t.options.column, b = t.options.selectOptionState;
            var o = options.datejson;
            if (b) {
                var monthline = options.datejson.currentWeek;
            } else {
                var monthline = options.datejson.currentMonth
            }
            a +=
                    '<div class="eh-time-grid">' +
                    '<div class="eh-appointment-set-detail">' +
                    '<table>' +
                    '<tbody>';
            for (var i = 0; i < monthline.length; i++) {
                a += '<tr>' +
                        '<td class="eh-axis-new eh-time eh-widget-content " >';

                if (monthline[i]['time'] < o.today) {
                    a += '<span class="font-color">';
                    a += monthline[i]['time'] + ' ' + monthline[i]['dayname'];
                } else {
                    a += '<span>';
                    a += monthline[i]['time'] + ' ' + monthline[i]['dayname'];
                }

                a += '</span>' +
                        '</td>';
                for (var j = 0; j < column.departName.length; j++) {
                    a += '<td data-date="' + monthline[i]['time'] + '" column_id="' + column.departId[j] + '" column_department="' + column.departName[j] + '" class="eh-widget-content eh-appointment-set-daily-content eh-appointment-set-daily-content-add">' +
                            '</td>';
                }
                a += '</tr>';
            }
            a +=
                    '</tbody>' +
                    '</table>' +
                    '</div>' +
                    '</div>';
            return a;
        }
        function refreshDate(json) {
            var o = options.datejson, a = '', column = options.column, a = '';
            for (var i = 0; i < column.departName.length; i++) {
                var target = ".eh-appointment-set-daily-content[column_id='" + column.departId[i] + "']";

                $(target).each(function () {
                    var date = $(this).attr("data-date"), column_id = $(this).attr("column_id"), data, data_date = [];
                    if (column_id == 'undefined') {
                        a = '<span class="eh-appointment-set-daily-add eh-appointment-set-daily-add-disable">' +
                                // '<i class="fa fa-plus"></i>' +
                                // '添加1' +
                                '</span>';
                    } else if (date < o.today || options.readOnly) {
                        a = '<span class="eh-appointment-set-daily-add eh-appointment-set-daily-add-disable">' +
                                // '<i class="fa fa-plus"></i>' +
                                // '添加1' +
                                '</span>';
                    } else {
                        a = '<span class="eh-mouseover eh-appointment-set-daily-add eh-appointment-set-daily-add-enable" style="display:none;">' +
                                '<i class="fa fa-plus"></i>' +
                                '添加' +
                                '</span>';
                    }
                    try {
                        //column.departId[i] ==column_id?data = json[i].daily_detail:"",
                        for (var k = 0; k < json.length; k++) {
                            if (column_id == json[k].id) {
                                data = json[k].daily_detail
                                var data_date = data[date];
                            }
                        }
                        if (data_date.length) {

                            a = '<div class="eh-appointment-set-daily-detail">';
                            a += '<span class="eh-appointment-set-daily-detail-cell"><span class="eh-appointment-set-daily-detail-time">' + data_date[0].time + '</span>(医生数量 <span class="eh-appointment-set-daily-detail-count">' + data_date[0].doctor_count + '</span>)</span>';
                            if(data_date.length != 1){
                                a += '<i class="eh-appointment-pencil" ></i>';
                            }

                            for (var j = 1; j < data_date.length; j++) {
                                a += '<br>';
                                a += '<span class="eh-appointment-set-daily-detail-cell"><span class="eh-appointment-set-daily-detail-time">' + data_date[j].time + '</span>(医生数量 <span class="eh-appointment-set-daily-detail-count">' + data_date[j].doctor_count + '</span>)</span>';
                                if(j != data_date.length-1){
                                    a += '<i class="eh-appointment-pencil" ></i>';
                                }
                            }

                            if (date < o.today || options.readOnly) {
                                a += '<i class=" eh-appointment-pencil disable"></i>';
                            } else {
                                a += '<i class="pencil eh-appointment-pencil eh-appointment-set-daily-add-enable" ></i>';
                            }
                            a += '</div>';
                        }


					}catch(x){}
                    $(this).html(a);

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
        function refreshCellData(data) {
            var a = '', data_date = data.daily_detail, thisCell = t.options.thisCell;
            thisCell.find(".eh-appointment-set-daily-detail").remove();
            if (data_date.length == 0) {
                a += '<span class="eh-appointment-set-daily-add eh-appointment-set-daily-add-enable" >' +
                        '<i class="fa fa-plus"></i>' +
                        '添加' +
                        '</span>';
            } else {
                a += '<div class="eh-appointment-set-daily-detail">';
                //第一行
                a += '<span class="eh-appointment-set-daily-detail-cell"><span class="eh-appointment-set-daily-detail-time">' + data_date[0].start_date + "-" + data_date[0].end_date + '</span>(医生数量 <span class="eh-appointment-set-daily-detail-count">' + data_date[0].doctor_count + '</span>)</span>';
                if(data_date.length != 1){
                    a += '<i class="eh-appointment-pencil" ></i>';
                }
                for (var i = 1; i < data_date.length; i++) {
                    a += '<br>';
                    a += '<span class="eh-appointment-set-daily-detail-cell"><span class="eh-appointment-set-daily-detail-time">' + data_date[i].start_date + "-" + data_date[i].end_date + '</span>(医生数量 <span class="eh-appointment-set-daily-detail-count">' + data_date[i].doctor_count + '</span>)</span>';
                    if(i != data_date.length-1){
                        a += '<i class="eh-appointment-pencil" ></i>';
                    }
                }
                a += '<i class="pencil eh-appointment-pencil eh-appointment-set-daily-add-enable" ></i>';
                a += '</div>';
            }
            thisCell.html(a);
            $(".eh-appointment-set-daily-add-enable").unbind("click").click(function () {
                var $target = $(this).parents(".eh-appointment-set-daily-content");
                t.options.thisCell = $target;
                options.tableCellCb($target);
            });
            var pencil = $('.pencil').parent();
            pencil.mouseover(function () {
                $(this).children('.pencil').addClass('eh-appointment-set-daily-detail-edit-img eh-appointment-set-daily-add-enable');
            });
            pencil.mouseout(function () {
                $(this).children('.pencil').removeClass('eh-appointment-set-daily-detail-edit-img eh-appointment-set-daily-add-enable');
            });
        }
        function formatDate(date, week) {
            var myyear = date.getFullYear();
            var mymonth = date.getMonth() + 1;
            var myweekday = date.getDate();

            if (mymonth < 10) {
                mymonth = "0" + mymonth;
            }
            if (myweekday < 10) {
                myweekday = "0" + myweekday;
            }
            var time = (myyear + "-" + mymonth + "-" + myweekday);
            if (week) {
                var dayNames = ["周日", "周一", "周二", "周三", "周四", "周五", "周六"];
                return {
                    "time": time,
                    "dayname": dayNames[date.getDay()]
                }
            }
            return time;
        }
        function calendarHeadFormatDate(string) {
            var date = string.split("-");
            return date[0] + '年' + date[1] + '月' + date[2] + '日';
        }
        //获得本周的开端日期
        function getWeekDate(date) {
            t.options.datejson = initWeekDate(date);
        }
        function initWeekDate(date) {

            var now;
            date ? now = new Date(date) : now = new Date(nowYearMonthDate);
            var nowDayOfWeek = now.getDay() - 1, //今天本周的第几天,按星期一为第一
                    nowDate = new Date(nowYearMonthDate),
                    week = now.getDay(),
                    nowWeek = nowDate.getDay(),
                    millisecond = 1000 * 60 * 60 * 24,
                    minusDay = week != 0 ? week - 1 : 6,
                    minusNowDay = nowWeek != 0 ? nowWeek - 1 : 6,
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
            thisWeekStartDayStamp = new Date((now.getTime()) - (minusDay * millisecond));
            thisWeekStartDay = formatDate(thisWeekStartDayStamp);
            thisWeekEndDay = formatDate(new Date((thisWeekStartDayStamp.getTime()) + (6 * millisecond)));
            currentWeekStartDayStamp = new Date((nowDate.getTime()) - (minusNowDay * millisecond));
            currentWeekStartDay = formatDate(currentWeekStartDayStamp);
            currentWeekEndDay = formatDate(new Date((currentWeekStartDayStamp.getTime()) + (6 * millisecond)));
            currentMonthStartDay = formatDate(new Date(nowDate.getFullYear(), nowDate.getMonth(), 1));
            currentMonthEndDay = formatDate(new Date(nowDate.getFullYear(), nowDate.getMonth() + 1, 0));
            currentWeek = [], currentMonth = [];
            for (var i = 0; i < 7; i++) {
                currentWeek.push(formatDate(new Date(nowYear, nowMonth, nowDay - nowDayOfWeek + i), true));
            }
            for (var i = 1; i <= thisMonthEndDay.split("-")[2]; i++) {
                currentMonth.push(formatDate(new Date(nowYear, nowMonth, i), true));
            }
            datejson = {
                "weekStartDate": weekStartDate,
                "weekEndDate": weekEndDate,
                "thisMonthStartDay": thisMonthStartDay,
                "thisMonthEndDay": thisMonthEndDay,
                "thisWeekStartDay": thisWeekStartDay,
                "thisWeekEndDay": thisWeekEndDay,
                "prevMonthStartDay": prevMonthStartDay,
                "nextMonthStartDay": nextMonthStartDay,
                "prevWeekStartDate": prevWeekStartDate,
                "nextWeekStartDate": nextWeekStartDate,
                "today": formatDate(nowDate),
                "currentMonthStartDay": currentMonthStartDay,
                "currentMonthEndDay": currentMonthEndDay,
                "currentWeekStartDay": currentWeekStartDay,
                "currentWeekEndDay": currentWeekEndDay,
                "currentWeek": currentWeek,
                "currentMonth": currentMonth
            }
            return datejson;
        }
    }
    // });  
})(jQuery, window);

