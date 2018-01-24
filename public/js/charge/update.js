    
    define(function(require){
        var common = require('js/lib/common');
        var main = {
            init : function(){
                _self = this;
                
                this.bindEvent();
                                
            },  
            bindEvent : function(){
                _self.addPrice();
                $('input:checkbox').parent().hide();
                $('.btn-rebate').hide();
                $('.btn-print').show();
                $('.refund').click(function(){
                    var hasClass = $(this).hasClass('refund-color');
                    if(!hasClass){
                        $(this).children('img').attr({'src' : baseUrl + '/public/img/charge/refund.png'});
                        $(this).children('span').html('取消退费');
                        $(this).addClass('refund-color');
                    }else{
                        $(this).children('img').attr({'src' : baseUrl + '/public/img/charge/default_refund.png'});
                        $(this).children('span').html('退费');
                        $(this).removeClass('refund-color');
                    }
                    $('input:checkbox').parent().toggle();
                    $('.btn-rebate').toggle();
                    $('.btn-print').toggle();
                });

                $('.charge-again').click(function(){
                    var hasClass = $(this).hasClass('refund-color');
                    if(!hasClass){
                        $(this).children('img').attr({'src' : baseUrl + '/public/img/charge/refund.png'});
                        $(this).children('span').html('取消');
                        $(this).addClass('refund-color');
                    }else{
                        $(this).children('img').attr({'src' : baseUrl + '/public/img/charge/default_refund.png'});
                        $(this).children('span').html('重新收费');
                        $(this).removeClass('refund-color');
                    }
                    $('input:checkbox').parent().toggle();
                    $('.btn-again').toggle();
                });

                $('body').on('click', '.btn-print', function () {
                    var name = $(this).attr('name');
                    $('.'+name).jqprint();
                });

                
                $('.select-on-check-all').on('click',function(){
                    setTimeout('_self.addPrice()',100);
                });
                $('input:checkbox[name="selection[]"]').on('click',function(){
                    if($(this).attr('data-value') == 2){
                        showInfo('本项不可退费，如需操作请至【会员卡】手动登记退还','400px',2);
                        $(this).prop('checked',false);
                        return ;
                    }
                    var value = $(this).attr('checked');
                    var payType = $(this).attr('data-paytype');
                    var chargeRecordId = $(this).attr('data-chargerecordid'); 
                    if(action == 'update' && payType == 8){
	                    if (value == 'checked') {//若被选中了，则
	                    	$('input:checkbox[data-chargerecordid="'+chargeRecordId+'"]').attr('checked', true);
	                    } else {
	                    	$('input:checkbox[data-chargerecordid="'+chargeRecordId+'"]').removeAttr('checked');
	                    }
                    }
                    _self.addPrice();    
                });

                $(document).on('click', '[role="modal-update"]', function (event) {
                    event.preventDefault();
                    var data;
                    var total_price = 0;
                    // Collect all selected ID's
                    var selectedIds = [];
                    var receiptAmount = $('.receiptAmount').text();
                    var payType = [];
                    $('input:checkbox[name="selection[]"]').each(function () {
                        if (this.checked){
                            selectedIds.push($(this).val());
                            var price = parseFloat($(this).parent('td').siblings('td.total_price').text());
                            total_price +=  price;
                            payType.push($(this).attr('data-paytype'));
                        }
                    });
                    // alert(selectedIds);
                    if ((selectedIds.length == 0 || total_price == 0) && $.inArray('8',payType) == -1) {//0元项不允许退费
                        // If no selected ID's show warning
                        return false;
                    }
//                    else if(discountType != 1 && selectedIds != chargeId){
//                    	showInfo('有优惠的收费只能全额退款','200px',2);
//                    	return false;
//                    }
                    else if(receiptAmount == 0 && $.inArray('8',payType) == -1 ){
                    	return false;
                    }else{
                        $('#selectIds').val(selectedIds);
                        modal.open(this, $('#ModalRemoteConfirmForm').serialize());
                    }
                });

                $(document).on('click', '[role="modal-print"]', function (event) {
                    event.preventDefault();

                    modal.open(this, $('#ModalRemoteConfirmForm').serialize());

                });
                
                $('input:checkbox[name="selection[]"]').each(function(){
                    if($(this).attr('data-value') == 2 || $(this).attr('data-value') == 3){
                        $('input:checkbox[name="selection_all"]').attr('disabled','disabled');
                        return ;
                    }
                    var payType = $(this).attr('data-paytype');
                    var chargeRecordId = $(this).attr('data-chargerecordid');
                    if(payType == 8 && action == 'update'){//若为套餐卡支付
                    	$('input:checkbox[data-chargerecordid="'+chargeRecordId+'"]').not(":first").hide();
                    	$('input:checkbox[data-chargerecordid="'+chargeRecordId+'"]').parent('td').not(":last").css({"border-bottom-color":"white"});
                    }
                });
                
                
        },
        addPrice : function(){
                var total_price = 0;
                var payType = [];
                $('input:checkbox[name="selection[]"]').each(function () {
                    if (this.checked){
                       var price = parseFloat($(this).parent('td').siblings('td.total_price').text());
                       total_price +=  price;
                       payType.push($(this).attr('data-paytype'));
                   }
                });
                var receiptAmount = $('.receiptAmount').text();
                if((total_price == 0 || receiptAmount == 0) && $.inArray('8',payType) == -1){
                	$('.save-charge>.btn-again').removeClass('btn-default').addClass('btn-disabled disabled');
                    $('.save-charge>.btn-again').attr('disabled', true);
                }else{
                    $('.save-charge>.btn-rebate').removeClass('btn-disabled disabled').addClass('btn-default');
                    $('.save-charge>.btn-again').removeClass('btn-disabled disabled').addClass('btn-default');
                    $('.save-charge>.btn-again').attr('disabled', false);
                }
                           
        }
    }
    return main;
})