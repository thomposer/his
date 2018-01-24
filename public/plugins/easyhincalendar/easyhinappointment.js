(function ($, window) {
    //初始化日历值
    var defaults = {
        column: [
            {
                "name": "列一",
                "data-source": "1",
                "appoint-num": ''
            },
            {
                "name": "列二",
                "data-source": "2",
                "appoint-num": ''
            },
        ],
        row: [
            {
                "name": "行一",
                "data-source": "1",
                "appoint-num": ''
            },
            {
                "name": "行二",
                "data-source": "2",
                "appoint-num": ''
            },
        ],
        contentHeight: 700,
        readOnly: false,
        timeConfig: false,
        copyWeek: false,
        addPlus: false,
        header: {
            backBtnUrl: "javascript:void(0)"
        },
        initDate: "",
        copyWeekConfig: "",
        switchWeekCb: function () {
        },
        thisWeekCb: function () {
        },
        tableCellCb: function (x) {
        }
    };
    $.fn.easyhinGrid = function (options) {
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

        t.updateDate = function (date) {
            updateDate(date);
        }
        t.render = function () {
            initialRender();
        }
        t.refresh = function () {
            refresh();
        }
        t.refreshGrid = function (json, callback) {
            var options = $.extend({}, defaults, json);
            t.options = options;
            refresh();
            refreshData(json, callback);
        }
        t.refreshData = function (json, callback) {
            refresh();
            refreshData(json, callback);
        }
        t.refreshCellData = function (json, callback) {
            refreshData(json, callback);
        }
        t.refreshThisCellData = function (data) {
            refreshThisCellData(data);
        }
        t.refreshDate = function (json) {
            refresh();
            refreshDate(json);
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
            element.addClass("eh eh-unthemed eh-outcontent no-radius");
            getWeekDate(t.options.initDate);
            viewCalendarTable();
            viewToolBar();
        }

        function viewToolBar() {
            var a = '', o = t.options.datejson, b = t.options.dataDepartment, c = t.options.selectName, nowdate = initWeekDate(formatDate(new Date(nowYearMonthDate)));
            var title = t.options.tableTitle;
            var re = new RegExp("-", "g");//正则替换时间格式
            var selectStartTime = t.options.datejson.currentWeek[0].replace(re, '.');
            var selectEndTime = t.options.datejson.currentWeek[6].replace(re, '.');

            if (c) {
                var placeholder = "请输入员工姓名";
            } else {
                var placeholder = "请输入医生姓名";
            }
            (t.options.headChange) ? buttonClass = 'eh-left' : buttonClass = 'eh-right';
            (t.options.headChange) ? dateClass = 'eh-center' : dateClass = 'eh-left';

            title ? title : title = '排班';
            a +=
                '<div class="eh-toolbar no-bottom">' +
                '<div class="' + buttonClass + '">' +
                '<div class="eh-button-group">' +
                '<button data-date="' + o.prevWeekStartDate + '" class="eh-prev-button eh-button  eh-update-button" type="button">' +
                '<i class="fa fa-angle-left"></i>'+
                '</button>';
            a +=
                '<div class="appointment-select-time-range">' +
                '<div class="form-group canlender-border">' +
                '<input type="text" readonly="true" class="empty-time form-control appointment-select-time-range-timepicker" value="" placeholder="' + selectStartTime + '-' + selectEndTime + '">' +
                '</div>' +
                '</div>';
            a += '<button data-date="' + o.nextWeekStartDate + '" class="eh-next-button eh-button  eh-update-button" type="button">' +
            '<i class="fa fa-angle-right"></i>'+
            '</button>' +
            '</div>';
            if (o.weekStartDate == nowdate.weekStartDate) {
                a += '<button class="eh-thisweek-button  eh-button eh-state-default eh-corner-left eh-corner-right " type="button" disabled="disabled">本周</button>';
            } else {
                a += '<button data-date="' + o.today + '" class="eh-thisweek-button  eh-button eh-state-default eh-corner-left eh-corner-right" type="button">本周</button>';
            }

            a +=
                '</div>' +
                '<div class="' + dateClass + '">' +
                // '<h2>' + title + calendarHeadFormatDate(o.currentWeek[0]) + '—' + calendarHeadFormatDate(o.currentWeek[6]) + '</h2>' +
                '</div>';
            //右侧搜索栏

            a += '<button  class=" btn-default close-appointment"  role="modal-remote" data-modal-size = "large" href="' + closeAppointmentSwitch + '" >关闭预约</button>';
            a += '<div class="back-history btn-group pull-right " id="J-select-box">';
            a += '<a href="'+appointmentDetail+'"  class="btn btn-group-left active">';
            a += '<span data-pjax="0">';
            a += '按医生展示' ;
            a += '</span>';
            a += '</a>';
            a += '<a href="'+appointmentIndex+'" class="btn btn-group-right ">';
            a += '<span data-pjax="0">';
            a += '按时间段展示' ;
            a += '</span>';
            a += '</a>';
            a += '</div>';

            try {
                if (t.options.header.backBtnUrl != defaults.header.backBtnUrl) {
                    a +=
                        '<div class="eh-right">' +
                        '<span class="eh-toolbar-back-btn">' +
                        '<a href="' + t.options.header.backBtnUrl + '">< 返回</a>' +
                        '</span>' +
                        '</div>';
                }
            } catch (x) {

            }

            a += '<div class="eh-clear"></div>';
            a += '</div>';

            $(a).prependTo(element).find(".eh-update-button").click(function () {
                t.options.initDate = $(this).attr("data-date");
                getWeekDate(t.options.initDate);
                t.options.switchWeekCb();
            });
            element.find(".eh-thisweek-button").click(function () {
                t.options.initDate = $(this).attr("data-date");
                getWeekDate(t.options.initDate);
                t.options.switchWeekCb();
                t.options.thisWeekCb();
            });
            //时间选择器
            $('.appointment-time-range-timepicker').remove();
            $('.appointment-select-time-range-timepicker').datetimepicker({
                language: 'zh-CN',
                format: 'yyyy-mm-dd',
                minView: 2,
                initialDate: selectStartTime,
                autoclose: true,
                weekStart: 1,
                forceParse:false,
                pickerPosition: 'bottom-right appointment-time-range-timepicker',
            });
            $(document).off('mousedown');
            $('.appointment-select-time-range-timepicker').on('mousedown', function (e) {    // Clicked outside the datetimepicker, hide it
                if($('.appointment-time-range-timepicker').attr('style').indexOf('display: block') == (-1)){
                    $('.appointment-time-range-timepicker').show();
                }else {
                    setTimeout(function () {
                        $('.appointment-time-range-timepicker').hide();
                    },200);
                }
            });
            $('.appointment-select-time-range-timepicker').bind('change', function () {
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

        function viewCalendarTable() {
            var a = '';
            a +=
                '<div class="eh-view-container" style="">' +
                '<div class="eh-view eh-agendaWeek-view eh-agenda-view table-responsive" >' +
                '<table class="table-overflow">' +
                viewCalendarColumnHead() +
                '<tbody>' +
                '<tr>' +
                '<td class="eh-widget-content">' +
                    // viewCalendarToday() +
                '<div class="eh-grid-container eh-scroller">' +
                viewCalendarRow() +
                '</div>' +
                '</td>' +
                '</tr>' +
                '</tbody>' +
                '</table>' +
                '</div>' +
                '</div>';
            $(a).prependTo(element);
            thisCellEvent(t.options);
            refreshData();
        }

        function viewCalendarColumnHead() {
            var a = '', isToday = '', highlight = '', week = options.datejson.currentWeek, column = t.options.column, currentTime = nowYearMonthDate;

            a += '<thead>' +
                '<tr>' +
                '<td class="eh-widget-header">' +
                '<div class="eh-row eh-widget-header">' +
                '<table>' +
                '<thead>' +
                '<tr>';
            for (var i = 0; i < column.length; i++) {
                //判断是否为今天
                isToday = (currentTime == column[i]['data-source']) ? 'eh-today-highlight-tip eh-schedule-nowDate' : '';
                highlight = (currentTime == column[i]['data-source']) ? 'highlight' : 'normal';

                //判断是否为第一列
                a += '<th data-source="' + column[i]['data-source'] + '" class="eh-day-header eh-widget-header ' + isToday + '">';
                a += '<div class="eh-title-' + highlight + '"></div>';
                a += '<div class="data-name">' + htmlEncodeByRegExp(column[i]['name']) + '</div>';
                a += '<div class="appoint-num">' + (column[i]['appoint-num']) + '</div>';
                // if (i != 0) {
                //     a += '<div class="data-name">' + htmlEncodeByRegExp(column[i]['name']) + '</div>';
                //     a += '<div class="appoint-num">' + (column[i]['appoint-num']) + '</div>';
                // } else {
                //     a += '<div class="data-name">' + htmlEncodeByRegExp(column[i]['name']) + '</div>';
                //     a += '<div class="appoint-num">' + (column[i]['appoint-num']) + '</div>';
                //     // a += '<div>' + htmlEncodeByRegExp(column[i]['name']) + '</div>';
                // }
                a += '<div class="eh-title-normal"></div>';
                a += '</th>';
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

        /**
         *
         * @param AddDayCount
         * AddDayCount->(-2)->前天
         * AddDayCount->(-1)->昨天
         * AddDayCount->(0)->今天
         * AddDayCount->(1)->明天
         * AddDayCount->(2)->后天
         * AddDayCount->(3)->大后天
         * @returns {string}
         * @constructor
         */
        function GetDateStr(AddDayCount) {
            var dd = new Date(nowYearMonthDate);
            dd.setDate(dd.getDate() + AddDayCount);//获取AddDayCount天后的日期
            var y = dd.getFullYear();
            var m = dd.getMonth() + 1;//获取当前月份的日期
            var d = dd.getDate();

            if (m < 10) {
                var new_m = '0' + m;
            } else {
                var new_m = m;
            }

            if (d < 10) {
                var new_d = '0' + d;
            } else {
                var new_d = d;
            }
            return y + "-" + new_m + "-" + new_d;
        }

        function viewCalendarRow() {
            var a = '', row = t.options.row, column = t.options.column,currentTime = nowYearMonthDate,currentYesterTime = GetDateStr(-1);
            a +=
                '<div class="eh-grid">' +
                '<div class="eh-appointment-set-detail">' +
                '<table>' +
                '<tbody>';
            for (var i = 0; i < row.length; i++) {
                a += '<tr>' +
                    '<td data-column-source="' + column[0]['data-source'] + '" data-row-source="' + row[i]['data-source'] + '" class="eh-axis eh-widget-content" >' +
                    '<div class="doctor-name">' +
                        htmlEncodeByRegExp(row[i]['name']) +
                    '</div>' +
                    '<div class="appoint-num doctor-detail">' +
                        row[i]['content'] +
                    '</div>' +
                    '</td>';
                for (var j = 1; j < column.length; j++) {
                    var editable = '';
                    (!t.options.readOnly) && (!column[j]['readonly']) ? editable = 'eh-editable-td' : '';
                    /**
                     * author:JeanneWu
                     * time:2017-02-22
                     *
                     * eh-today-highlight-tip 用于标识高亮蓝色边框
                     *
                     * case 1
                     * 作用：判断是否为今日
                     * currentTime == column[j]['data-source']
                     * 示例：
                     * currentTime（今日日期）格式为：2017-02-22
                     * column[j]['data-source'] 为表格上attr的属性  格式为：2017-02-20 或者 occupation（属于表头属性）
                     *
                     * case 2
                     * 作用：判断是否为昨天
                     * currentYesterTime == column[j]['data-source']
                     * 示例：
                     * currentYesterTime（昨天日期） 格式为：2017-02-21
                     * column[j]['data-source'] 为表格上attr的属性  格式为：2017-02-20 或者 occupation（属于表头属性）
                     *
                     * case 3
                     * 作用：昨天之前的情况 不含昨天
                     *
                     */

                    if (currentTime == column[j]['data-source']) {
                        a += '<td data-column-source="' + column[j]['data-source'] + '" data-row-source="' + row[i]['data-source'] + '"  class="eh-today-highlight eh-widget-content eh-appointment-set-daily-content eh-future ' + editable + ' eh-today-highlight-tip">' +

                            '</td>';
                    } else if (currentYesterTime == column[j]['data-source']) {
                        a += '<td data-column-source="' + column[j]['data-source'] + '" data-row-source="' + row[i]['data-source'] + '"  class="eh-widget-content eh-appointment-set-daily-content ' + editable + '">' +

                            '</td>';
                    } else if (currentTime > column[j]['data-source']) {
                        a += '<td data-column-source="' + column[j]['data-source'] + '" data-row-source="' + row[i]['data-source'] + '"  class="eh-widget-content eh-appointment-set-daily-content ' + editable + '">' +

                            '</td>';
                    } else {
                        var className = (column[j]['data-source'] != 'occupation') ? 'eh-future ' : '';
                        a += '<td data-column-source="' + column[j]['data-source'] + '" data-row-source="' + row[i]['data-source'] + '"  class="eh-widget-content eh-appointment-set-daily-content  ' + className + editable + '">' +

                            '</td>';
                    }
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
            var o = options.datejson, a = '', column = t.options.column, a = '';
            for (var i = 0; i < column.length; i++) {
                var target = ".eh-appointment-set-daily-content[data-column-source='" + column[i]['data-source'] + "']";
                $(target).each(function () {
                    var date = $(this).attr("data-column-source"), column_id = $(this).attr("data-row-source"), data, data_date = [];
                    if (column_id == 'undefined') {
                        a = '<span class="eh-appointment-set-daily-add eh-appointment-set-daily-add-disable">' +
                            '</span>';
                    } else if (date < o.today || options.readOnly) {
                        a = '<span class="eh-appointment-set-daily-add eh-appointment-set-daily-add-disable">' +
                            '</span>';
                    } else {
                        a = '<span class="eh-mouseover eh-appointment-set-daily-add eh-appointment-set-daily-add-enable" style="display:none;">' +
                            '<i class="fa fa-plus"></i>' +
                            '添加' +
                            '</span>';
                    }
                    try {
                        for (var k = 0; k < json.length; k++) {
                            if (column_id == json[k]['data-row-source'] && date == json[k]['data-column-source']) {
                                data_date = json[k]['content']
                            }
                        }
                        if (data_date.length) {
                            a = '<div class="eh-appointment-set-daily-detail"><br>';
                            for (var j = 0; j < data_date.length; j++) {
                                a += '<span class="eh-appointment-set-daily-detail-cell"><span class="eh-appointment-set-daily-detail-time">' + data_date[j] + '</span></span><br>';
                            }
                            if (date < o.today || options.readOnly) {
                                a += '<i class=" eh-appointment-pencil disable"></i>';
                            } else {
                                a += '<i class="pencil eh-appointment-pencil eh-appointment-set-daily-add-enable" ></i>';
                            }
                            a += '</div>';
                        }


                    } catch (x) {
                    }
                    $(this).html(a);

                })

            }
            thisCellEvent(t.options);
        }

        function refreshData(json, callback) {
            var callback = callback || function () {
                    },
                json = json || t.options.datajson,
                redCss = t.options.redCss,
                target = [];

            for (var i = 0; i < json.length; i++) {
                  var a = '', tdData = json[i],statusContent = '' ;
                  var  contentNum = tdData['content'];
                    $target = $(".eh-grid td[data-column-source='" + tdData['data-column-source'] + "'][data-row-source='" + tdData['data-row-source'] + "']");
                    if(tdData.appointmentStatus == 2 && tdData.usedAppointmentCount==0){
                         statusContent = '— —';
                    }else{
                         statusContent = (tdData.status == 1) ? contentNum : '— —' ;
                    }
                    statusClass = (tdData.status == 1 && tdData.appointmentStatus !=2) ? 'eh-appointment-appoint-detail-time' : 'grey';
                    a += '<span class="'+statusClass+'">' + statusContent + '</span>';

                $target.html(a).attr("data-source", tdData['data-source']);
                $target.length != 0 ? target.push($target) : '';
            }

            callback(target);
        }


        function refreshThisCellData(data) {
            var a = '', data_date = data.dailyDetail, thisCell = t.options.thisCell;
            thisCell.find(".eh-appointment-set-daily-detail").remove();
            if (data_date.length == 0) {
                a += '<span class="eh-appointment-set-daily-add eh-appointment-set-daily-add-enable" >' +
                    '<i class="fa fa-plus"></i>' +
                    '添加' +
                    '</span>';
            } else {
                a += '<div class="eh-appointment-set-daily-detail"><br>';
                for (var i = 0; i < data_date.length; i++) {
                    a += '<span class="eh-appointment-set-daily-detail-cell"><span class="eh-appointment-set-daily-detail-time">' + data_date[i].start_date + "-" + data_date[i].end_date + '</span></span><br>';

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

        function thisCellEvent(options) {
            if (options.timeConfig) {
                $(".eh-appointment-set-daily-add-enable").unbind("click").click(function () {
                    var $target = $(this).parents(".eh-appointment-set-daily-content");
                    options.thisCell = $target;
                    options.tableCellCb($target);
                });
            } else {
                $(".eh-grid .eh-future").unbind("click").click(function () {
                    var $target = $(this);
                    options.thisCell = $target;
                    options.tableCellCb($target);
                });
            }
        }
    }
})(jQuery, window);

