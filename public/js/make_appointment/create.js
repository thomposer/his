

	define(function(require){
		var cityPickerData = require('js/lib/city-picker.data');
		var cityPicker = require('js/lib/city-picker');
		var main = {
			init : function(){
				$('.datepicker').css({'padding':'0px','display':'none'});
				$('#patient-birthday').on('click',function(){
					$('.datepicker').toggle();
				})
				
			},	
		};
		return main;
	})