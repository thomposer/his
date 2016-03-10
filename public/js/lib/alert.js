/**
 * 
 */

	define(function(require){
		var alertify = require('alertifyJs/build/alertify.min');
		var main = {
			init : function(){
				this.bindEvent();
			},	
			bindEvent : function(){
				alertify.defaults.transition = "slide";
				alertify.defaults.theme.ok = "btn btn-primary";
				alertify.defaults.theme.cancel = "btn btn-danger";
				alertify.defaults.theme.input = "form-control";	
				alertify.confirm()
        		.set({
        			title: title,
        			message: message
        		})
        		.set('onok', function() {
        			url ? window.history.back() : window.location.href=url;
        		}).show();			
			}
		};
		return main;
	})