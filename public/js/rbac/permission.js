
define(function(require){
	 // 引入模版引擎
	var select2 = require('plugins/select2/select2.full.min');
	
	var data = {};
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
				var me = this;
				//Initialize Select2 Elements
			    $(".select2").select2();
//				$("a.delete").on('click',function(e){
//					e.preventDefault();
//					var id = $(this).data('id');
//					var type = $(this).data('type');
//					var obj = alertify.confirm().set({'title':'删除','message' :'你确定要删除嘛？'}).set('onok',
//							function(event){
//								var csrfToken = $('meta[name="csrf-token"]').attr("content");
//								 $.post(deleteUrl,{
//									 '_csrf' : csrfToken,
//									 'id': id,
//									 
//								 },function(data, textStatus, xhr) {
//										alert(data);
//										window.location.href = indexUrl;
//								})
//							}).show();
//				});
				$('.dropdown-menu li').on('click',function(){
					
					var text = $(this).text();
					
					$(".btn-group button").text(text);
				})
		},
	}
	return main;
})