/* 
 * time: 2017-3-21 18:11:56.
 * author : yu.li.
 */

define(function (require) {
    var common = require('js/lib/common');
    var qrcode = require('js/lib/jquery.qrcode.min');
    var cardStatus = 0;//选择卡是否成功，默认失败，成功为1
    var originalPrice = 0;
    var main = {
    	oderStatus: '',
        init: function () {
            _self = this;
            main.payment();
        },
        payment: function () {
            main.setPayType();
            main.setChangePrice();
                
            _self.oderStatus = window.setInterval("_self.checkOderStatus(outTradeNo,price)", 3000);
            _self.clickPayType(type);
            //选用充值卡事件 
            $(document).off('click', '#recharge-card .recharge-card-div').on('click', '#recharge-card .recharge-card-div', function (event) {
                
                var cardId = $(this).attr('data-id');
                $('#recharge-card input[name="CardOrder[cardType]"]').val(cardId);
                $('img.selected-recharge-card').remove();
                var img = '<img class="selected-recharge-card" src="'+baseUrl+'/public/img/charge/btn_select.png">';
                $(this).append(img);
                $('#recharge-card .recharge-card-div').removeClass('recharge-card-border-color');
                $(this).addClass('recharge-card-border-color');
                // console.log(val);
               
                // $('input[name="CardOrder[originalPrice]"]').attr({'disabled': false});
                
                main.requestCard(cardId);

            });
            //不使用卡折扣
            $(document).off('click', 'CardOrder[originalPrice]').on('click', 'input[name="CardOrder[originalPrice]"]', function (event) {
                var val = $(this).val();
                if (this.checked) {
                    originalPrice = 1;
                } else {
                    originalPrice = 0;
                }
                var value = $('input[name="CardOrder[cardType]"]').val();
                main.requestCard(value);

            });
            $(document).on('click','button[data-dismiss="modal"]',function(){
                window.clearInterval(_self.oderStatus);
            });

        },
        setPayType: function () {
            
            $(document).on('click', '.pay-type', function (event) {
                var val = $(this).attr('type');
                $(this).addClass('active').siblings().removeClass('active');
                $('.active-type').val(val);
                $("input[name='CardOrder[scanMode]").val('2');
                main.showType(val);
            });
        },
        showType : function(val){
            if (val == 1) {
                    $('.field-order-card').hide();
                    $('.field-order-alipay').hide();
                    $('.field-order-wechat').hide();
                    $('.recharge-card-pay').hide();
                    $('button[type="submit"]').show();
                    $('.field-cardorder-income').css({'display':'block'});
                    $('.field-cardflow-f_income').css({'display':'block'});
                    $('.field-cardorder-remark').css({'display':'block'});
                    
                    $('button[type="submit"]').attr({'disabled': false});
                    $('.field-order-meituan').hide();
                } else if (val == 2) {
                    $('.field-cardorder-income').hide();
                    $('.field-cardflow-f_income').hide();
                    $('.field-order-alipay').hide();
                    $('.field-order-wechat').hide();
                    $('.recharge-card-pay').hide();
                    $('button[type="submit"]').show();
                    $('.field-order-card').show();
                    $('.field-cardorder-remark').show();
                    $('button[type="submit"]').attr({'disabled': false});
                    $('.field-order-meituan').hide();
                } else if (val == 3) {
                    $('.field-cardorder-remark').hide();
                    $('.field-order-wechat').show();
                    $('.field-order-card').hide();
                    $('.field-cardorder-income').hide();
                    $('.field-cardflow-f_income').hide();
                    $('.field-order-alipay').hide();
                    $('.recharge-card-pay').hide();
                    $('button[type="submit"]').attr({'disabled': false});
                    $('button[type="submit"]').show();
                    $('.field-order-meituan').hide();
                    _self.changeScanMode(val,wechatUrl);
                } else if (val == 4) {
                    $('.field-cardorder-remark').hide();
                    
                    $('.field-order-card').hide();
                    $('.field-cardflow-f_income').hide();
                    $('.field-cardorder-income').hide();
                    $('.field-order-wechat').hide();
                    $('.recharge-card-pay').hide();
                    $('button[type="submit"]').show();
                    $('.field-order-alipay').show();
                    $('button[type="submit"]').attr({'disabled': false});
                    $('.field-order-meituan').hide();
                    _self.changeScanMode(val,aliPayUrl);
                }else if (val == 5) {
                    $('.field-cardorder-remark').hide();
                    $('.field-order-alipay').hide();
                    $('.field-order-card').hide();
                    $('.field-cardflow-f_income').hide();
                    $('.field-cardorder-income').hide();
                    $('.field-order-wechat').hide();
                    $('button[type="submit"]').show();
                    $('.recharge-card-pay').show();
                    $('.recharge-card-total-amount').show();
                    $('button[type="submit"]').attr({'disabled': true});
                    $('.field-order-meituan').hide();
                    var val = $('#recharge-card .recharge-card-div').length;
                    if (val != 0) {//若有充值卡，默认选中第一张
                        $('#recharge-card .recharge-card-div').eq(0).click();
                        $('#recharge-card input[name="CardOrder[originalPrice]"]').attr({'disabled': false});//默认不折扣
                    } else {//若没有充值卡，不允许提交
                        $('button[type="submit"]').attr({'disabled': true});
                    }
                }else if(val == 6){
                    $('.field-cardorder-income').hide();
                    $('.field-cardflow-f_income').hide();
                    $('.field-order-alipay').hide();
                    $('.field-order-wechat').hide();
                    $('.recharge-card-pay').hide();
                    $('button[type="submit"]').show();
                    $('.field-order-card').hide();
                    $('.field-cardorder-remark').show();
                    $('button[type="submit"]').attr({'disabled': false});
                    $('.field-order-meituan').show();
                }
        },
        setChangePrice: function () {
            $('body').on('input propertychange', '#cardorder-income', function () {
                var val = $(this).val();
                if (isNaN(val)) {
                    return false;
                }
                val = Math.floor(val * 100) / 100;
                var total_price = price;
                var result = sub(val, total_price);
                $('.cash').html('¥' + toDecimal2(result));
            })
        },
        checkOderStatus: function (outTradeNo,price) {
        	
            $.ajax({
                type: 'POST',
                url: checkUrl,
                data: {
                    out_trade_no: outTradeNo,
                    total_amount: price
                },
                cache: false,
                success: function (json) {
                    if (json.errorCode == 0) {
                        window.clearInterval(_self.oderStatus);
                        showInfo('支付成功', '180px');
                        $('.charge-suc').html('<img class="charge_suc_img" src=' + baseUrl + '/public/img/charge_success.png />');
                        setTimeout(function () {
                            main.jump();
                        }, 3000);
//            	    		window.location.href = returnUrl;  
                    }
                },
                error: function () {

                },
                dataType: 'json',
            });
        },
        clickPayType: function (type) {
            console.log('.pay-type[type=' + type + ']', 'type');
            main.showType(type);
            $('body').off('input propertychange', '#cardorder-wechatauthcode').on('input propertychange', "#cardorder-wechatauthcode", function () {
                var val = $(this).val();
                if (val.length == 18) {
                    console.log(18);
                    $('.create-rebate').click();
                    return;
                }
            });
            $('body').off('input propertychange', "#cardorder-alipayauthcode").on('input propertychange', "#cardorder-alipayauthcode", function () {
                var val = $(this).val();
                if (val.length == 18) {
                    $('.create-rebate').click();
                    return;
                }
            });
        },
        changeScanMode: function (payType, codeUrl) {
            $('.scan-code[mode=1]').click();
            $("input[name='CardOrder[scanMode]").val('1');
            $("input[name='CardOrder[alipayAuthCode]']").focus();
            $("input[name='CardOrder[wechatAuthCode]']").focus();
            $('body').off('click', '.scan-code').on('click', '.scan-code', function () {
                $(this).addClass('active').siblings().removeClass('active');
                var mode = $(this).attr('mode');
                $('.active-scan-mode').val(mode);
                var modeAarea = mode == 1 ? 'scan-mode-first' : 'scan-mode-second';
                if (mode == 2) {
                    var codeArea = payType == 3 ? 'wechatPayCode' : 'alipayCode';
                    var attrUrl = payType == 3 ? 'wechatUrl' : 'aliPayUrl';
                    if (codeUrl != null && codeUrl != '') {
                        $('#' + codeArea).html('');
                        jQuery('#' + codeArea).qrcode({width: 200, height: 200, text: codeUrl});
                        $('#' + codeArea).attr(attrUrl, codeUrl);
                    } else {
                        showInfo('内容填写错误，请核对', '180px', 2);
                        return false;
                    }
                    $('.create-rebate').hide();
                    $("input[name='CardOrder[scanMode]").val('2');
                } else {
                    $("input[name='CardOrder[scanMode]").val('1');
                    $('button[type="submit"]').show();
                }
                if(modeAarea == 'scan-mode-first'){
                    $('.scan-mode-second').hide();
                    $('.scan-mode-first').show();
                }else{
                    $('.scan-mode-second').show();
                    $('.scan-mode-first').hide();
                }
                if (mode == 1) {
                    $("input[name='CardOrder[alipayAuthCode]']").focus();
                    $("input[name='CardOrder[wechatAuthCode]']").focus();
                }
            });
        },
        requestCard: function (val) {
            $.ajax({
                type: 'POST',
                url: apiRechargeGetCardDiscountPrice,
                data: {
                    id: val,
                    outTradeNo: outTradeNo,
                    originalPrice: originalPrice,
                },
                cache: false,
                success: function (json) {
                    if (json.errorCode == 0) {
                        cardStatus = 1;
                        $('#recharge-total-price-span').html(json.allPrice);
                        $('button[type="submit"]').attr({'disabled': false});

                    } else {
                        cardStatus = 0;
                        $(this).attr({'checked': false});
                        $('button[type="submit"]').attr({'disabled': true});
                        if (json.errorCode == 1005) {
                            $('#recharge-total-price-span').html(0);
                        }
                        showInfo(json.msg, '250px', 2);
                    }

                },
                error: function () {

                },
                dataType: 'json',
            });
        },
        jump: function () {
            window.location.reload();
        },
    }
    return main;
});



