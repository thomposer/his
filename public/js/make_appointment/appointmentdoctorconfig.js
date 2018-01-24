define(function(require) {
	var easyhincalendardoctorconfig = require('public/plugins/easyhincalendar/easyhincalendardoctorconfig');
	var common = require('js/lib/common');
	var main = {
		init: function () {
			$('#calendar').easyhinCalendarConfig({
				timeLine: timeLine,//时间间隔
				doctorInfo:doctorInfo,//可预约医生
				switchDayCb: function () {
					main.getAppointmentSetInfo();
				},
				title: " ",//title显示的内容
				readDate: false,//是否显示日期
				addClass:true
			});
			main.getAppointmentIndex();
			$('.search_button button[type="button"]').on('click', function () {
				main.getAppointmentIndex();
			})
		},

		getAppointmentIndex:function(){
			var nowDate = new Date(nowYearMonthDate),
				nowDay = nowDate.getDate(), //当前日
				nowMonth = nowDate.getMonth(), //当前月
				nowYear = nowDate.getFullYear(); //当前年
				nowdate=formatDate((new Date(nowYear, nowMonth, nowDay)));

			$.ajax({
				url: appointDoctorConfig,
				data: {
					'date':nowdate,//预约起始时间
				},
				type: "post",
				dataType:"json",
				success: function (json, response) {
					$('#calendar').easyhinCalendarConfig('refreshDate',json);
				},
				error: function (x) {

				}
			});
		},

		getAppointmentSetInfo:function(){
			var initDate = $('#calendar').data('easyhinCalendar').options.initDate;

			$.ajax({
				url: appointDoctorConfig,
				data: {
					'date':initDate,//预约起始时间
				},
				type: "post",
				dataType:"json",
				success: function (json, response) {
					
					$('#calendar').easyhinCalendarConfig('refreshDate',json);

				},
				error: function (x) {

				}
			});
		}
	};
	return main;
})

function formatDate(date) {
	var myyear = date.getFullYear();
	var mymonth = date.getMonth()+1;
	var myweekday = date.getDate();

	if(mymonth < 10){
		mymonth = "0" + mymonth;
	}
	if(myweekday < 10){
		myweekday = "0" + myweekday;
	}
	return (myyear+"-"+mymonth + "-" + myweekday);
}