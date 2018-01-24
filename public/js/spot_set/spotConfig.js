

	define(function(require){
		var template = require('template');
		var common = require('js/lib/common');
		var cropper = require('dist/cropper');
		var uploadFile = require('tpl/uploadModal.tpl');
		var upload = require('upload/main');

		var main = {
			init : function(){
				this.logoDeal();
				this.logoUpload();
				this.uploadFile();//上传logo图片
				this.setLogoSize();//根据形状设置logo大小
				this.recipeDebate();
				//this.startCropper();//重写
				//upload.startCropper(1);
			},
			logoDeal : function(){

				/*
				 打印设置取消返回上一页面
				*/

				$('.spot-config-cancel').click(function(e){

						history.go(-1);

				});
				$('.conf-img-delImg').click(function(e){

					var cancel_option = {
						label: "取消",
						className: 'btn-default  btn-form',
					};
					var confirm_option = {
						label: "确定",
						className: 'btn-cancel btn-form',
					};
					btns = {
						cancel: cancel_option,
						confirm: confirm_option,

					};
					bootbox.confirm(
							{
								message: '你确定要删除此项吗?',
								title: '系统提示',
								buttons: btns,
								callback: function (confirmed) {
									if (confirmed) {
										$('.conf-img-show').attr('src',baseUrl+'/public/img/common/img_logo.png');
										$('#avatar_url').attr('value','');
										//设置删除后的logo默认图的显示
										var type = $("input[name='SpotConfig[logo_shape]']:checked").val();
										if(type == 1){
											$('.conf-img-show').css("width","120px")
											$(".conf-img-show").attr('src',"/public/img/common/img_logo.png");
										}else{
											$('.conf-img-show').css("width","360px")
											$(".conf-img-show").attr('src',"/public/img/common/img_logo_chang.png");
										}
										$('.spot-config-ImgDel').addClass('hide');
									} else {
										return true;
									}
								}
							}
					);
				});



			},
			uploadFile : function(){
				$('body').on('click','.avatar-save',function(){
					var avatar = document.getElementById('avatarInput');
					var filename = avatar.value;
					var fileExtension = filename.substring(filename.lastIndexOf('.') + 1).toLowerCase();
					console.log(fileExtension);
					if (!filename && fileExtension != 'jpg' && fileExtension != 'png' && fileExtension != 'jpeg' && fileExtension != 'gif') {
						showInfo('请上传正确的图片格式', '180px',2);
						return false;
					}
					$('.spot-config-ImgDel').removeClass('hide');
				});
			},
			logoUpload : function(){
				var uploadModal = template.compile(uploadFile)({
					title : '上传诊所图标',
					url : uploadUrl,

				});
				$('#crop-avatar').append(uploadModal);





			},
			setLogoSize : function () {
				//初始化设置logo大小
				var type = $("input[name='SpotConfig[logo_shape]']:checked").val();
				if(type == 1){
					$(".avatar-width").val("120");
					$(".avatar-height").val("120");
					$('.conf-img-show').css("width","120px")
				}else{
					$(".avatar-width").val("360");
					$(".avatar-height").val("120");
					$('.conf-img-show').css("width","360px")
				}


				//监听选择，设置logo大小
				$("input[name='SpotConfig[logo_shape]']").click(function () {
					var val = $(this).val();
					var avatar_url = $('#avatar_url').attr('value');
					//判断是否有上传图片，如果没有，证据形状显示不同的默认图
					if(avatar_url !=''){
						if(val == 1){
							$(".avatar-width").val("120");
							$(".avatar-height").val("120");
							$('.conf-img-show').css("width","120px")
						}else{
							$(".avatar-width").val("360");
							$(".avatar-height").val("120");
							$('.conf-img-show').css("width","360px")
						}
					}else{
						if(val == 1){
							$(".avatar-width").val("120");
							$(".avatar-height").val("120");
							$('.conf-img-show').css("width","120px");
							$(".conf-img-show").attr('src',baseUrl + "/public/img/common/img_logo.png");
						}else{
							$(".avatar-width").val("360");
							$(".avatar-height").val("120");
							$('.conf-img-show').css("width","360px");
							$(".conf-img-show").attr('src',baseUrl + "/public/img/common/img_logo_chang.png");
						}
					}


				});

			},
				/*
				处方打印设置
				 */
			recipeDebate:function(){
				var recipeRebateUrlInit=$('.recipe-rebate-url').attr('href');
				this.recipeDebateInit(recipeRebateUrlInit);
				$('#spotconfig-recipe_rebate').bind("change",function(){
					main.recipeDebateInit(recipeRebateUrlInit);
				});
			},

			/*
			初始化处方打印路由url
			 */
			recipeDebateInit: function (recipeRebateUrlInit) {
				var recipeRebateValue=$('#spotconfig-recipe_rebate option:selected').val();
				var recipeRebateUrl=recipeRebateUrlInit;
				if(recipeRebateValue==1){
					recipeRebateUrl+="&type=1";
				}else if(recipeRebateValue==2){
					recipeRebateUrl+="&type=2";
				}
				$('.recipe-rebate-url').attr('href',recipeRebateUrl);
			},



		};
		return main;
	});