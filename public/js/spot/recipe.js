

	define(function(require){
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
				$("#recipelist-medicine_description_id").on('change',function(){

					var medicineDescriptionId = $(this).val();
					if(medicineDescriptionId != ''){
						$('.review').attr({'disabled' : false,'data-url':itemUrl+'?id='+medicineDescriptionId});
						$('.review').removeClass('btn-cancel disabled').addClass('btn-default');
					}else{
						$('.review').attr({'disabled' : true,'data-url':''});
						$('.review').removeClass('btn-default').addClass('btn-cancel disabled');
					}

				}).change();
			}
		};
		return main;
	});