/**
 * 
 */

define(function(require){
	var template = require('template');
	var cropper = require('dist/cropper.min');
	var uploadFile = require('tpl/uploadModal.tpl');	
	var upload = require('upload/main');
	var main = {
			init : function(){
				var uploadModal = template.compile(uploadFile)({
					title : '上传头像',
					url : uploadUrl,
				});
				$('#crop-avatar').append(uploadModal);				
			}
	};
	return main;
})