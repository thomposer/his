/**
 * 
 */

define(function(require){
	var bootstrap = require('js/bootstrap/bootstrap.min');
	var icheck = require('plugins/iCheck/icheck.min');
	var main = {
			init : function(){
				this.bindEvent();
			},
			bindEvent : function(){
				 $('input:checkbox').iCheck({
				      checkboxClass: 'icheckbox_square-blue',
				      radioClass: 'iradio_square-blue',
				      increaseArea: '20%' // optional
				    });

			}
	};
	
	return main;
})