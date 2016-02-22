
define(function(require){
	 // 引入模版引擎
	var select = require('plugins/select2/select2.full.min');
	var main = {
			/**
			 * [init 初始化]
			 * @return {[type]} [description]
			 */
			init : function(){
				
				this.bindEvent();

			},
			/**
	         * 绑定事件方法
	         */

			bindEvent : function(){
				$(".select2").select2();
			},
		
	}
	return main;
})