define(function(require) {
	var claendarset = require('public/plugins/easyhincalendar/easyhinschedule');
	var bootstrap_timepicker = require('plugins/timepicker/bootstrap-timepicker.min');
	var template = require('template');
	var common = require('js/lib/common');
	var appointmentConfig = require('tpl/appointmentConfigRow.tpl');
	var main = {
		sortType:0,
		thisCell:null,
		nowYearMonthDate:nowYearMonthDate,
		schedule:{},
		init: function() {

			main.viewScheduleSelect();
			main.initScheduleRecord();
			main.bind();
			$('body').on("input propertychange",'.scheduleset',function () {
				var schedule_id = $(this).val();
				var schedule_value =$(this).find("option:selected").text();

				main.setScheduleRecord(schedule_id,schedule_value)
			});
			//$('body').on("click",'.eh-appointment-set-daily-content',function () {
			//	var schedule_id = $(this).find('span').html();
			//	var scheduleset = $(this).find('span').find('.scheduleset').html();
			//	var date = $(this).attr('data-column-source');
			//	var selectDate = new Date(date);
			//	var nowDate = new Date(nowYearMonthDate);
			//	if(selectDate >= nowDate){
			//		if(!scheduleset){
			//			$(this).html('');
			//			var b = main.viewScheduleSelect();
			//			$(this).append(b);
			//			$(this).children('.eh-mouseover').show();
			//		}
			//	}
            //
			//});
		},
		serializeScheduleGridConfig:function(json){
			var data = json,result = {},
				column = [{
					"name":"员工",
					"data-source":"doctor_name",
					"readonly":true
				},
				{
					"name":"职位",
					"data-source":"occupation",
					"readonly":true
				}],
				rowOb = {},
				occupationCell = {},//第二列职业数据
				gridDate = [];
			var userlist = [];
			var saveduserID = [];
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
						gridCell['content'] = schedule.shift_name;
						gridDate.push(gridCell);
					}

					if ($.inArray(schedule.doctor_id ,saveduserID) < 0){
                        var userInfo = {};
                        userInfo['doctor_id'] = schedule.doctor_id;
                        userInfo['doctor_name'] = schedule.doctor_name;
                        userInfo['occupation'] = schedule.occupation;
                        saveduserID.push(schedule.doctor_id);
                        userlist.push(userInfo);
					}

				}
			}

			var row = [];
			var occupation = [];
			for(var i = 0 ; i < userlist.length; i++){

				var userInfo = userlist[i];
                var row_head = {};
                row_head['name'] = userInfo['doctor_name'];
                row_head['data-source'] = userInfo['doctor_id'];
                row.push(row_head);

                var occupa = {};
                occupa['data-row-source'] = userInfo['doctor_id'];
                occupa['data-column-source'] = 'occupation';
                occupa['content'] = userInfo['occupation'];
                occupation.push(occupa);
			}


			result['column'] = column;//[{ name="职业",data-source="occupation",readonly=true},{ name="周一(05/02)",data-source="2016-05-02"}]
			result['row'] = row;//[{ name="王晓辉",data-source="11"},{name="刘医生",data-source="12"}]
			result['data'] = gridDate;//[{ data-row-source="12",data-column-source="2016-05-04",data-source=1,content='全天班'}]
			result['occupation'] = occupation;

			return result;
		},
		appointmentConfigRowHtml:function(data){
			var html = '',data = data || [];
			if(data.length!=0){
				for(var i = 0;i < data.length;i++){
					html += template.compile(appointmentConfig)(data[i]);
				}
			}else{
				html = template.compile(appointmentConfig)({})
			}
			return html;
		},
		getScheduleList:function(thisCell){
			$.ajax({
	            url: apiSchedulingScheduleConf,
	            data: {
	                // 'data' : JSON.stringify(data)
	            },
	            type: "post",
	            success: function (json, response) {
	            	var json = JSON.parse(json);
	            	if(json.errorCode == 0){
							//main.viewScheduleList(json.data,thisCell);


	            		$("#set_schedule_select").on("click",function(){
							showValidity($(this));
						});
	            	}else{
	            		showInfo("失败！"+json.msg,"300px");
	            	}
	            },
	            error: function (x) {

	            }
        	});
		},
		bind:function(){
			$('body').on('click','.search_button',function(){
				main.getAppointmentSetInfo();
			});
		},
		initScheduleRecord: function() {
			var now = new Date(nowYearMonthDate);
			var nowDayOfWeek = now.getDay() - 1; //今天本周的第几天,按星期一为第一天
			nowDayOfWeek < 0 ? nowDayOfWeek = 6 : "";
			var	currentWeekStartDay = formatDate(new Date(now.getFullYear(), now.getMonth(),now.getDate() - nowDayOfWeek));
			var	currentWeekEndDay = formatDate(new Date(now.getFullYear(), now.getMonth(), now.getDate() + (6 - nowDayOfWeek)));
			$.ajax({
				url: apiSchedulingIndex,
				data: {
					'start_date': currentWeekStartDay,
					'end_date': currentWeekEndDay,
					'name': $("#schedulingsearch-user_id").val(),
					'department_id': $("#schedulingsearch-schedule_id").val(),
					'sort_type': main.sortType,
					'occupation': $("#schedulingsearch-occupation").val(), //人员职位,
					occupationList:occupationList,
					entrance:entrance
				},
				type: "post",
				success: function(json, response) {
					var json = JSON.parse(json);
					if (json.errorCode == 0) {
						var data = main.serializeScheduleGridConfig(json.data);
						var configDate = {
							column: data.column,
							row: data.row,
							datajson: data.data,
							readOnly: false,
							addPlus:true,//是否要mouseover “+添加”字样,
							schedule_list:main.viewScheduleSelect(),
							//scheduleOpt:JSON.parse(scheduleOpt),
							switchWeekCb: function() {
								main.getAppointmentSetInfo();
							},
							tableCellCb: function(thisCell) {

								main.getScheduleList(thisCell);


								main.thisCell = thisCell;

								if(typeof(thisCell.attr('data-source'))=='undefined'){
									$("#set_schedule_select").val(0);
								}else{
									$("#set_schedule_select").val(thisCell.attr('data-source'));
								}
							}
						};
						$('#schedule_grid').easyhinGrid(configDate);
						$('#schedule_grid').easyhinGrid('refreshCellData', data.occupation); //刷新职业列数据
                        $("th[data-source='occupation']").css({"cursor": "pointer", "color": "#76A6EF"}).on("click", function () {
								main.sortType = main.sortType > 0 ? 0 : 1;
								main.getAppointmentSetInfo();8

						});
					} else {
						showInfo("失败！" + json.msg, "300px");
					}
				},
				error: function(x) {

				}
			});
		},
		getAppointmentSetInfo:function(){
		var datejson = $('#schedule_grid').data('easyhinCalendar').options.datejson;
			$.ajax({
	            url: apiSchedulingIndex,
	            data: {
	                'start_date':datejson.currentWeek[0],//预约起始时间
	                'end_date':datejson.currentWeek[6],//结束时间
	                'name': $("#schedulingsearch-user_id").val(),
					'department_id': $("#schedulingsearch-schedule_id").val(),
                    'sort_type': main.sortType,
					'occupation': $("#schedulingsearch-occupation").val() //人员职位
	            },
	            type: "post",
	            success: function (json, response) {
	            	var json = JSON.parse(json);
					if (json.errorCode == 0) {
						var data = main.serializeScheduleGridConfig(json.data);

						var configDate = {
							column: data.column,
							row: data.row,
							datajson : data.data,
							initDate :datejson.currentWeek[0],
							readOnly: false,
							addPlus:true,//是否要mouseover “+添加”字样,
							schedule_list:main.viewScheduleSelect(),
							//scheduleOpt:JSON.parse(scheduleOpt),
							occupation:$("#schedulingsearch-occupation").val(),
							occupationList:occupationList,
							entrance:entrance,
							switchWeekCb: function() {
								main.getAppointmentSetInfo();
							},
							tableCellCb: function(thisCell) {
								main.getScheduleList(thisCell);
								main.thisCell = thisCell;
								if(typeof(thisCell.attr('data-source'))=='undefined'){
									$("#set_schedule_select").val(0);
								}else{
									$("#set_schedule_select").val(thisCell.attr('data-source'));
								}

							}
						};
						$('#schedule_grid').easyhinGrid('refreshGrid',configDate);
						// $('#schedule_grid').easyhinGrid('refreshData', data.data);
						$('#schedule_grid').easyhinGrid('refreshCellData', data.occupation); //刷新职业列数据
                        $("th[data-source='occupation']").css({"cursor": "pointer", "color": "#76A6EF"}).on("click", function () {
                            main.sortType = main.sortType > 0 ? 0 : 1;
                            main.getAppointmentSetInfo();

                        });
						
					} else {
						showInfo("失败！" + json.msg, "300px");
					}
	            },
	            error: function (x) {

	            }
        	});
		},
		setScheduleRecord:function(schedule_id,schedule_value){
			//var schedule_id = $("#set_schedule_select").val();
			if(!schedule_id){
				var schedule_id = 0;
			}
			$.ajax({
	            url: scheduleSchedulingAddScheduling,
	            data: {
	                'date' : main.thisCell.attr('data-column-source'),
	                'worker_id' : main.thisCell.attr('data-row-source'),
	                'schedule_id' : schedule_id
	            },
	            type: "post",
	            success: function (json, response) {
	            	var json = JSON.parse(json)

	            	if(json.errorCode == 0){
	            		if(schedule_id != 0 ){

							main.thisCell.attr("data-source",schedule_id).html('<span class="eh-appointment-set-daily-detail-time reception-span">'+htmlEncodeByRegExp(schedule_value)+'</span>');
						}
						else {
							main.thisCell.removeAttr("data-source").html(main.viewScheduleSelect());
						}

	            		//setScheduleModal.hide();
	            		showInfo("保存成功!","300px");
	            	}else{
	            		showInfo("系统异常，保存失败！"+json.msg,"300px");
	            	}

					var parentDiv = $('.eh-mouseover').parent();
					parentDiv.mouseover(function () {
						$(this).children('.eh-mouseover').show();
					});
					parentDiv.mouseout(function () {
						$(this).children('.eh-mouseover').hide();
					});
	            },
	            error: function (x) {

	            }
        	});
		},
		viewScheduleList:function(data,thisCell){

			var thisFirstCell = thisCell.siblings().eq(0);
			var doctorName = thisFirstCell.find('span').text();
			var nowDate = thisCell.attr('data-column-source');
			var selectedId = thisCell.attr('data-source');
			var a = '';
			a += '<div style="width:80%;margin:0 auto">';
			a += '<select  class="form-control" id="set_schedule_select">';
			a += '<option value="0">';
			a += '选择班次';
			a += '</option>';
			for(var i = 0;i < data.length;i++){
				var selectedType = (selectedId == data[i].id)?"selected = selected":'';
				a += '<option value="'+data[i].id+'" '+ selectedType + '>';
				a += data[i].shift_name;
				a += '</option>';
			}
			a += '</select>';
			a += '</div>';
			setScheduleModal = new easyhinModal({
				"id": "setSchedule",
				"title": "班次设置(" + doctorName + '-' + nowDate + ')',
				"confirmText": "保存",
				"extender": a,
				"confirmCb": function () {
					main.setScheduleRecord();
				},
				"showCb": function () {

				}
			});
			setScheduleModal.show();

		},
		viewScheduleSelect:function(){
			var a ='';
			a +=   '<span class="eh-mouseover eh-appointment-set-daily-add eh-appointment-set-daily-add-enable" style="display: none;">';
			a +=   '<div class="form-horizontal">';
			a +=        '<div class="form-group field-scheduleset has-success">';
			a +=    '\t\t<select id="scheduleset" class="scheduleset form-control">';
			a +=    '\t\t<option value="">请选择</option>';
			if(scheduleOpt.length>0){
				for(var k=0;k<scheduleOpt.length;k++){
					a +=    '<option value="'+scheduleOpt[k].id+'">'+htmlEncodeByRegExp(scheduleOpt[k].shift_name)+'</option>';
				}
			}
			a +=    '</select>';
			a +=     '</div>   ';
			a +=   '</div>';
			a +=  '</span>';
			return a;
		},

	};

	return main;
});
function calendarHeadFormatDate(string){
		var date = string.split("-");
		return "("+date[1]+"/"+date[2]+")";
}

