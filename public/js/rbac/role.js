
define(function(require){
	 // 引入模版引擎
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
			        $(':checkbox[name="RoleForm[parentName][]"]').click(function () {
			            $(':checkbox', $(this).closest('li')).prop('checked', this.checked);
			        });
					$(':checkbox[name="RoleForm[child][]"]').click(function () {
						
						 $(this).prop('checked', this.checked);
					});
					$('#backid').click(function(){
							window.location.href="index.html";
					 });
			   		
			},
		
	}
	return main;
})