define(function(require){
	var echarts = require('public/plugins/chartjs/echarts.min');
    var template = require('template');
    var chartTpl = require('tpl/chart.tpl');
	var main = {
			init : function(){
                main.bindEvent();
                main.get_chart_data();
                //main.set_td_color();
			},
            bindEvent : function(){
                $('#selectChart').click(function () {
                    main.get_chart_data();
                });
                $('input[type="radio"]').click(function () {

                    if(1 == $(this).attr('value')){

                        $.pjax({url: "/H5-his/data/data/index.html?forms_type=1", container: '#crud-datatable-pjax'})
                    }else {

                        $.pjax({url: "/H5-his/data/data/index.html?forms_type=2", container: '#crud-datatable-pjax'})
                    }

                });
            },
            get_chart_data:function () {
                var date = $('#chartDate').val()?$('#chartDate').val():chartDate;
                $.ajax({
                    type: 'POST',
                    url: apiGetChartData,
                    data: {
                        date:date
                    },
                    cache: false,
                    dataType: 'json',
                    success: function (json) {
                        if(0 == json.errorCode) {
                            var dateArray = date.split("-");
                            var newDate = '（'+dateArray[0]+'年'+dateArray[1]+'月'+dateArray[2]+'日'+' '+getDay(new Date(dateArray[0]+'/'+dateArray[1]+'/'+dateArray[2]).getDay())+'）';
                            $('.chart-title-span').html(newDate);
                            var chartHtml = template.compile(chartTpl)({
                                data: json.data
                            });
                            $('#chartMain').html('');
                            $('#chartMain').html(chartHtml);
                        }

                    },
                    error: function () {

                    }
                });
            },
            get_chart:function () {
                var html = '';

                return html;
            },
            get_line_chart : function(title,xArray,data){
                var myChart = echarts.init(document.getElementById('chartMain'));
                var option = {  // 指定图表的配置项和数据
                    title: {
                        text: title
                    },
                    tooltip: {},
                    legend: {
                        data:['销量']
                    },
                    xAxis: {
                        data: xArray
                    },
                    yAxis: {},
                    series: [{
                        name: '',
                        type: 'line',
                        data: data
                    }]
                };
                myChart.setOption(option); // 使用刚指定的配置项和数据显示图表。
            },

        //set_td_color:function(){
        //    var tdId=$('tbody').find('tr').eq(0).find('td');
        //    tdId.each(function(k){
        //        $(this).addClass('data_report_header_'+k);
        //    });
        //    //console.log($("tbody tr:first-child td").data("col-seq"));
        //    //if($("td").data("col-seq") == 1){
        //    //    console.log("hhhh");
        //    //}
        //}
    };
    return main;
});
function getQueryString(name) {
    var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)", "i");
    var r = window.location.search.substr(1).match(reg);
    if (r != null)
        return r[2];
    return '';
}
function getDateStr(addDayCount) {
    var dd = new Date();
    dd.setDate(dd.getDate()+addDayCount);//获取AddDayCount天后的日期
    var y = dd.getFullYear();
    var m = dd.getMonth()+1;//获取当前月份的日期
    var d = dd.getDate();
    return y+"-"+m+"-"+d;
}
function getDay(weekDay) {
    var weekDayString = "";
    if (weekDay == 1) {
        weekDayString = "周一";
    } else if (weekDay == 2) {
        weekDayString = "周二";
    } else if (weekDay == 3) {
        weekDayString = "周三";
    } else if (weekDay == 4) {
        weekDayString = "周四";
    } else if (weekDay == 5) {
        weekDayString = "周五";
    } else if (weekDay == 6) {
        weekDayString = "周六";
    } else if (weekDay == 7) {
        weekDayString = "周日";
    }
    return weekDayString;
}