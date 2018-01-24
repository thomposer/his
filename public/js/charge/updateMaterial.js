

define(function (require) {
    var template = require('template');
    var select = require('plugins/select2/select2.full.min');
    var createMaterialTpl = require('tpl/charge/createMaterial.tpl');
    var common = require('js/lib/common');
    var _self;
    var redirectUrl = '';
    var main = {
        init: function () {
            _self = this;
            this.bindEvent();
        },
        bindEvent: function () {

            
            $('.select2').select2();
            $('body').off('change', '#materialcharge-id').on('change','#materialcharge-id',function(e){
                    var val = $(this).val();
                    _self.addMaterial(val);
                    $(this).val('');
            });
            $('body').on('click','#createMaterial .op-group>img',function(){
                    $(this).parents('tr').hide();
                    $(this).siblings('input[name="MaterialCharge[deleted][]"]').val(1);
            });
			
            var isCommitted = false;//表单是否已经提交标识，默认为false
            $('#createMaterialForm').find(".btn-default").one('click',function(){
		        $('#createMaterialForm').yiiAjaxForm({
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

		            	  if(data.redirectUrl){
		            		  showInfo(data.msg,'350px');
		            		  redirectUrl = data.redirectUrl;
		            		  setTimeout(_self.redirect,1000);
		            	  }else{
		            		  showInfo(data.msg,'200px');
		            		  setTimeout(_self.redirect,1000);
		            	  }

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
            
            $('#createMaterial .empty').parent('td').remove();
        },
        addMaterial : function(id){
        	var list = materialList[id];
        	var totalNum = materialTotal[id]?materialTotal[id]:0;
        	var showValue = '';
            if (list["manufactor"] != '') {
                showValue += '生产商：' + htmlEncodeByRegExp(htmlEncodeByRegExp(list["manufactor"])) + '<br/>';
            }
            showValue += '零售价：' + list.price + '元';
            var createMaterialModel = template.compile(createMaterialTpl)({
                    
                    list : list,
                    baseUrl : baseUrl,
                    showValue : '',
                    totalNum : totalNum
                }); 
            $('#createMaterial tbody').append(createMaterialModel);   
         
        },
        redirect : function(){  
        	if(redirectUrl != ''){
      		  window.location.href = redirectUrl;
      	  }else{
      		  window.location.reload()
      	  }    
        }  
        

    };
    return main;
})