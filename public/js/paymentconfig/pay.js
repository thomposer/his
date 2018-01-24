define(function (require) {
    var common = require('js/lib/common');
    var _self;
    var main = {
        init: function () {
            _self = this;
            this.bindEvent();
        },
        bindEvent: function () {
            _self.generate();
        },
        generate: function () {
 
        	$('body').off('click', '.pay-view').on('click', '.pay-view', function (e) {
                var formInfo = $('#pay-view').serialize();
                var appid = $('#paymentconfig-appid').val();
                var mchid = $('#paymentconfig-mchid').val();
                var payment_key = $('#paymentconfig-payment_key').val();
                console.log(formInfo);
                if (!appid || !mchid || !payment_key) {
                    showInfo('请填写完整相关信息', '180px', 2);
                    return false;
                }
                console.log(_self.check(appid));
                console.log(_self.check(mchid));
                console.log(_self.check(payment_key));
                if(_self.check(appid) || _self.check(mchid) || _self.check(payment_key)){
                    showInfo('请填写正确的支付配置', '180px', 2);
                    return false;
                }
                _self.getCode(this, appid,mchid,payment_key);
                return;
           });
            $('body').on('click','#btn-submit[type="button"]',function(){
                console.log(111);
                $('#pay-view .form-control').attr('readonly',false);
                $(this).html('保存');
                $(this).attr({'type' : 'button','class' : 'btn btn-form btn-disabled disabled','readonly':true});

            })
        },
        check : function(data){
　　         var reg = new RegExp("[\\u4E00-\\u9FFF]+","g");
            if(reg.test(data)){
                return true;
            }
            return false;
        },
        codeModal: function (obj,codeUrl) {
            modal.open(obj, 'codeUrl='+codeUrl+'&sendType=2');
        },
        getCode: function (obj, appid,mchid,payment_key) {
            var _csrf = $("[name='_csrf']").val();
            var url = $(obj).data('url');
            $.ajax({
                cache: false,
                type: "POST",
                url: url,
                data: {
                    sendType : 1,
                    appid : appid,
                    mchid : mchid,
                    payment_key : payment_key
                }, // 你的formid
                dataType: 'json',
                async: false,
                success: function (data, textStatus, jqXHR) {
                    var code_url = data.code_url;
                    if (code_url !== '' && code_url != null) {
                        $('#btn-submit').attr({'type':'submit','class' : 'btn btn-default btn-form','readonly' : false});
                        $('#pay-view .form-control').attr('readonly',true);
                        $('#btn-submit').html('保存');
                        _self.codeModal(obj, code_url);
                        return ;
                    } else {
                        $('#btn-submit').attr({'type' : 'button','class' : 'btn btn-form btn-disabled disabled','readonly' : true});
                        showInfo('内容填写错误，请核对', '180px', 2);
                        return false;
                    }

                },
                error: function () {
                    $('#btn-submit').attr({'type' : 'button','class' : 'btn btn-form btn-disabled disabled','readonly':true});
                    showInfo('内容填写错误，请核对', '180px',2);
                    return false;
                }
            });
//            return code_url;
        }

    };
    return main;
})