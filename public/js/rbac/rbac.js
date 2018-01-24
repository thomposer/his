
define(function(require){
	var $ = require('jquery');
	 // 引入模版引擎
	var template = require('template');
	var alertify = require('js/widget/alertify');
	var jqPaginator = require('js/jqPaginator/jqPaginator');
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
					e.preventDefault();
					
					var obj = alertify.confirm().set({'title':'删除','message' :'你确定要删除嘛？'}).set('onok',function(event){window.location.href=deleteUrl;}).show();
				})
				$.jqPaginator('#pagination2', {
			        
			        totalCounts:count,
			        pageSize:10,
			        visiblePages: 10,
			        currentPage: 1,
			        prev: '<li class="prev"><a href="javascript:;">Previous</a></li>',
			        next: '<li class="next"><a href="javascript:;">Next</a></li>',
			        page: '<li class="page"><a href="javascript:;">{{page}}</a></li>',
			        onPageChange: function (num, type) {
			        	 var con = "";
			             var  i = num*5;
			             var  k = count - i > 5? i+5 : count;
			             // for(var o in permission_data){
			             // 	$.extend(true, data, permission_data[o]);
			             	
			             // }
			            
			            // con =me.addPermissionPage(permission_data,i,k);
			                                                                                                           
               
			            // $(".table-bordered").append(
			            // 	con	
			            //         );
			        }
			    });
		
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