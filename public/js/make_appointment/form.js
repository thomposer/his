

	define(function(require){
		var template = require('template');
		var select = require('plugins/select2/select2.full.min');
		var bootstrap_timepicker = require('plugins/timepicker/bootstrap-timepicker.min');
        var common = require('js/lib/common');
		var cropper = require('dist/cropper');
		var uploadFile = require('tpl/uploadModal.tpl');
		var appointment = require('tpl/appointment.tpl');
		var upload = require('upload/main');
		var csrfToken = $('meta[name="csrf-token"]').attr('content');
		var appointmentTime = '';
		var appointmentTimeText = '';
		var appointmentList = '';
		var once = 0;
		var second = 0;
		var create = 0;
		var detail = 0;//判断第一次详情页挑转过来
		var appointmentType = 0;//判断预约服务是否首次加载
//		var hasCommon = 0;//判断时间是否有重复
		var main = {
			init : function(){
				this.bindEvent();
				this.selectAppointmentType();
			},	
			bindEvent : function(){
				$("button[type=submit]").click(function () {
					var jsonFormCurr = $("form").serialize();
					if(jsonFormInit != jsonFormCurr) {
						$('#appointment-hasappointmentoperator').val(1);
					}else {
						$('#appointment-hasappointmentoperator').val(0);
					}
				});
				if(deleteStatus != 1){
					$(".select2").select2({
                        "language": {
                        	"noResults": function(){
                        		return "暂无查询结果";
                        	}
                    },
                   });
				}

				var uploadModal = template.compile(uploadFile)({
					title : '上传头像',
					url : uploadUrl,
				});
                $('body').on('click','.avatar-save',function(){
                    var avatar = document.getElementById('avatarInput');
                    var filename = avatar.value;
                    var fileExtension = filename.substring(filename.lastIndexOf('.') + 1).toLowerCase();
                    if (!filename && fileExtension != 'jpg' && fileExtension != 'png' && fileExtension != 'jpeg' && fileExtension != 'gif') {
                        showInfo('请上传正确的图片格式', '180px',2);
                        return false;
                    }
                });
				if(action == 'update' && hasAppointmentDoctor != 1){
					
					main.selectAppointmentTime();
				}
				$('#crop-avatar').append(uploadModal);

				$(".timepicker").timepicker({
					showInputs: false,
					showMeridian: false,
					minuteStep: 10,
					defaultTime: '00:00',
				});

				if($('#appointment-hourmin').val()=='00:00'){
					$('#appointment-hourmin').val('');
				}
//				var doctor_id = $('#appointment-doctor_id').val();
//				if(doctor_id == '' && typeof(doctor_id) != 'undefined'){
//					once = 1;
//					second = 1;
//				}
				jsonFormInit = $("form").serialize();  //为了表单验证

				$('.field-appointment-username').bind('input propertychange',function(){
					
					$.ajax({

					     type: 'post',

					     url: getPatients ,

					     data: {
					     	'patientName' : $('#appointment-username').val()
					     } ,

					     success: function(json){
					     	$('.J-search-name').remove();
					     	if(json.data.length >= 1){
						     	
								var appointmentModal = template.compile(appointment)({
									list : json.data,
									baseUrl : baseUrl,
									cdnHost : cdnHost
								});
						     	$('.field-appointment-username').append(appointmentModal);
						     }
					     },
					     error : function(){

					     },
					     dataType: 'json'

					});
					
				});
				$('.field-appointment-iphone').bind('input propertychange',function(){

					$.ajax({

						type: 'post',

						url: getIphone ,

						data: {
							'patientIphone' : $('#appointment-iphone').val()
						} ,

						success: function(json){
							$('.J-search-name').remove();
							if(json.data.length >= 1){

								var appointmentModal = template.compile(appointment)({
									list : json.data,
									baseUrl : baseUrl,
									cdnHost : cdnHost
								});
								$('.field-appointment-iphone').append(appointmentModal);
							}
						},
						error : function(){

						},
						dataType: 'json'

					});

				});
				if(onlyAppointmentDoctor == 1){
					/* 获取预约时间 */
					$('#appointment-type').on('change',function(){
							main.selectAppointmentDoctorTime();
					});
					var doctor_id = getUrlParam('doctor_id');

					if(doctor_id && action != 'update'){// 从预约详情进来的
						$('#appointment-type').change();
					}
				}else{
					/* 获取预约时间 */
					$('#appointment-type').on('change',function(){
							main.selectAppointmentTime();
					});
					if(action != 'update'){
						$('#appointment-type').change();
					}
				}
				
				$('body').on('click','.J-name-search-submit',function(){

					var id = $(this).attr('id');
					var departmentId = getUrlParam('departmentId');
					var doctor_id = getUrlParam('doctor_id');
					var date = getUrlParam('date');
					var type = getUrlParam('type');
					var returnUrl = getUrlParam('return');	
					
					if(!departmentId || !doctor_id || !date || !type){
						window.location.href = createUrl+'?patientId='+id+'&return='+returnUrl;
						return;
					}
					window.location.href = createUrl+'?patientId='+id+'&departmentId='+departmentId+'&doctor_id='+doctor_id+'&date='+date+'&type='+type+'&return='+returnUrl;
				});
				$('body').on('click',function(){
					$('.J-search-name').remove();
				});

				$('#appointment-appointmentdate').on('change',function(){
					var val = $(this).val();
					main.htmlAppointTime(val);
				});
				//判断预约信息是否被删除，若被删除，则全部置灰，不能选
				if(deleteStatus == '1'){
					main.setAppointmentDisabled();
				}

			},
			selectAppointmentType : function(){

				//医生下拉框值改变时
				$('#appointment-doctor_id').change(function(){
					var val = $(this).val();
					var type =  getUrlParam('type');//获取预约服务
					var onlyOneType = 0;//判断是否该医生只有一个服务类型
					if(val){
						$.ajax({

						     type: 'post',

						     url: apiAppointmentGetAppointmentType,

						     data: {
						     	'id' : val,
						     },
						     success: function(json){
						     	var a = '<option value="">请选择</option>';
						     	if(appointmentType == 0 && action == 'update'){
						     		a += '<option value="'+spotTypeId+'" selected>'+spotTypeName+'</option>';
						     	}
						     	var hasType = action == 'update'?0:1;
						     	var result  = json.data;
						     	if(deleteStatus != 1){
							     	if(result.length >= 1){
							     		
								     	for( key in result){
								     		if(appointmentType == 0 && parseInt(result[key]['id']) == parseInt(spotTypeId) && action == 'update'){
								     			hasType = 1;
								     		}else if(appointmentType == 0 && result[key]['id'] == spotTypeId && error == 1){
								     			a += '<option value="'+result[key]['id']+'" selected>'+htmlEncodeByRegExp(result[key]['type'])+'</option>';
								     		}else if(result.length == 1){
								     			onlyOneType = 1;
								     			a += '<option value="'+result[key]['id']+'" selected>'+htmlEncodeByRegExp(result[key]['type'])+'</option>';
								     		}else{
								     			a += '<option value="'+result[key]['id']+'">'+htmlEncodeByRegExp(result[key]['type'])+'</option>';
								     		}
								     	}
										
								     }else{
								    	 if(action == 'update' && appointmentType == 0){
								    		 main.setAppointmentDisabled();
								     	}
								     }
						     	}
							     $('#appointment-type').html(a);
							     if((appointmentType == 0 && action == 'update') || (appointmentType == 0 && error == 1)){
							    	 main.selectAppointmentDoctorTime();
							     }
							     //当为预约详情入口进来时，默认初始化对应的预约时间，预约服务类型
							     if(action == 'create' && type != null && error == 0 && appointmentType == 0){
							    	 $('#appointment-type').val(type);
							    	 main.selectAppointmentDoctorTime();
							     }
							     if(onlyOneType == 1){//若只有只一个服务类型，默认调用获取预约时间
							    	 main.selectAppointmentDoctorTime();
							     }
							     if(hasType == 0 && appointmentType == 0){
							     		main.setAppointmentDisabled();
							     }
							     appointmentType = 1;
							     $('#select2-appointment-appointmentdate-container').html('请选择预约日期');
							     $('#select2-appointment-time-container').html('请选择预约时间');
								 $('#appointment-appointmentdate').html('');
								 $('#appointment-time').html('');
								 jsonFormInit = $("form").serialize();  //为了表单验证
							     		
						     },
						     error : function(){

						     },
						     dataType: 'json'

						});
					}else{
				     	var a = '<option value="">请选择</option>';
						$('#appointment-type').html(a);
					}
				}).change();
				
			},
			selectAppointmentDoctorTime : function(){
				var type = $('#appointment-type').val();
				var doctor_id = $('#appointment-doctor_id').val();
				var id = getUrlParam('id');
				console.log('dsadsadsad:'+create);
				console.log('type::'+type);
				console.log('doctor_id:'+doctor_id);
				if(type == '' || doctor_id == ''){
					if(create == 1){
						$('#select2-appointment-appointmentdate-container').html('请选择预约日期');
						$('#select2-appointment-time-container').html('请选择预约时间');
						$('#appointment-appointmentdate').html('');
						$('#appointment-time').html('');
						
					}
					return;
				}
				create = 1;
				$.ajax({

					     type: 'post',

					     url: apiAppointmentDoctorTime ,

					     data: {
					     	'doctorId' : doctor_id,
					     	'type' : type,
					     	'id' : id
					     },
					     success: function(json){
					     	appointmentList = json.data;
					     	main.htmlAppointDate(json.data);
					     	
					     },
					     error : function(){

					     },
					     dataType: 'json'

					});

			},
			selectAppointmentTime : function(){
					var type = $('#appointment-type').val();
					var doctor_id = $('#appointment-doctor_id').val();
					if(doctor_id == '' || type == ''){
						if(once == 1){

							$('#select2-appointment-appointmentdate-container').html('请选择预约日期');
							$('#select2-appointment-time-container').html('请选择预约时间');
							$('#appointment-appointmentdate').html('');
							$('#appointment-time').html('');
						}
						once = 1;

						return;
					}
					if(doctor_id != '' && typeof(doctor_id) != 'undefined'){
						main.selectAppointmentDoctorTime();
						return;
					}
					$.ajax({

					     type: 'post',

					     url: manageSitesAppointmentTime ,

					     data: {
					     	'id' : departmentId,
					     	'type' : type,
					     	'_csrf' : csrfToken
					     } ,

					     success: function(json){
					     	appointmentList = json.data;
					     	main.htmlAppointDate(json.data);
					     	
					     },
					     error : function(){

					     },
					     dataType: 'json'

					});
			},
			htmlAppointDate : function(data){
				var weekArray = ['周日','周一','周二','周三','周四','周五','周六'];
				var time = '<option value="">请选择预约日期</option>';
				var appointmentTime = date;
				var doctor_id = getUrlParam('doctor_id');

				var hasValue = 0;	
				if(once == 0 && action == 'update'){
					if(appointmentTime){
						var week = appointmentTime + '（'+weekArray[new Date(appointmentTime).getDay()]+'）';
						time += '<option value ="'+appointmentTime+'" selected>'+week+'</option>';
//						setTimeout(function () {
							$('#select2-appointment-appointmentdate-container').html(week);
//						},0);	
					}
				}else{	
					$('#select2-appointment-appointmentdate-container').html('请选择预约日期');
				}
				main.htmlAppointTime(appointmentTime);
				if(deleteStatus != 1){
					if(data != ''){

						for(var o in data){
							var week = o + '（'+weekArray[new Date(o).getDay()]+'）';
						    if(o != appointmentTime){
						    	if(action == 'update'){
						    		hasValue = 1;
						    	}
						     	time += '<option value="'+o+'">'+week+'</option>';
	
						    }else{
						    	hasValue = 1;
						    	$('#select2-appointment-appointmentdate-container').html(week);	
						    	if(once == 1 || doctor_id){     	
						    		time += '<option value="'+o+'"  selected>'+week+'</option>';
						    	}else if(once == 0 && error == 1 && action != 'update'){//若有验证错误。则默认回填错误信息
						    		time += '<option value="'+o+'"  selected>'+week+'</option>';
						    	}
						    	
						    }
						}
						     		
					}
				}
				if(hasValue == 0){
					$('#select2-appointment-appointmentdate-container').html('请选择预约日期');	     	
				}
				once = 1;

				$('#appointment-appointmentdate').html(time);
				jsonFormInit = $("form").serialize();  //为了表单验证
			},
			htmlAppointTime : function(data){
				var time = '<option value="">请选择预约时间</option>';
				var appointmentTime = dateTimeValue;
				var appointmentText = dateTimeText;
				var hasValue = 0;	
				var hasCommon = 0;
				//若为页面首加载，则判断为编辑，还是验证失败导致，决定是否回填历史数据
				if((second == 0 && action == 'update') || (second == 0 && error == 1)){
					if(appointmentTime){
						setTimeout(function () {
							$('#select2-appointment-time-container').html(appointmentText);	     	
                    	}, 0);	
						time += '<option value ="'+appointmentTime+'" selected>'+appointmentText+'</option>';	
						hasCommon = 1;     		
					}
					second = 1;
				}else{					
					$('#select2-appointment-time-container').html('请选择预约时间');
				}

				if(deleteStatus != 1){
					if(appointmentList[data] != ''){
	
						for(var o in appointmentList[data]){ 
							var disabled = '';
						    var msg = '';
//						    console.log(appointmentList[data][o].value);
//						    console.log(appointmentTime);
//						    console.log(hasCommon);
						    if(!appointmentList[data][o].selected){
						     	disabled = 'disabled';
						     	msg = '(不可选)';
						    }
						    if(appointmentList[data][o].value == appointmentTime){
						    	if(appointmentList[data][o].selected){
						    		hasValue = 1;
						    	}
						    	console.log(hasCommon);
						    	if(hasCommon == 0){
						    		$('#select2-appointment-time-container').html(appointmentList[data][o].name);	     	
						    		time += '<option value="'+appointmentList[data][o].value+'" '+disabled+' selected>'+appointmentList[data][o].name+msg+'</option>';
						   		}
						    }else if(appointmentList[data][o].value != appointmentTime || second == 1){
						    	if(action == 'update'){
						    		hasValue = 1;
						    	}
						     	time += '<option value="'+appointmentList[data][o].value+'" '+disabled+'>'+appointmentList[data][o].name+msg+'</option>';
	
						    }
						}
						     		
					}
				}
				if(hasValue == 0){
					$('#select2-appointment-time-container').html('请选择预约时间');	     	
				}
				$('#appointment-time').html(time);
				jsonFormInit = $("form").serialize();  //为了表单验证
			},
			setAppointmentDisabled : function(){
				$('#appointment-doctor_id').attr({'disabled':true});
				$('#appointment-type').attr({'disabled':true});
				$('#appointment-appointmentdate').attr({'disabled':true});
				$('#appointment-time').attr({'disabled':true});
				$('#appointment-appointment_origin').attr({'disabled':true});
			}
		};

		return main;
	})