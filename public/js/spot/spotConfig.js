

	define(function(require){
		var bootstrap_timepicker = require('plugins/timepicker/bootstrap-timepicker.min');

		var main = {
			init : function(){
				this.bindEvent();
				
			},	
			bindEvent : function(){
				$('body').on('focus', "#spotconfig-begin_time", function () {
					$("#spotconfig-begin_time").timepicker({
						showInputs: false,
						showMeridian: false,
						minuteStep: 10,
						defaultTime: '08:00'
					});
				});

				$('body').on('focus', "#spotconfig-end_time", function () {
					$("#spotconfig-end_time").timepicker({
						showInputs: false,
						showMeridian: false,
						minuteStep: 10,
						defaultTime: '20:00'
					});
				});
                                
                                $('body').off('change','#spottype-organization_type_id').on('change', '#spottype-organization_type_id', function(){
                                    console.log(status);
                                    if(status == 0){
                                        return ;
                                    }
                                    var id = $('#spottype-organization_type_id').val();
                                    console.log(id);
                                    $.ajax({
                                        type: 'post',
                                        url: apiTypeConfigGetTypeTime,
                                        data: {
                                            'id' : id
                                        },
                                        success: function(json){
                                            var time = json.time;
                                            $('#spottype-time').val(time);
                                        },
                                        error : function(){

                                        },
					dataType: 'json'
                                        
                                    });
                                });

			},
			
		};
		return main;
	})