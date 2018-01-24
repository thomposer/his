define(function(require){
	var _self;
	var countdown = 60;
	var common = require('../lib/common.js');
	var content='';
	var main = {
		init: function () {
			_self = this;
			this.bindEvent();

			$('body').on('click', '#batch-myform', function () {
				$('.code-message').empty();
			})

				$('#batch-form').yiiAjaxForm({
					beforeSend: function() {
						$.ajax({
							url:codeUrl,
							data: {
								'content' :$("#batch-form").serializeArray() ,
								'token':token,
							},
							type: "post",
							success: function (data, textStatus, jqXHR) {
								if(data.errorCode != 0){
									$('.code-message').empty();
									content='<span style="color:#ff4b00">'+data.msg+'</span>';
									$('.code-message').append(content);
								}
							},
							error: function () {
								showInfo('系统问题，请稍后再试', '180px',2);
							},
							dataType: 'json'
						})
					},
					complete: function() {
					},
					success: function(data) {
					},
					error : function(){
					}
				});

		},

		bindEvent: function () {
			$('body').on('click', '#btn', function () {
				$.ajax({
					url:disUrl,
					data: {
						 'token' :token ,
					},
					type: "post",
					success: function (data, textStatus, jqXHR) {
						if(data.errorCode == 0){
							$('.code-message').empty();
							content='<span>已向'+data.iphone.substring(0,3)+'****'+data.iphone.substring(7,11)+'发送手机验证码，请查收</span>';
							$('.code-message').append(content);
							_self.setTime();
						}else{
							showInfo(data.msg, '280px',2);
							$('#btn'). removeClass('btn-code-off').addClass('btn-code-on');
						}
					},
					error: function () {
						showInfo('系统异常,请稍后再试', '280px',2);
						$('#btn'). removeClass('btn-code-off').addClass('btn-code-on');
						$('#btn').attr('disabled', true);
					},
					dataType: 'json'
				});
			})

			if($('#user-password').val()!=''){
				$('#btn'). removeClass('btn-code-off').addClass('btn-code-on');
				$('#btn').attr("disabled",false);
			};

			_self.fromFocus();
		},
		setTime: function () {
			 if (countdown <= 0) {
			 $("#btn").removeAttr("disabled"); //移除disabled属性
			 $('#btn span').text('获取验证码');
			 $('#btn'). removeClass('btn-code-off').addClass('btn-code-on');
			 countdown = 60;
			 return;
			 } else {
			 $('#btn').attr('disabled', true);
			 $('#btn'). removeClass('btn-code-on').addClass('btn-code-off');
			 $('#btn span').text('重新发送'+countdown);
			 countdown--;
				 setTimeout(function()
			 { _self.setTime()},1000)
			 }
		},

		fromFocus : function(){
			$('body').on('change','#user-password',function(){
				$('#btn'). removeClass('btn-code-off').addClass('btn-code-on');
				$('#btn').attr("disabled",false);
			});
		}
	};
	return main;
})