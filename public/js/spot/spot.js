/**
 * 
 */
define(function(require){
	
	
	var select2 = require('plugins/select2/select2.full.min');
	var main = {
			init : function(){
				this.bindEvent();
			},
			bindEvent : function(){
				
				$('.select2').select2();
				
			}
	};
	return main;
})