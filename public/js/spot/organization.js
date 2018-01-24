

	define(function(require){
		//var cityPickerData = require('js/lib/city-picker.data');
		//var cityPicker = require('js/lib/city-picker');
		var select = require('plugins/select2/select2.full.min');
		var main = {
			init : function(){
				this.bindEvent();
			},	
			bindEvent : function(){
				$(".select2").select2({
					"language": {
						"noResults": function(){
							return "暂无查询结果";
						}
					},
				});
			}
		};
		return main;
	})