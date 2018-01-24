

	define(function(require){
		var template = require('template');
		var common = require('js/lib/common');
//		var cityPickerData = require('js/lib/city-picker.data');
//		var cityPicker = require('js/lib/city-picker');
		var cropper = require('dist/cropper');
		var uploadFile = require('tpl/uploadModal.tpl');	
		var upload = require('upload/main');
		var main = {
			init : function(){
				this.bindEvent();
				$('body').on('click','.avatar-save',function(){
					var avatar = document.getElementById('avatarInput');
					var filename = avatar.value;
					var fileExtension = filename.substring(filename.lastIndexOf('.') + 1).toLowerCase();
                    console.log(fileExtension);
                    if (!filename && fileExtension != 'jpg' && fileExtension != 'png' && fileExtension != 'jpeg' && fileExtension != 'gif') {
                        showInfo('请上传正确的图片格式', '180px',2);
                        return false;
                    }
				});

			},	
			bindEvent : function(){
				var uploadModal = template.compile(uploadFile)({
					title : '上传诊所图标',
					url : uploadUrl,
				});
				$('#crop-avatar').append(uploadModal);
				var checkType = $('#spot-addselected input:checked').val();
				if(checkType == 1){
					$('.btn-form').addClass('btn-background');
					$('.btn-form').removeClass('btn-disabled-color');
					$(".btn-form").attr('disabled',false);
				}
				$('body').unbind("click").on('click','#spot-addselected label',function (e){
					var ev = e || window.event;
					var elm = ev.target || ev.srcElement;
					if (elm.tagName === 'LABEL') {
						return;
					}
					var ev = e || window.event;
					var elm = ev.target || ev.srcElement;
					if (elm.tagName === 'SPAN') {
						return;
					}
					checkType = $('#spot-addselected input:checked').val();

					if(checkType == 1){
						$('.btn-form').addClass('btn-background');
						$('.btn-form').removeClass('btn-disabled-color');
						$(".btn-form").attr('check','0');
						$(".btn-form").attr('disabled',false);
					}else{
						$('.btn-form').removeClass('btn-background');
						$('.btn-form').addClass('btn-disabled-color');
						$(".btn-form").attr('check','1');
						$(".btn-form").attr('disabled',true);
					}
				});

			}
		};
		return main;
	})