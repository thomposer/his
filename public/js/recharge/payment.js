/* 
 * time: 2017-3-21 18:11:56.
 * author : yu.li.
 */

define(function (require) {
    var common = require('js/lib/common');
    var qrcode = require('js/lib/jquery.qrcode.min');
    var main = {
        oderStatus: '',
        init: function () {
            _self = this;
            main.payment();
        },
        payment: function () {
            main.setPayType();
            main.setChangePrice();
            window.clearInterval(_self.oderStatus);
            _self.oderStatus = window.setInterval("_self.checkOderStatus(outTradeNo,price)", 3000);
            _self.clickPayType(type);
        },
        setPayType: function () {
            if (price == 0) {//若总金额为0时，优惠方式为 无，其他禁用
                $('#chargerecord-discount_type label:eq(1)').children('input').attr('disabled', true);
                $('#chargerecord-discount_type label:eq(2)').children('input').attr('disabled', true);
            }
            if (type == 1) {
                $('.field-cardflow-f_income').css({'display': 'block'});
//                var cashValue = $('#chargerecord-cash').val();
//                if (!isNaN(cashValue)) {
//                    var result = sub((Math.floor(cashValue * 100) / 100), $('#chargerecord-price').val());
//                    $('.cash').html('¥' + toDecimal2(result));
//                }
            }
            $(document).on('click', 'button[data-dismiss="modal"]', function () {
                window.clearInterval(_self.oderStatus);
            });
            $(document).on('click', '.pay-type', function (event) {
                var val = $(this).attr('type');
                $(this).addClass('active').siblings().removeClass('active');
                $('.active-type').val(val);
                $("input[name='CardFlow[scanMode]").val('2');
                if (val == 1) {
                    $('.field-order-card').hide();
                    $('.field-order-alipay').hide();
                    $('.field-order-wechat').hide();
                    $('.field-order-meituan').hide();
                    $('button[type="submit"]').show();
                    $('.field-cardflow-f_income').show();
                    $('.field-cardflow-f_remark').show();
                } else if (val == 2) {
                    $('.field-cardflow-f_income').hide();
                    $('.field-order-alipay').hide();
                    $('.field-order-wechat').hide();
                    $('.field-order-meituan').hide();
                    $('button[type="submit"]').show();
                    $('.field-order-card').show();
                    $('.field-cardflow-f_remark').show();
                } else if (val == 3) {
                    $('.field-cardflow-f_remark').hide();
                    $('.field-order-wechat').show();
                    $('.field-order-card').hide();
                    $('.field-order-meituan').hide();
                    $('.field-cardflow-f_income').hide();
                    $('.field-order-alipay').hide();
                    $('button[type="submit"]').show();
                    _self.changeScanMode(val, wechatUrl);
                } else if (val == 4) {
                    $('.field-cardflow-f_remark').hide();
                    $('.field-order-alipay').show();
                    $('.field-order-card').hide();
                    $('.field-order-meituan').hide();
                    $('.field-cardflow-f_income').hide();
                    $('.field-order-wechat').hide();
                    $('button[type="submit"]').show();
                    _self.changeScanMode(val, aliPayUrl);
                } else if (val == 5) {
                    $('.field-cardflow-f_income').hide();
                    $('.field-order-alipay').hide();
                    $('.field-order-card').hide();
                    $('.field-order-wechat').hide();
                    $('button[type="submit"]').show();
                    $('.field-order-meituan').show();
                    $('.field-cardflow-f_remark').show();
                }
            });
        },
        setChangePrice: function () {
            $('body').on('input propertychange', '#cardflow-f_income', function () {
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
        checkOderStatus: function (outTradeNo, price) {

            $.ajax({
                type: 'POST',
                url: apiRechargeCheckUrl,
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
            $('.pay-type[type=' + type + ']').click();
            $('body').off('input propertychange', '#cardflow-wechatauthcode').on('input propertychange', "#cardflow-wechatauthcode", function () {
                var val = $(this).val();
                if (val.length == 18) {
                    console.log(18);
                    $('.create-rebate').click();
                    return;
                }
            });
            $('body').off('input propertychange', "#cardflow-alipayauthcode").on('input propertychange', "#cardflow-alipayauthcode", function () {
                var val = $(this).val();
                if (val.length == 18) {
                    $('.create-rebate').click();
                    return;
                }
            });
        },
        changeScanMode: function (payType, codeUrl) {
            $('.scan-code[mode=1]').click();
            $("input[name='CardFlow[scanMode]").val('1');
            $("input[name='CardFlow[alipayAuthCode]']").focus();
            $("input[name='CardFlow[wechatAuthCode]']").focus();
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
                    $("input[name='CardFlow[scanMode]").val('2');
                } else {
                    $("input[name='CardFlow[scanMode]").val('1');
                    $('button[type="submit"]').show();
                }
                if (modeAarea == 'scan-mode-first') {
                    $('.scan-mode-second').hide();
                    $('.scan-mode-first').show();
                } else {
                    $('.scan-mode-second').show();
                    $('.scan-mode-first').hide();
                }
                if (mode == 1) {
                    $("input[name='CardFlow[alipayAuthCode]']").focus();
                    $("input[name='CardFlow[wechatAuthCode]']").focus();
                }
            });
        },
        jump: function () {
            window.location.reload();
        },
    }
    return main;
});



