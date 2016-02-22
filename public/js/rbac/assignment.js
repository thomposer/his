
define(function(require){
	var $ = require('jquery');
	 // 引入模版引擎
	var template = require('template');
	var alertify = require('js/widget/alertify');
	//var jqPaginator = require('js/jqPaginator/jqPaginator');
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
				$("a.delete").on('click',function(e){
					
					var id = $(this).data('id');//用户id
					var rolelist = $(this).data('type');//角色
					
					var obj = alertify.confirm().set({'title':'删除','message' :'你确定要删除嘛？'})
					.set('onok',
							function(event){
								var csrfToken = $('meta[name="csrf-token"]').attr("content");
								$.post(deleteUrl, 
									{
										'_csrf' : csrfToken,
										'user_id': id,
										'rolelist' : rolelist,
										
									},
									function(data, textStatus, xhr) {
										alert(data);
										window.location.href = indexUrl;
								});
								
						}).show();
				})

		
			},
		//定义一个函数，用来读取特定的cookie值。
        getCookie : function(name){
            var arr = document.cookie.split("; ");
            for(var i=0,len=arr.length;i<len;i++){
                var item = arr[i].split("=");
                if(item[0]==name){
                    return item[1];
                }
            }
            return "";
        },
		addPermissionPage : function(permission,i,k){
					var con = "";
					 for(var y in permission){
					 	alert(y+':'+permission[y]);
					 }
					for(i;i < k; i++){
						
						con +=  "<tr>";
				        con +=  "<td>"+permission[i]['name']+"</td>";
				        con +=  "<td>"+permission[i]['description']+"</td>";
				        con +=  "<td>"+permission[i]['data']+"</td>";
				        con +=  "<td>";
				        con +=  "<a class='delete' href='"+updateUrl+"'></a>";      
				        con +=  "</td>";
				        con +=  "<td>";
				        con +=  "<a href='javascript:void(0)' class = 'delete'>删除</a>";        
				        con +=  "</td>";
				        con +=  "</tr>";
					}
					
					return con;
		}
	}
	return main;
})