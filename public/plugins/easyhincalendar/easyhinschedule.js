(function ($, window) {
    //初始化日历值
    var defaults = {
        column: [
            {
                "name": "列一",
                "data-source": "1"
            },
            {
                "name": "列二",
                "data-source": "2"
            },
        ],
        row: [
            {
                "name": "行一",
                "data-source": "1"
            },
            {
                "name": "行二",
                "data-source": "2"
            },
        ],
        contentHeight: 700,
        readOnly: false,
        timeConfig: false,
        copyWeek:false,
        addPlus:false,
        header: {
            backBtnUrl: "javascript:void(0)"
        },
        initDate: "",
        copyWeekConfig: "",

        switchWeekCb: function () {},
        thisWeekCb: function () {},
        tableCellCb: function (x) {}
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
        t.refreshThisCellData=function (data) {
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
            element.addClass("eh eh-unthemed eh-outcontent");
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
            (t.options.headPosition) ? buttonClass = 'eh-right' : buttonClass = 'eh-left';
            (t.options.headChange) ? dateClass = 'eh-center' : dateClass = 'eh-left';

            title ? title : title = '排班';
            a += '<div class="eh-toolbar no-bottom">' ;
            if(entrance !=1){
                a +=
                    '<div class="' + buttonClass + '">' +
                    '<div class="eh-button-group">' +
                    '<button data-date="' + o.prevWeekStartDate + '" class="eh-prev-button eh-button  eh-update-button" type="button">' +
                        // '<span class="eh-icon eh-icon-left-single-arrow"></span>' +
                    // '<img class="" alt="" src="' + baseUrl + '/public/img/common/icon_last_page.png">' +
                    '<i class="fa fa-angle-left"></i>'+
                    '</button>';


                a += '<div class="appointment-select-time-range">' +
                    '<div class="form-group canlender-border">' +
                    '<input readonly="true" type="text" class="form-control empty-time appointment-select-time-range-timepicker" value="" placeholder="' + selectStartTime + '-' + selectEndTime + '">' +
                    '</div>' +
                    '</div>';
                a +=
                    '<button data-date="' + o.nextWeekStartDate + '" class="eh-next-button eh-button  eh-update-button" type="button">' +
                        // '<span class="eh-icon eh-icon-right-single-arrow"></span>' +
                    // '<img class="" alt="" src="' + baseUrl + '/public/img/common/icon_next_page.png">' +
                    '<i class="fa fa-angle-right"></i>'+
                    '</button>' +
                    '</div>';
                if (o.weekStartDate == nowdate.weekStartDate) {
                    a += '<button class="eh-thisweek-button  eh-button eh-state-default eh-corner-left eh-corner-right " type="button" disabled="disabled">本周</button>';
                } else {
                    a += '<button data-date="' + o.today + '" class="eh-thisweek-button  eh-button eh-state-default eh-corner-left eh-corner-right" type="button">本周</button>';
                }
                //职位搜索框
                    a +='</div>';
                    a += '<div class="' + dateClass + '">' ;
                    // a += '<h2>' + title + calendarHeadFormatDate(o.currentWeek[0]) + '—' + calendarHeadFormatDate(o.currentWeek[6]) + '</h2>' ;
                    a += '</div>';

            }
            //右侧搜索栏

            if (t.options.headChange) {
                if(!t.options.copyWeek) {

                    a +=
                            '<div id = "eh-right" class="eh-right eh-top">' +
                            '<span class="search-default">筛选：</span>' +
                            '<div class="form-group field-appointmentsearch-username">' +
                            '<input type="text" placeholder="' + placeholder + '" name="AppointmentSearch[username]" class="form-control" id="appointmentsearch-username" >' +
                            '</div>' +
                            '<div class="form-group field-appointmentsearch-second_department_id">' +
                            '<select name="AppointmentSearch[second_department_id]" class="form-control" id="appointmentsearch" style="width: 150px">' +
                            '<option value="">请选择科室</option>';
                    if (b) {
                        $.each(b, function (key, val) {
                            a += '<option value="' + b[key].department_id + '">' + htmlEncodeByRegExp(b[key].department_name) + '</option>';
                        });
                    }


                    a += '</select>' +
                            '</div>' +
                            '<button id="search" type="button" class="btn btn-default">搜索</button>' +
                            '</div>';

                } else {
                    var data_date = o.prevWeekStartDate;
                    a +=
                            '<div id = "eh-right" class="eh-right eh-top">' +
                            '<div  class="btn-group">' +
                            '<span class="btn btn-group-left active copy-last-week">' +
                            '<a data-confirm-message="此操作会覆盖当前设置，请谨慎操作！" data-delete="" data-confirm-title="系统提示" data-toggle="tooltip" role="modal-remote" data-request-method="post" data-pjax="1" aria-label="复制上周" title=""  data-original-title="复制上周" data-url="' + t.options.copyWeekConfig + '?prevWeekStartDate=' + data_date + '" data-request-method="post">' +
                            '<i class=" commonSearchStatus btn-background">' +
                            '</i>' +
                            '复制上周' +
                            '</a>' +
                            '</span>' +
                            '</div>'+

                            '<div  class="btn-group">' +
                            '<span class="btn btn-group-left active copy-last-week">' +
                            '<a href="'+ t.options.appointmentTimeTemplateUrl +'">' +
                            '<i class=" commonSearchStatus btn-background">' +
                            '</i>' +
                            '模板管理' +
                            '</a>' +
                            '</span>' +
                            '</div>'+

                            '</div>';
                }

            }

            try {
                if (t.options.header.backBtnUrl != defaults.header.backBtnUrl)
                {
                    a +=
                            '<div class="eh-right">' +
                            '<span class="eh-toolbar-back-btn">' +
                            '<a href="' + t.options.header.backBtnUrl + '">< 返回</a>' +
                            '</span>' +
                            '</div>';
                }
            } catch (x) {
            }
            if(entrance == 1){
                a += '<div class="eh-left">' ;
                a += '<h2>' + title + calendarHeadFormatDate(o.currentWeek[0]) + '—' + calendarHeadFormatDate(o.currentWeek[6]) + '</h2>' ;
                a += '</div>';
            }
            a += '<div class="eh-clear"></div>';
            a += '</div>';
            if(entrance ==1){
                a +=
                    '<div class="eh-toolbar no-bottom">' +
                    '<div class="eh-left">' +
                    '<div class="eh-button-group">' +
                    '<button data-date="' + o.prevWeekStartDate + '" class="eh-prev-button eh-button  eh-update-button" type="button">' +
                        // '<span class="eh-icon eh-icon-left-single-arrow"></span>' +
                    // '<img class="" alt="" src="' + baseUrl + '/public/img/common/icon_last_page.png">' +
                    '<i class="fa fa-angle-left"></i>'+
                    '</button>';
                a += '<div class="appointment-select-time-range">' +
                    '<div class="form-group canlender-border">' +
                    '<input readonly="true" type="text" class="empty-time form-control appointment-select-time-range-timepicker" value="" placeholder="' + selectStartTime + '-' + selectEndTime + '">' +
                    '</div>' +
                    '</div>';
                a +=
                    '<button data-date="' + o.nextWeekStartDate + '" class="eh-next-button eh-button  eh-update-button" type="button">' +
                        // '<span class="eh-icon eh-icon-right-single-arrow"></span>' +
                    // '<img class="" alt="" src="' + baseUrl + '/public/img/common/icon_next_page.png">' +
                    '<i class="fa fa-angle-right"></i>'+
                    '</button>' +
                    '</div>';
                if (o.weekStartDate == nowdate.weekStartDate) {
                    a += '<button class="eh-thisweek-button  eh-button eh-state-default eh-corner-left eh-corner-right " type="button" disabled="disabled">本周</button>';
                } else {
                    a += '<button data-date="' + o.today + '" class="eh-thisweek-button  eh-button eh-state-default eh-corner-left eh-corner-right" type="button">本周</button>';
                }
                if(entrance ==1){

                    a += '</div>';

                }
                //职位搜索框
                if(entrance == 1){
                    a +='<div class="eh-right schedule-search-content">';
                    a +=     '<form id="w0" class="form-horizontal search-form" action="#" method="get" data-pjax="">    <span class="search-default">筛选：</span>';
                    a +=     '<div class="form-group field-schedulingsearch-occupation">'
                    a += '<select id="schedulingsearch-occupation" class="form-control doctor-width" name="SchedulingSearch[occupation]">';
                    a += '<option value="">请选择职位</option>';
                    for(var key in occupationList){
                        a += '<option value="'+key+'">'+occupationList[key]+'</option>';
                    }
                    a += '</select>';
                    a += '</div>';
                    a +=     '<div class="form-group search_button">';
                    a +=         '<button type="button" class="btn btn-default">搜索</button>';
                    a +=     '</div>';
                    a +=     '</form>';
                    a += '</div>';
                    a +='</div>';

                    a += '</div>';

                    a += '<div class="eh-clear"></div>';
                    a += '</div>';
                }
            }

            $(a).prependTo(element).find(".eh-update-button").click(function () {
                t.options.initDate = $(this).attr("data-date");
                getWeekDate(t.options.initDate);
                t.options.switchWeekCb();
            });

            ////回写职位
            $('#schedulingsearch-occupation').val(t.options.occupation);


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
                pickerPosition: 'bottom-right appointment-time-range-timepicker'
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
            if(t.options.addPlus){
                var parentDiv = $('.insertSelected');
                parentDiv.mouseover(function () {
                    var value = $(this).attr('data-source');
                    if(!value){
                        value = 0;
                    }
                    var b = viewScheduleSelect(value);
                    var columnSource = $(this).attr('data-column-source');
                    if(columnSource == 'occupation'){
                        return;
                    }
                    if($(this).children().hasClass('eh-mouseover') == false){
                        $(this).append(b);
                    }
                    $(this).children('.eh-mouseover').show();
                    $(this).children('.reception-span').hide();
                });
                parentDiv.mouseout(function () {
                    $(this).children('.eh-mouseover').hide();
                    $(this).children('.reception-span').show();
                });
            }



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
            var a = '', week = options.datejson.currentWeek, column = t.options.column;
            //var currentTime = GetDateStr(0);
            var currentTime = nowYearMonthDate;
            var currentYesterTime = GetDateStr(-1);

            a += '<thead>' +
                    '<tr>' +
                    '<td class="eh-widget-header">' +
                    '<div class="eh-row eh-widget-header">' +
                    '<table>' +
                    '<thead>' +
                    '<tr>';
            for (var i = 0; i < column.length; i++) {
                if (currentTime == column[i]['data-source']) {
                    a += '<th data-source="' + column[i]['data-source'] + '" class="eh-day-header eh-widget-header eh-today-highlight-tip eh-schedule-nowDate"><div class="eh-title-highlight"></div>' + htmlEncodeByRegExp(column[i]['name']) + '<div class="eh-title-normal"></div></th>';
                }else if(currentYesterTime == column[i]['data-source']){
                    a += '<th data-source="' + column[i]['data-source'] + '" class="eh-day-header eh-widget-header "><div class="eh-title-normal"></div>' + htmlEncodeByRegExp(column[i]['name']) + '<div class="eh-title-normal"></div></th>';
                }else {
                    a += '<th data-source="' + column[i]['data-source'] + '" class="eh-day-header eh-widget-header"><div class="eh-title-normal"></div>' + htmlEncodeByRegExp(column[i]['name']) + '<div class="eh-title-normal"></div></th>';
                }
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
            dd.setDate(dd.getDate()+AddDayCount);//获取AddDayCount天后的日期
            var y = dd.getFullYear();
            var m = dd.getMonth()+1;//获取当前月份的日期
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
            return y+"-"+new_m+"-"+new_d;
        }
        function viewCalendarRow() {
            var a = '', row = t.options.row, column = t.options.column;
            var selectTmp=t.options.selectTmp;
            //var currentTime = GetDateStr(0);
            var currentTime = nowYearMonthDate;
            var currentYesterTime = GetDateStr(-1);
            var b = viewScheduleSelect();
            a +=
                    '<div class="eh-grid">' +
                    '<div class="eh-appointment-set-detail">' +
                    '<table>' +
                    '<tbody>';
            for (var i = 0; i < row.length; i++) {
                a += '<tr>' +
                        '<td data-column-source="' + column[0]['data-source'] + '" data-row-source="' + row[i]['data-source'] + '" class="eh-axis eh-widget-content" >' +
                        '<span>' +
                        htmlEncodeByRegExp(row[i]['name']) +
                        '</span>' +
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
                        a += '<td data-column-source="' + column[j]['data-source'] + '" data-row-source="' + row[i]['data-source'] + '" data-doctor-type=\'' + row[i]['data-doctor-type'] + '\' class="eh-today-highlight eh-widget-content eh-appointment-set-daily-content eh-future ' + editable + ' eh-today-highlight-tip insertSelected">';

                        a +=   '</td>';
                    }else if(currentYesterTime == column[j]['data-source']){
                        a += '<td data-column-source="' + column[j]['data-source'] + '" data-row-source="' + row[i]['data-source'] + '" data-doctor-type=\'' + row[i]['data-doctor-type'] + '\' class="eh-widget-content eh-appointment-set-daily-content ' + editable + '">' +
                            '<span class="eh-appointment-set-daily-add eh-appointment-set-daily-add-disable"></span>'+
                            '</td>';
                    } else if(currentTime > column[j]['data-source']){
                        a += '<td data-column-source="' + column[j]['data-source'] + '" data-row-source="' + row[i]['data-source'] + '" data-doctor-type=\'' + row[i]['data-doctor-type'] + '\' class="eh-widget-content eh-appointment-set-daily-content ' + editable + '">' +
                            '<span class="eh-appointment-set-daily-add eh-appointment-set-daily-add-disable"></span>'+
                            '</td>';
                    }else {
                        var className = (column[j]['data-source'] != 'occupation') ? 'eh-future ': '';
                        a += '<td data-column-source="' + column[j]['data-source'] + '" data-row-source="' + row[i]['data-source'] + '" data-doctor-type=\'' + row[i]['data-doctor-type'] + '\' class="insertSelected eh-widget-content eh-appointment-set-daily-content  ' + className + editable + '">';

                        a +=   '</td>';
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
       function  viewScheduleSelect(value){
            var a ='';
            a +=   '<span class="eh-mouseover eh-appointment-set-daily-add eh-appointment-set-daily-add-enable" style="display: none;">';
            a +=   '<div class="form-horizontal">';
            a +=        '<div class="form-group field-scheduleset has-success">';
            a +=    '\t\t<select class="scheduleset form-control">';
            a +=    '\t\t<option value="">请选择</option>';
            if(scheduleOpt.length>0){
                for(var k=0;k<scheduleOpt.length;k++){
                    var selected = '';
                    if(value == scheduleOpt[k].id){
                        selected = 'selected';
                    }
                    a +=    '<option value="'+scheduleOpt[k].id+'"'+ selected +'>'+htmlEncodeByRegExp(scheduleOpt[k].shift_name)+'</option>';
                }
            }
            a +=    '</select>';
            a +=     '</div>   ';
            a +=   '</div>';
            a +=  '</span>';
            return a;
        };

        function refreshDate(json) {
            var o = options.datejson, a = '', column = t.options.column, a = '';
            for (var i = 0; i < column.length; i++) {
                var target = ".eh-appointment-set-daily-content[data-column-source='" + column[i]['data-source'] + "']";
                $(target).each(function () {
                    $(this).attr('data-select-type',"\"\"");
                    var date = $(this).attr("data-column-source"), column_id = $(this).attr("data-row-source"), data, data_date = [];
                    if (column_id == 'undefined') {
                        a = '<span class="eh-appointment-set-daily-add eh-appointment-set-daily-add-disable">' +
                            '</span>';
                    } else if (date < o.today || options.readOnly) {
                        a = '<span class="eh-appointment-set-daily-add eh-appointment-set-daily-add-disable">' +
                            '</span>';
                    } else {
                         a = makeUseTemplateHtml(this,false);
                    }
                    var tipContent = "";
                    try {

                        //column.departId[i] ==column_id?data = json[i].daily_detail:"",
                        for (var k = 0; k < json.length; k++) {
                            if (column_id == json[k]['data-row-source'] && date == json[k]['data-column-source']) {
                                data_date = json[k]['content'];
                                $(this).attr('data-select-type',JSON.stringify(json[k]['content']));
                            }
                        }
                        if (data_date.length) {

                            a = '<div class="eh-appointment-set-daily-detail" data-toggle="tooltip" data-html="true" data-placement="bottom" data-original-title = ""><br>';
                            for (var j = 0; j < data_date.length; j++) {
                                if(data_date[j].typeNameList){
                                    tipContent += data_date[j].shift_name +"  " + htmlEncodeByRegExp(data_date[j].typeNameList) + '<br/>';
                                    a += '<span class="eh-appointment-set-daily-detail-cell"><span class="eh-appointment-set-daily-detail-time">' + data_date[j].shift_name + '<br/><span class="type-name-list">（' + htmlEncodeByRegExp(data_date[j].typeNameList) + '）</span></span></span>';
                                }else{
                                    tipContent += data_date[j].shift_name + '<br/>';
                                    a += '<span class="eh-appointment-set-daily-detail-cell"><span class="eh-appointment-set-daily-detail-time">' + data_date[j].shift_name + '</span></span><br>';
                                }
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
                    if(tipContent.length>0){
                        $(this).find('.eh-appointment-set-daily-detail').attr('data-original-title',tipContent);
                    }


                })

            }
            thisCellEvent(t.options);
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
        function refreshData(json, callback) {
            var callback = callback || function () {},
                    json = json || t.options.datajson,
                    redCss = t.options.redCss,
                    target = [];
            for (var i = 0; i < json.length; i++) {
                var tdData = json[i],
                        $target = $(".eh-grid td[data-column-source='" + tdData['data-column-source'] + "'][data-row-source='" + tdData['data-row-source'] + "']");
                if (!redCss) {
                    var contentNum = tdData['content'];
                } else if (redCss) {
                    if (tdData['data-column-source'] == 'doctor_name' || tdData['data-column-source'] == 'department_name') {
                        var contentNum = htmlEncodeByRegExp(tdData['content']);
                    } else {
                        var contentNum = '<span class="eh-num">' + (tdData['content']) + ' 人</span>';
                    }

                }
                var a = '';
                if (contentNum) {
//                    for (te in contentNum) {
                        a += '<span class="eh-appointment-set-daily-detail-time reception-span' +
                            '">' + contentNum + '</span>';
//                    }
//                    a += '<i class="pencil eh-appointment-pencil eh-appointment-set-daily-add-enable" ></i>';
                }
                $target.html(a).attr("data-source", tdData['data-source']);
                $target.length != 0 ? target.push($target) : '';
            }
            callback(target);
        }
        
        
        function refreshThisCellData(data) {
            var a = '', data_date = data.dailyDetail;
            var thisCell = $(".eh-grid td[data-column-source ='"+ data['date'] +"'][data-row-source = '"+ data['doctorId'] +"'] ").eq(0);
            thisCell.find(".eh-appointment-set-daily-detail").remove();
            var dataSelectType = [];
            var tipContent = "";
            if (data_date.length == 0) {
                a = makeUseTemplateHtml(thisCell,true);
            } else {
                a += '<div class="eh-appointment-set-daily-detail" data-toggle="tooltip" data-html="true" data-placement="bottom" data-original-title = ""><br>';

                for (var i = 0; i < data_date.length; i++) {
                    var selectType = {};
                    selectType.shift_name = data_date[i].start_date + "-" + data_date[i].end_date;
                    selectType.typeNameList = data_date[i].serveTypeNames.join(',');
                    selectType.typeIdList = data_date[i].serve_types;
                    if(data_date[i].serveTypeNames.length){
                        a += '<span class="eh-appointment-set-daily-detail-cell"><span class="eh-appointment-set-daily-detail-time">' + data_date[i].start_date + "-" + data_date[i].end_date + '<br><span class="type-name-list">（' + htmlEncodeByRegExp(data_date[i].serveTypeNames.join(' / ')) + '）</span></span></span>';
                         tipContent += data_date[i].start_date + "-" + data_date[i].end_date +"  " + htmlEncodeByRegExp(data_date[i].serveTypeNames.join(' / ')) + '<br/>';
                    }else{
                        a += '<span class="eh-appointment-set-daily-detail-cell"><span class="eh-appointment-set-daily-detail-time">' + data_date[i].start_date + "-" + data_date[i].end_date + '</span></span><br>';
                        tipContent += data_date[i].start_date + "-" + data_date[i].end_date  + '<br/>';
                    }
                    dataSelectType.push(selectType);

                }
                a += '<i class="pencil eh-appointment-pencil eh-appointment-set-daily-add-enable" ></i>';
                a += '</div>';
            }
            thisCell.attr('data-select-type',JSON.stringify(dataSelectType));
            thisCell.html(a);
            if(tipContent.length>0){
                $(thisCell).find('.eh-appointment-set-daily-detail').attr('data-original-title',tipContent);
            }
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

        function makeUseTemplateHtml(theCell,isShow) {
            var dataDoctorType = JSON.parse($(theCell).attr('data-doctor-type'));
            var appendClass = isShow ? "" : "eh-mouseover"
            var appendStyle = isShow ? "" : 'style = "display:none"';
            var templateA = '';
            if(dataDoctorType == undefined || !(dataDoctorType instanceof Array) || dataDoctorType.length < 1){
                templateA = '<a class="eh-appointment-template-a" href="javascript:void(0)">';
            }else {
                var doctor_id = $(theCell).attr('data-row-source');
                var date = $(theCell).attr('data-column-source');
                var templateUrl = t.options.appointmentTimeTemplateListUrl+'?doctor_id='+doctor_id+'&date='+date;
                templateA = '<a role="modal-remote" href="'+ templateUrl +'">';
            }
            var html = '<span class="eh-appointment-set-daily-add '+ appendClass +'" '+ appendStyle +'>' +
                            '<div class="eh-appointment-set-daily-add-enable eh-appointment-cell-left" ><span class="eh-appointment-cell-btn1">添加</span></div>' +
                            templateA +
                               '<div class="eh-appointment-cell-right"> <span class="eh-appointment-cell-btn2">使用模板</span> </div>' +
                           '</a>'
                       '</span>';
            return html;
        }
    }
    // });  
})(jQuery, window);

