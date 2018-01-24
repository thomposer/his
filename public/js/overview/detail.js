/**
 * 
 */

define(function(require){
	var main = {
			init : function(){
				this.bindEvent();
				
			},
			bindEvent : function(){
				
				$('body').on('click','.spot-go',function(){
					var id = $(this).attr('id');
					var csrfToken = $('meta[name="csrf-token"]').attr("content");
					// $.post(
					// 	selectSpotUrl,
					// 	{
					// 		'id' : id,
					// 		'_csrf' : csrfToken,
					// 	}
					// );
				$.ajax({
	                url: selectSpotUrl,
	                data: {
	                    'id' : id,
						'_csrf' : csrfToken
	                },
	                type: "post",
	                
            	});

			});
			
				
			}
			
	};
	
	return main;
})