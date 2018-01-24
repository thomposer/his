define(function(require) {
	var claendarset = require('public/plugins/easyhincalendar/easyhincalendarset');
	var bootstrap_timepicker = require('plugins/timepicker/bootstrap-timepicker.min');
	var template = require('template');
	var common = require('js/lib/common');
	var appointmentConfig = require('tpl/appointmentConfigRow.tpl');
	var main = {
		init: function() {		
			main.initAppointmentSetInfo();
			
		},
		bind:function () {
			$('#J-select-box').find('.btn-group-left').click(function () {
				localStorage.setItem('selectOptionState',true);
				var selectOptionState = localStorage.getItem('selectOptionState');
				// var selectOptionState = true;
				main.initAppointmentSetInfo(selectOptionState);
			});
			$('#J-select-box').find('.btn-group-right').click(function () {
				localStorage.setItem('selectOptionState',false);
				main.initAppointmentSetInfo();
			});
		},
		appointmentConfigRowHtml:function(data){
			var html = '',data = data || [];
			if(data.length!=0){
				for(var i = 0;i < data.length;i++){
                                         if (i > 0) {
                                            data[i].removeTitleState = 1;
                                        }
					html += template.compile(appointmentConfig)(data[i]);
				}
			}else{
				html = template.compile(appointmentConfig)({})
			}
			return html;
		},
		appointmentConfigRow:function(){
//			if($('.appointConfig').length>=2){
				$('.clinic-add').hide();
				$('.clinic-add').last().show();
//				$('.clinic-delete').show();
				var len = $('.appointConfig').length;
//                                if (len == 1) {
//                                    $('.clinic-delete').hide();
//                                }
//			}
			$(".appointConfig .btn").unbind("click").click(function() {
				$('.clinic-add').hide();
				if ($(this).hasClass("clinic-add")) {
					var data=[{removeTitleState:1}];
					$(this).parents(".modal-content-details").append(main.appointmentConfigRowHtml(data));
					$(this).prev().show();
					$(this).hide();
					$(".clinic-delete").first().show();
				}
				if ($(this).hasClass("clinic-delete")) {
					$(this).parents(".appointConfig").remove();
				}
				$('.clinic-add').last().show();
				main.appointmentConfigRow();
			});
			$(".timepicker").datetimepicker({
                                startDate:timeConfig.begin_time,
                                endDate:timeConfig.end_time,
                                language:'zh-CN',
                                startView: 1,
				minuteStep: 10,
				initialDate: '00:00',
                                format: 'hh:ii',
                                minView: 0,
                                maxView: 1,
                                formatViewType:'time',
                                autoclose:true
			});
			// $(".clinic-add").hide().last().show();


		},
		appointmentSetShow:function(data,thisCell){
			var departName = thisCell.attr("column_department");
			var extender = main.appointmentConfigRowHtml(data);
			var setAppointmentModal = new easyhinModal({
				"id" : "setAppointment",
				"title" : "预约设置"+"（"+departName+"）",
				"confirmText" : "保存",
				"extender" : extender,
				"confirmCb" : function(){
                    var verification = true;

					$(".appointConfig").each(function(){
						var $beginTime = $(this).find(".appoint-begin-time"),
							$endTime = $(this).find(".appoint-end-time");
                                                if($beginTime.val() == ''){
                                                    showValidity($beginTime, "开始时间不能为空！");
                                                    verification = false;
                                                    return;
                                                }else{
                                                    showValidity($beginTime);
                                                }
                                                if($endTime.val() == ''){
                                                    showValidity($endTime, "结束时间不能为空！");
                                                    verification = false;
                                                    return;
                                                }else{
                                                    showValidity($endTime);
                                                }
						if($beginTime.val()>=$endTime.val()){
							showValidity($endTime,"结束时间必须大于开始时间！");
                            verification = false;
                            return;
						}else{
							showValidity($endTime);
						}
                                                if(timeConfig.begin_time&&$beginTime.val()<timeConfig.begin_time){
                                                    showValidity($beginTime, "开始时间必须大于诊所设置的开始时间!");
                                                    verification = false;
                                                    return;
                                                }else{
                                                    showValidity($beginTime);
                                                }
                                                if(timeConfig.end_time&&$endTime.val()>timeConfig.end_time){
                                                    showValidity($endTime, "结束时间必须小于诊所设置的结束时间！");
                                                    verification = false;
                                                    return;
                                                }else{
                                                    showValidity($endTime);
                                                }
					});
					$(".appoint_doctor_count").each(function(){
						if(!$(this).val()){
							showValidity($(this),"医生数量不能为空");
                            verification = false;
                            return;
						}else{
							showValidity($(this));
						}
						if(!/^([1-9][0-9]*)$/.test($(this).val())){  
					        showValidity($(this),"医生数量必须为正整数!");
                            verification = false;
                            return;
						}  
					});

					if(verification){
                        var data = main.serializeAppointmentConfig(setAppointmentModal.thisModal,thisCell);
                        main.setAppointmentConfig(data,thisCell,function(){setAppointmentModal.hide()});
					}

				},
				"showCb" : function(){

				}
			});
			setAppointmentModal.show();
//			$(".clinic-delete").first().hide();
			main.appointmentConfigRow();
		},
		serializeAppointmentConfig:function($modal,$td){
			var departId = $td.attr("column_id"),
				date = $td.attr("data-date"),
				departName = $td.attr("column_department"),
				data = {},daily_detail = [];
				$modal.find(".appointConfig").each(function(){
					var a = {};
					a.start_date = $(this).find(".appoint-begin-time").val();
					a.end_date = $(this).find(".appoint-end-time").val();
					a.doctor_count = $(this).find(".appoint_doctor_count").val();
					daily_detail.push(a);
				});
				data.id = departId;
				data.depart_name = departName;
				data.date = date;
				data.daily_detail = daily_detail;
				return data;
		},
		setAppointmentConfig:function(data,thisCell,callback){
			$.ajax({
	            url: makeAppointmentSaveConfig,
	            data: {
	                'data' : JSON.stringify(data)
	            },
	            type: "post",
	            success: function (json, response) {
	            	var json = JSON.parse(json);
	            	if(json.errorCode == 0){
	            		$('#calendars').easyhinCalendarSet('refreshCellData',data);
						callback();
						showInfo("保存成功","300px");
	            	}else{
	            		showInfo(json.msg,"300px",2);
	            	}
	            },
	            error: function (x) {

	            }
        	});
		},
		getAppointmentSetInfo:function(selectOptionState){
			var selectOptionState = localStorage.getItem('selectOptionState');
			var datejson = $('#calendars').data('easyhinCalendar').options.datejson;

				if(selectOptionState){
					var start_date = datejson.thisWeekStartDay,
						end_date = datejson.thisWeekEndDay;
				}else {
					var start_date = datejson.thisMonthStartDay,
						end_date = datejson.thisMonthEndDay;
				}
				$.ajax({
		            url: apiAppointmentAppointmentConfig,
		            data: {
		                'start_date':start_date,//预约起始时间
		                'end_date':end_date//结束时间
		            },
		            type: "post",
		            dataType:"json",
		            success: function (json, response) {
		            	
						$('#calendars').easyhinCalendarSet('refreshDate',json.data);
						main.bind();
		            },
		            error: function (x) {

		            }
	        	});
		},
		initAppointmentSetInfo:function(selectOptionState){
			var selectOptionState = localStorage.getItem('selectOptionState');
			var nowDate = new Date(nowYearMonthDate),
				nowDayOfWeek = nowDate.getDay()-1,
				nowYear = nowDate.getFullYear(),
				nowMonth = nowDate.getMonth(),
				nowDay = nowDate.getDate();
				nowDayOfWeek<0?nowDayOfWeek = 6:"";
			var currentMonthStartDay = formatDate(new Date(nowDate.getFullYear(), nowDate.getMonth(),1)),
				currentMonthEndDay = formatDate(new Date(nowDate.getFullYear(), nowDate.getMonth()+1,0)),
				weekStartDate = formatDate(new Date(nowYear, nowMonth, nowDay - nowDayOfWeek)),
				weekEndDate = formatDate(new Date(nowYear, nowMonth, nowDay + (6 - nowDayOfWeek))),
				startDay = '',
				endDay = '';

			if(selectOptionState == "true"){
				startDay = weekStartDate;
				endDay = weekEndDate;
			}else {
				startDay = currentMonthStartDay;
				endDay = currentMonthEndDay;
				var selectOptionState = false;
			}
			$.ajax({
	            url: apiAppointmentAppointmentConfig,
	            data: {
	                'start_date':startDay,//预约起始时间
	                'end_date':endDay    //结束时间
	            },
	            type: "post",
            	dataType:"json",
	            success: function (json, response) {
					var json = json.data;
					var departName = [],departId = [];
					for(var i = 0;i<json.length;i++){
						departName.push(json[i].name);
						departId.push(json[i].id||(Number(i)+999));
					}
					var configDate = {
						column: {
							departName:departName,
							departId:departId
						},
						readOnly:false,
						selectOptionState:selectOptionState,
						copyWeekConfig:copyWeekConfig,
						header:{
							backBtnUrl:referrer
						},
						switchWeekCb:function(){
						
							main.getAppointmentSetInfo(selectOptionState);
						},
						tableCellCb:function(thisCell){
							var data = [];
							thisCell.find(".eh-appointment-set-daily-detail-cell").each(function(){
								var a = {},
								time = $(this).find(".eh-appointment-set-daily-detail-time").text().split("-"),
								count = $(this).find(".eh-appointment-set-daily-detail-count").text(),
								begin_time = time[0],end_time = time[1];
								a.begin_time = begin_time;
								a.end_time = end_time;
								a.doctor_count = count;
								data.push(a);
							})
							main.appointmentSetShow(data,thisCell);					
						}
					};
					if(departName.length == 0){
						delete(configDate.column);
					}
					$('#calendars').easyhinCalendarSet("destroy");
					$('#calendars').easyhinCalendarSet(configDate);	
					$('#calendars').easyhinCalendarSet('refreshDate',json);
					main.bind();
	            },
	            error: function (x) {

	            }
        	});
		}
	};

	return main;
});

function formatDate(date,week) {
			var myyear = date.getFullYear();
			var mymonth = date.getMonth()+1;
			var myweekday = date.getDate();

			if(mymonth < 10){
			mymonth = "0" + mymonth;
			}
			if(myweekday < 10){
			myweekday = "0" + myweekday;
			}
			var time = (myyear+"-"+mymonth + "-" + myweekday);
			if(week){
				var dayNames = ["周日","周一","周二","周三","周四","周五","周六"]; 
				return {
					"time":time,
					"dayname": dayNames[date.getDay()]
				}
			}
			return time;
}
function getQueryString(name) {
	var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)", "i");
	var r = window.location.search.substr(1).match(reg);
	if (r != null)
		return r[2];
	return '';
}

