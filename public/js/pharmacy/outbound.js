

define(function (require) {
    var template = require('template');
    var select = require('plugins/select2/select2.full.min');
    var inboundTpl = require('../../tpl/pharmacy/outbound.tpl.js?v=11');
    var common = require('js/lib/common');
    var _self;
    var main = {
        init: function () {
            _self = this;
            this.bindEvent();
        },
        bindEvent: function () {

            
            $('.select2').select2();
            $('body').on('change','#outboundinfo-recipename',function(e){
                    var val = $(this).val();
                    _self.addRecipe(val);
                    $(this).val('');
            });
            $('body').on('click','.outbound-info .op-group>img',function(){
                    $(this).parents('tr').hide();
                    $(this).siblings('input[name="OutboundInfo[deleted][]"]').val(1);
            });
            
            $('.empty').parents('tr').remove();
            var isCommitted = false;//表单是否已经提交标识，默认为false
            $('#outbound-form').yiiAjaxForm({
               beforeSend: function() {
                   if(isCommitted==false){
                       isCommitted = true;//提交表单后，将表单是否已经提交标识设置为true
                       return true;//返回true让表单正常提交
                   }else{
                       return false;//返回false那么表单将不提交
                   }
               },
               complete: function() {

               },
               success: function(data) {
                    
                    
                   if(data.errorCode == 0){
                        window.location.href = inboundIndexUrl;
                        
                   }else{
                        var $button = $(this).data('yiiActiveForm').submitObject;
                        if ($button) {
                            $button.prop('disabled', false);
                        }
                        isCommitted=false;
                        showInfo(data.msg,'200px',2);
                   }
               },
                error : function(){
                    isCommitted=false;
                }
            });
            $('body').on('change','.outboundinfo-batch_number',function(){
                var batchNumber = $(this).val();
                if(batchNumber == ''){
                    $(this).parent('td').siblings('td.default_price').html('');
                    $(this).parent('td').siblings('td.expire_time').html('');
                    $(this).parent('td').siblings('td.num').html('');
                    $(this).parents('tr').children('td.op-group').children('input.stock_info_id').val('');
                    return false;
                }
                var recipeId = $(this).siblings('input.recipe_id').val();
                var list = recipeList[recipeId]['batch_number'][batchNumber];
                $(this).parent('td').siblings('td.default_price').html(list.default_price);
                $(this).parent('td').siblings('td.expire_time').html(list.expire_time);
                $(this).parent('td').siblings('td.num').html(list.num);
                $(this).parents('tr').children('td.op-group').children('input.stock_info_id').val(list.id);    
                
                
            })
            

        },
        
        addRecipe : function(id){
        	var list = recipeList[id];
            var inboundModel = template.compile(inboundTpl)({
                    
                    list : list,
                    unit : unitList[list.unit],
                    baseUrl : baseUrl

                }); 
            $('.outbound-info tbody').append(inboundModel);   
         
        }
        

    };
    return main;
})