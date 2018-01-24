/**
 * 
 */
define(function(require){
	var template = require('template');
        var common = require('js/lib/common');
	var cropper = require('dist/cropper');
	var uploadFile = require('tpl/uploadModal.tpl');	
	var upload = require('upload/main');
	var appointment = require('tpl/appointment.tpl');
//	var cityPickerData = require('js/lib/city-picker.data');
//	var cityPicker = require('js/lib/city-picker');
	var main = {
			init : function(){
				var uploadModal = template.compile(uploadFile)({
					title : '上传头像',
					url : uploadUrl,
				});
				$('#crop-avatar').append(uploadModal);
				jsonFormInit = $("form").serialize();  //为了表单验证
			},
	};
	return main;
})



