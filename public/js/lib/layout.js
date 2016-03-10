/**
 * 
 */

define(function(require){
	//var jquery = require('jquery');
	var dist_app = require('dist/app.min');
	var main = {
			init : function(){
				this.bindEvent();
			},
			bindEvent : function(){
				
				$('li.dropdown-submenu ul').addClass('menu');
				$('.active').closest('li.treeview').addClass('active');
			}
	};
	
	return main;
})