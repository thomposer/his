

define(function (require) {
    var template = require('template');
    var select = require('plugins/select2/select2.full.min');
    var inboundTpl = require('tpl/pharmacy/inbound.tpl');
    var common = require('js/lib/common');
    var totalPriceMain = require('js/pharmacy/total-price');
    var _self;
    var main = {
        init: function () {
            _self = this;
            this.bindEvent();
        },
        bindEvent: function () {
            $('.select2').select2();
            totalPriceMain.init();
            $('body').on('change','#stockinfo-recipename',function(e){
                    var val = $(this).val();
                    _self.addRecipe(val);
                    $(this).val('');
            });
            $('body').on('click','.stock-info .op-group>img',function(){
                    $(this).parents('tr').hide();
                    $(this).siblings('input[name="StockInfo[deleted][]"]').val(1);
                    totalPriceMain.defaultTotal();
            });
            $('body').on('focus','.date .form-control',function(){
                $(this).datepicker({
                    format: 'yyyy-mm-dd',
                    language : 'zh-CN',
                    inline : false,
                    autoclose : true
                })

            });

            $('.empty').parents('tr').remove();
            var isCommitted = false;//表单是否已经提交标识，默认为false
            $('#inbound-form').find(".btn-default").one('click',function(){
		        $('#inbound-form').yiiAjaxForm({
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
		        });
            });
        },
        addRecipe : function(id){
        	var list = recipeList[id];
            var inboundModel = template.compile(inboundTpl)({
                    
                    list : list,
                    unit : unitList[list.unit],
                    baseUrl : baseUrl

                }); 
            $('.stock-info tbody').append(inboundModel);
            $('.stock-total-pre-num').html('');
        },

    };
    return main;
})