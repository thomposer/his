
define(function (require) {
    var common = require('js/lib/common');
    var template = require('template');
    var qrcode = require('js/lib/jquery.qrcode.min');
    var chargePrintTpl = require('tpl/charge/chargePrint.tpl');
    var migrate = require('js/lib/jquery-migrate-1.1.0');
    var jqprint = require('js/lib/jquery.jqprint-0.3');
    var status = 1; //可读状态
    var timeout = false; //启动及关闭按钮  
    var timeId;
    var generateCode;
    var once = 0;
    var originalPrice = 0;//默认打折扣
    var cardStatus = 0;//选择卡是否成功，默认失败，成功为1
    var confirm = true;

//    var checkOderStatus;
    var main = {
        oderStatus: '',
        init: function () {
            _self = this;
            this.bindEvent();
            main.setTime();
        },
        bindEvent: function () {
            $('.discount').hide();
            var discount_type = $('#chargerecord-discount_type input:checked').val();
            if (discount_type != 1) {
                _self.showType(discount_type);
            }
            if (allPrice == 0) {//若总金额为0时，优惠方式为 无，其他禁用
                $('#chargerecord-discount_type label:eq(1)').children('input').attr('disabled', true);
                $('#chargerecord-discount_type label:eq(2)').children('input').attr('disabled', true);
            }
            if (type == 1) {
                $('.field-chargerecord-cash').css({'display': 'block'});
                var cashValue = $('#chargerecord-cash').val();
                if (!isNaN(cashValue)) {
                    if (cashValue == "") {
                        cashValue = 0;
                    }
                    var result = sub(toDecimal2(cashValue), $('#chargerecord-price').val());
                    $('.cash').html('¥' + toDecimal2(result));
                }
            }
            if (readonly == 1) {
                $('#chargerecord-discount_price').attr({'readonly': true});
                $('#chargerecord-discount_reason').attr({'readonly': true});
                $('.discount-form').html('修改');
            }
            $(document).on('click','button[data-dismiss="modal"]',function(){
                window.clearInterval(_self.oderStatus);
            });
            $(document).off('click', '.pay-type').on('click', '.pay-type', function (event) {
                var pay_type = $(this).attr('type');
                var mode = $('.scan-code.active').attr('mode');
                $('#chargerecord-cash').val('');
                $('#chargerecord-wechatauthcode').val('');
                $('#chargerecord-alipayauthcode').val('');
                if (_self.buttonText == '') {
                    _self.buttonText = $('button[type="submit"]').html();
                }
                var val = pay_type;
                $(this).addClass('active').siblings().removeClass('active');
                $('.active-type').val(val);
                if (val == 1) {
                    $('#card-total-price').val('0.00');
                    $('input[name="ChargeRecord[originalPrice]"]').prop({'checked': false});
                    $('.field-chargerecord-card').hide();
                    $('.field-chargerecord-alipay').hide();
                    $('.field-chargerecord-wechat').hide();
                    $('.recharge-card-pay').hide();
                    $('button[type="submit"]').show();
                    $('.field-chargerecord-cash').show();
                    $('button[type="submit"]').attr({'disabled': false});
                    $('.cost-bg').css({'background': 'url(' + baseUrl + '/public/img/charge/cost_bg.png)', 'background-color': '#55657d'});
                    $('.field-chargerecord-meituan').hide();
                } else if (val == 2) {
                    $('#card-total-price').val(0);
                    $('input[name="ChargeRecord[originalPrice]"]').prop({'checked': false});
                    $('.field-chargerecord-cash').hide();
                    $('.field-chargerecord-alipay').hide();
                    $('.field-chargerecord-wechat').hide();
                    $('.recharge-card-pay').hide();
                    $('button[type="submit"]').show();
                    $('.field-chargerecord-card').show();
                    $('button[type="submit"]').attr({'disabled': false});
                    $('.cost-bg').css({'background-image': 'url(' + baseUrl + '/public/img/charge/cost_bg_pree.png)', 'background-color': 'rgb(120,167,237)'});
                    $('.field-chargerecord-meituan').hide();
                } else if (val == 3) {
                    $('#card-total-price').val(0);
                    $('input[name="ChargeRecord[originalPrice]"]').prop({'checked': false});
                    $('.field-chargerecord-wechat').show();
                    $('.field-chargerecord-card').hide();
                    $('.field-chargerecord-cash').hide();
                    $('.field-chargerecord-alipay').hide();
                    $('.recharge-card-pay').hide();
                    $('button[type="submit"]').attr({'disabled': false});
//                    $('button[type="submit"]').hide();
//                    $('button[type="submit"]').remove();
                    $('.cost-bg').css({'background': 'url(' + baseUrl + '/public/img/charge/cost_bg.png)', 'background-color': '#55657d'});
                    $('.field-chargerecord-meituan').hide();
                } else if (val == 4) {
                    $('#card-total-price').val(0);
                    $('input[name="ChargeRecord[originalPrice]"]').prop({'checked': false});
                    $('.field-chargerecord-alipay').show();
                    $('.field-chargerecord-card').hide();
                    $('.field-chargerecord-cash').hide();
                    $('.field-chargerecord-wechat').hide();
                    $('.recharge-card-pay').hide();
                    $('button[type="submit"]').attr({'disabled': false});
//                    $('button[type="submit"]').hide();
//                    $('button[type="submit"]').remove();
                    $('.cost-bg').css({'background': 'url(' + baseUrl + '/public/img/charge/cost_bg.png)', 'background-color': '#55657d'});
                    $('.field-chargerecord-meituan').hide();
                } else if (val == 5) {
                    originalPrice = 0;
                    $('input[name="ChargeRecord[originalPrice]"]').attr({'disabled': false});
                    $('input[name="ChargeRecord[firstDiagnosisFree]"').attr({'disabled': true});
                    $('.field-chargerecord-card').hide();
                    $('.field-chargerecord-cash').hide();
                    $('.field-chargerecord-wechat').hide();
                    $('.field-chargerecord-alipay').hide();
                    $('.recharge-card-pay').show();
                    $('button[type="submit"]').show();
                    $('input[name="recharge-card"]:checked').prop({'checked': false});
                    // $('.recharge-card input[name="ChargeRecord[cardType]"]:checked').prop({'checked': false});
                    $('input[name="ChargeRecord[originalPrice]"]').attr({'disabled': true});
                    $('button[type="submit"]').attr({'disabled': true});
                    $('.recharge-checkbox-border').hide();    
                    $('.recharge-card-solid').hide();
                    $('.recharge-total-price').hide();
                    $('.service-card-tips').hide();
                    $('.cost-bg').css({'background': 'url(' + baseUrl + '/public/img/charge/cost_bg.png)', 'background-color': '#55657d'});
                    $('.field-chargerecord-meituan').hide();
                }else if(val == 9){
                    $('.field-chargerecord-meituan').show();
                    $('#card-total-price').val(0);
                    $('input[name="ChargeRecord[originalPrice]"]').prop({'checked': false});
                    $('.field-chargerecord-cash').hide();
                    $('.field-chargerecord-alipay').hide();
                    $('.field-chargerecord-wechat').hide();
                    $('.recharge-card-pay').hide();
                    $('button[type="submit"]').show();
                    $('.field-chargerecord-card').hide();
                    $('button[type="submit"]').attr({'disabled': false});
                }
                
                if(val != 5){
                    $('input[name="package-card"]').prop({'checked': false});
                    $('input[name="recharge-card"]').prop({'checked': false});
                    $('input[name="service-card"]').prop({'checked': false});
                    $('.card-content-list').hide();
                    $('.recharge-card-total-price').hide();
                    $('.service-card-tips').hide();
                }
                if (pay_type == 3) {
                    if (mode == 1) {
                        $("input[name='ChargeRecord[wechatAuthCode]']").focus();
                    }

                    _self.changeScanMode(pay_type, wechatUrl);
                    clearTimeout(generateCode);
                    generateCode = setTimeout(_self.generateNewCode, 1000);

                } else if (pay_type == 4) {
                    if (mode == 1) {
                        $("input[name='ChargeRecord[alipayAuthCode]']").focus();
                    }
                    _self.changeScanMode(pay_type, aliPayUrl);
                    clearTimeout(generateCode);
                    generateCode = setTimeout(_self.generateNewCode, 1000);
                }


            });
            $(document).off('change', 'input[name="ChargeRecord[discount_type]"]').on('change', 'input[name="ChargeRecord[discount_type]"]', function (event) {
                var discount_type = $('#chargerecord-discount_type input:checked').val();
                _self.showType(discount_type);
                _self.generateCode(allPrice, '1', '');
                $('#chargerecord-discount_price').val('');
                $('#chargerecord-discount_reason').val('');
                $('#chargerecord-discount_price').attr({'readonly': false});
                $('#chargerecord-discount_reason').attr({'readonly': false});
                $(".discount-form").html('保存');
                $('#chargerecord-readonly').val(0);
            });
            $('body').off('click').on('click', '.discount-form', function () {

                var type = $('#chargerecord-discount_type input:checked').val();
                if (type == 1) {
                    return;
                }
                var discount_price = $('#chargerecord-discount_price').val();
                var discount_reason = $('#chargerecord-discount_reason').val();
                var reg = /^([0-9][0-9]*)+(.[0-9]{1,2})?$/;
                if (discount_price == '') {
                    showInfo('优惠金额不能为空。', '180px', 2);
                    return;
                } else {
                    $('.field-chargerecord-discount_price').removeClass('has-error');
                    $('.field-chargerecord-discount_price .help-block').html('');
                }
                if (discount_reason == '') {
                    showInfo('优惠原因不能为空。', '180px', 2);
                    return;
                } else {
                    $('.field-chargerecord-discount_reason').removeClass('has-error');
                    $('.field-chargerecord-discount_reason .help-block').html('');
                }

                var hasReadon = $('#chargerecord-discount_price').attr('readonly');
                if (hasReadon == false || typeof (hasReadon) == "undefined") {

                    var discount_price = $('#chargerecord-discount_price').val();
                    _self.generateCode(discount_price, type, discount_reason);
                } else {
                    $('#chargerecord-discount_price').attr({'readonly': false});
                    $('#chargerecord-discount_reason').attr({'readonly': false});
                    $(this).html('保存');
                    $('#chargerecord-readonly').val('0.00');
                }
            });
            _self.printInfo();
            $('#chargerecord-cash').change(function () {
                var length = $(this).val().length;
                if (length > 10) {
                    $(this).val($(this).val().substr(0, 10));
                }
            });
            _self.clickPayType(type);
            _self.clickRechargeCard();//计算充值卡支付金额
        },
        generateCode: function (discount_price, type, discountReason) {
            var id = getUrlParam('id');
            var pks = $('#selectIds').val();
            var radioType = $('#chargerecord-type').find('input:checked').attr('value');
            if (!id || !pks) {
                showInfo('操作异常，请稍后再试', '180px', 2);
                return;
            }

            $.ajax({
                type: 'POST',
                url: generateCodeUrl,
                data: {
                    discountPrice: discount_price,
                    discountType: type,
                    discountReason: discountReason,
                    pks: pks,
                    recordId: id,
                    username: info['username'],
                    patient_id: info['patient_id']
                },
                cache: false,
                success: function (json) {
                    if (json.errorCode == 0) {
                        outTradeNo = json.outTradeNo;
                        price = json.price;
                        $('#alipayCode').html('');
                        $('#wechatPayCode').html('');
                        $('#chargerecord-out_trade_no').val(outTradeNo);
                        $('#chargerecord-price').val(price);
                        if (json.aliPayUrl != '') {
                            aliPayUrl = json.aliPayUrl;
                            $('#alipayCode').qrcode({width: 200, height: 200, text: json.aliPayUrl});
                            $('#alipayCode').attr('aliPayUrl', aliPayUrl);
                        }
                        if (json.wechatUrl != '') {
                            wechatUrl = json.wechatUrl;
                            $('#wechatPayCode').qrcode({width: 200, height: 200, text: json.wechatUrl});
                            $('#wechatPayCode').attr('wechatUrl', wechatUrl);
                        }
                        window.clearInterval(_self.oderStatus);
                        if (price != 0) {//若支付金额为0.则无需轮询订单状态
                            $('.charge-type-total').show(); //支付方式显示
                            $('.alipay').html('￥' + price);
                            var result = sub(toDecimal2($('#chargerecord-cash').val()), price);
                            $('.cash').html('￥' + toDecimal2(result));
                            if (3 == radioType || 4 == radioType) {//支付宝或者微信支付隐藏确认收费按钮
                                $('button[type="submit"]').hide();
                            }
                        } else {
                            $('.charge-type-total').hide(); //支付方式以下全部隐藏
                            $('button[type="submit"]').show(); //显示确认收费按钮
                        }
                        if (type != 1) {
                            $('#chargerecord-discount_price').attr({'readonly': true});
                            $('#chargerecord-discount_reason').attr({'readonly': true});
                            $('.discount-form').html('修改');
                            $('#chargerecord-readonly').val(1);
                        }
                    } else {
                        showInfo(json.msg, '280px', 2);
                    }
                },
                error: function () {
                    showInfo('系统异常,请稍后再试', '200px', 2);
                },
                dataType: 'json',
            });
        },
        showType: function (discount_type) {
            if (discount_type == 2) {
                $('.discount').show();
                $('.label-discount_price').text('金额(¥)');
                $('#chargerecord-discount_price').attr({'placeholder': '请输入优惠的金额(¥)'});
            } else if (discount_type == 3) {
                $('.discount').show();
                $('.label-discount_price').text('折扣(%)');
                $('#chargerecord-discount_price').attr({'placeholder': '请输入优惠的折扣(%)'});
            } else {
                $('.discount').hide();
            }
        },
        checkOderStatus: function (outTradeNo, price) {
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
                        showInfo('支付成功', '180px');
                        $('.charge-suc').html('<img class="charge_suc_img" src=' + baseUrl + '/public/img/charge_success.png />');
                        var chargeLogId = json.data["charge_log_id"];
                        setTimeout(function () {
                            main.jump(chargeLogId);
                        }, 3000);
//            	    		window.location.href = returnUrl;  
                    } else if (json.errorCode != 1003) {
                        showInfo(json.msg, '200px', 2);
                        setTimeout(function () {
                            window.location.reload();//刷新页面
                        }, 3000);
                    }
                },
                error: function () {

                },
                dataType: 'json',
            });
        },
        jump: function (chargeLogId) {
//            $('.btn-cancel').click();
//            showInfo('支付成功');
            window.location.href = returnUrl + "?id=" + chargeLogId;
        },
        printInfo: function () {
            $('body').on('click', '.print-info', function () {
                var outTradeNo = $('#chargerecord-out_trade_no').val();
                var id = getUrlParam('id');
                var pks = $('#selectIds').val();
                var payType = $('#chargerecord-type').find('.active').attr('type');
                var cardId  = '';
                var firstDiagnosisFree = '';
                if (cardStatus == 1 && payType == 5) {
                    if($('input[name="ChargeRecord[cardType]"]').val()){//必须选卡，首单免诊金才生效
                        firstDiagnosisFree = $('#charge-first-free').is(':checked') ? 1 : '';//选择了首单免诊金
                    }

                    if (!$('input[name="ChargeRecord[originalPrice]"]:checked').val()) {//折扣
                        cardId = $('input[name="ChargeRecord[cardType]"]').val();//传cardId
                    }
                }
                
                
                $.ajax({
                    type: 'POST',
                    url: chargePrintDataUrl,
                    data: {
                        outTradeNo: outTradeNo,
                        pks: pks,
                        id: id,
                        cardId: cardId,
                        firstDiagnosisFree: firstDiagnosisFree
                    },
                    cache: false,
                    success: function (json) {
                        console.log(json.data.spotConfig);
//                        return ;
                        var aliPayUrl = $('#alipayCode').attr('aliPayUrl');
                        var wechatUrl = $('#wechatPayCode').attr('wechatUrl');
                        var radioType = $('#chargerecord-type').find('.active').attr('type');
                        var chargerecordDiscountPrice = $('#chargerecord-discount_price').val();
                        var chargerecordType = $('#chargerecord-discount_type').find('input:checked').attr('value');
                        var shouldPayPrice = $('#chargerecord-price').val();
                        var logo_img = '';
                        if(json.data.spotConfig.logo_shape == 1){
                            logo_img = "clinic-img"
                        }else{
                            logo_img = "clinic-img-long"
                        }
                        if (json.errorCode == 0) {
                            var chargePrint = template.compile(chargePrintTpl)({
                                orderLogList: json.data.orderLogList,
                                materialList: json.data.materialList,
                                chargeRecordLogList: json.data.chargeRecordLog,
                                packageRecord: json.data.packageRecord,
                                baseUrl: baseUrl,
                                soptInfo: json.data.spotInfo,
//                                userInfo: json.userInfo,
                                outTradeNo: outTradeNo,
//                                inspectReportDataProvider: json.chargeInfo,
//                                chargeType: json.chargeType,
//                                doctorName: json.doctorName,
//                                discount: json.discount,
                                cdnHost: cdnHost,
                                spotConfig : json.data.spotConfig,
                                logo_img: logo_img,
//                                printWay: json.printWay,
//                                typeList: json.typeList,
                            });
                            $('#print-charge-preview').html(chargePrint);
                            //包一层try catch，【容错】以免不传参数url报错

                            try {
                                //由于打印不能打印canvas，所以将canvas转为base64的图片
                                if (radioType == 3) {
                                    var url = wechatUrl;
                                    var noticeWords = "“<b>微信</b>”支付";
                                    var canvas = $("#wechatPayCode").find('canvas')[0];
                                } else if (radioType == 4) {
                                    var url = aliPayUrl;
                                    var noticeWords = "“<b>支付宝</b>”支付";
                                    var canvas = $("#alipayCode").find('canvas')[0];
                                }

                                $('#pay_code').qrcode({width: 60, height: 60, text: url});
                                $('#pay_code').html(_self.convertCanvasToImage(canvas));
                                $('#notice_words').html(noticeWords);
                            } catch (error) {
                                $('.code-part').hide();
                            }
                            //折扣为0元时二维码隐藏

                            if (Number(shouldPayPrice) == 0) {
                                $('.code-part').hide();
                            }
                            $('.chargePrint' + outTradeNo).jqprint();
                        } else {
                            showInfo(json.msg, '220px', 2);
                        }
                    },
                    error: function () {

                    },
                    dataType: 'json',
                });
            })
        },
        //canvas转base64图片 带img标签 eg:<img src="XXX" />
        convertCanvasToImage: function (canvas) {
            //新Image对象，可以理解为DOM
            var image = new Image();
            // canvas.toDataURL 返回的是一串Base64编码的URL，当然,浏览器自己肯定支持
            // 指定格式 PNG
            image.src = canvas.toDataURL("image/png");
            return image;
        },
        changeScanMode: function (payType, codeUrl) {
            $('.scan-code[mode=1]').click();
            $('body').off('click', '.scan-code').on('click', '.scan-code', function () {
                $(this).addClass('active').siblings().removeClass('active');
                var mode = $(this).attr('mode');
                $('.active-scan-mode').val(mode);
                var modeAarea = mode == 1 ? 'scan-mode-first' : 'scan-mode-second';
                console.log('modeAarea:' + modeAarea);
                if (mode == 2) {
                    $('button[type="submit"]').hide();
                } else {
                    $('button[type="submit"]').show();
                }
                $('.' + modeAarea).show().siblings('.scan-mode').hide();
                if (mode == 1) {
                    $("input[name='ChargeRecord[alipayAuthCode]']").focus();
                    $("input[name='ChargeRecord[wechatAuthCode]']").focus();
                }
            });
        },
        clickPayType: function (type) {
            console.log('.pay-type[type=' + type + ']', 'type');
            $('.pay-type[type=' + type + ']').click();
            $('body').off('input propertychange', '#chargerecord-wechatauthcode').on('input propertychange', "#chargerecord-wechatauthcode", function () {
                var val = $(this).val();
                if (val.length == 18) {
                    console.log(18);
                    $('.create-rebate').click();
                    return;
                }
            })
            $('body').off('input propertychange', "#chargerecord-alipayauthcode").on('input propertychange', "#chargerecord-alipayauthcode", function () {
                var val = $(this).val();
                if (val.length == 18) {
                    $('.create-rebate').click();
                    return;
                }
            })
        },
        clickRechargeCard: function () {
            var oldVal = '';
            //选择充值卡
            $(document).off('click', 'input[name="recharge-card"]').on('click', 'input[name="recharge-card"]', function (event) {
                $('.active-type').val(6);
                $('input[name="package-card"]').prop({'checked': false});
                $('input[name="service-card"]').prop({'checked': false});
                
                $('.card-content-list').hide();
                $('.recharge-checkbox-border').show(); 
                $('.recharge-card-solid').show();                    
                $('.recharge-card').show();
                $('.recharge-total-price').show();
                $('.recharge-card-total-price').hide();
                $('.service-card-tips').hide();
                var val = $('.recharge-card .recharge-card-div').length;
                if (val != 0) {//若有充值卡，默认选中第一张
                    $('.recharge-card .recharge-card-div').eq(0).click();
                    $('.recharge-card input[name="ChargeRecord[originalPrice]"]').attr({'disabled': false});//默认不折扣
                } else {//若没有充值卡，不允许提交
                    $('button[type="submit"]').attr({'disabled': true});
                }
            });
            $(document).off('click', 'ChargeRecord[originalPrice]').on('click', 'input[name="ChargeRecord[originalPrice]"]', function (event) {//选择是否使用折扣
                if (this.checked) { 
                    originalPrice = 1;
                } else {
                    originalPrice = 0;
                }
                var value = $('.recharge-card input[name="ChargeRecord[cardType]"]').val();
                main.requestCard(value);

            });
//            首单减免金额
            $(document).off('click', '#charge-first-free').on('click', '#charge-first-free', function (event) {
                var value = $('.recharge-card input[name="ChargeRecord[cardType]"]').val();
                main.requestCard(value);
            });
          
            //点击选择充值卡
            $(document).off('click', '.recharge-card .recharge-card-div').on('click', '.recharge-card .recharge-card-div', function (event) {
                var cardId = $(this).attr('data-id');
                $('.recharge-card input[name="ChargeRecord[cardType]"]').val(cardId);
                $('img.selected-recharge-card').remove();
                var img = '<img class="selected-recharge-card" src="'+baseUrl+'/public/img/charge/btn_select.png">';
                $(this).append(img);
                $('.recharge-card .recharge-card-div').removeClass('recharge-card-border-color');
                $(this).addClass('recharge-card-border-color');
                

                $('input[name="ChargeRecord[firstDiagnosisFree]"]').attr({'disabled': false});
                $('input[name="ChargeRecord[originalPrice]"]').attr({'disabled': false});
                var val = $('.recharge-card input[name="ChargeRecord[cardType]"]').val();
                main.requestCard(val);
            });

            //选择服务卡
            $(document).off('click', '.input[name="service-card"]').on('click', 'input[name="service-card"]', function (event) {
                $('.active-type').val(7);
                $('input[name="package-card"]').prop({'checked': false});
                $('input[name="recharge-card"]').prop({'checked': false});
                
                $('.service-card-tips').show();
                
                $('.card-content-list').hide();
                $('.service-card .service-card-service-input').val('');
                $('.service-card .service-card-service-input').removeClass('card-service-time-check-input-error');
                $('.service-card .help-block').html('');
                $('.service-card input[type=checkbox]').prop({'checked': false});
                $('.service-card .service-card-service-input').attr({'disabled':true});
                $('.service-card').show();
                $('button[type="submit"]').attr({'disabled': true});
                $('#card-total-price').val('0.00');
                
                // $('input[name="ChargeRecord[originalPrice]"]').attr({'disabled': true});
                // $('input[name="ChargeRecord[originalPrice]"]').prop({'checked': false});
                // $('input[name="ChargeRecord[firstDiagnosisFree]"]').attr({'disabled': true});
                // $('input[name="ChargeRecord[firstDiagnosisFree]"]').prop({'checked': false});
                $('.recharge-checkbox-border').hide();    
                $('.recharge-card-solid').hide();
                $('.recharge-total-price').hide();
                originalPrice = 0;
            });
            //选择套餐卡
            $(document).off('click', 'input[name="package-card"]').on('click', 'input[name="package-card"]', function (event) {
                $('.active-type').val(8);
                $('input[name="service-card"]').prop({'checked': false});
                $('input[name="recharge-card"]').prop({'checked': false});
                
                $('.recharge-card-total-price').hide();
                $('.service-card-tips').hide();
                $('.package-card .package-card-service-input').val('');
                $('.package-card .package-card-service-input').removeClass('card-service-time-check-input-error');
                $('.package-card .help-block').html('');
                $('.package-card input[type=checkbox]').prop({'checked': false});
                $('.package-card .package-card-service-input').attr({'disabled':true});
                $('.card-content-list').hide();
                $('.package-card').show();
                $('button[type="submit"]').attr({'disabled': true});
                //$('#card-total-price').val(0);
                // $('input[name="ChargeRecord[originalPrice]"]').attr({'disabled': true});
                // $('input[name="ChargeRecord[originalPrice]"]').prop({'checked': false});
                // $('input[name="ChargeRecord[firstDiagnosisFree]"]').attr({'disabled': true});
                // $('input[name="ChargeRecord[firstDiagnosisFree]"]').prop({'checked': false});
                $('.recharge-checkbox-border').hide();    
                $('.recharge-card-solid').hide();
                $('.recharge-total-price').hide();
                originalPrice = 0;
            });
            
            
            $(document).off('blur', '.package-card-service-input,.service-card-service-input').on('blur', '.package-card-service-input,.service-card-service-input', function(){
                if($(this).parents('.card-service-item').find('input[type=checkbox]').prop('checked')){
                    _self.validateSingle($(this));//验证
                }
                _self.ableSubmit($(this));
             });
             
             $(document).off('input propertychange', '.package-card-service-input,.service-card-service-input').on('input propertychange', '.package-card-service-input,.service-card-service-input', function(){//input输入事件  propertychange兼容ie9.0以下版本
                if($(this).parents('.card-service-item').find('input[type=checkbox]').prop('checked')){
                    _self.validateSingle($(this));//验证
                }
                _self.ableSubmit($(this));
             });
             
             $(document).off('click','.package-card input[type=checkbox],.service-card input[type=checkbox]').on('click','.package-card input[type=checkbox],.service-card input[type=checkbox]',function(){
                if($(this).prop('checked')){
                    $(this).parents('.card-service-item').find('.card-service-input').attr('disabled',false);
                }else{
                    $(this).parents('.card-service-item').find('.card-service-input').removeClass('card-service-time-check-input-error');
                    $(this).parents('.card-service-list').find('.help-block').html('');
                    $(this).parents('.card-service-item').find('.card-service-input').val('');
                    $(this).parents('.card-service-item').find('.card-service-input').attr('disabled',true);
                }
                _self.ableSubmit($(this));
             });
        },
        requestCard: function (val) {
            var pks = $('#selectIds').val();
            var firstDiagnosisFree = $('#charge-first-free').is(':checked') ? 1 : 2;
            $.ajax({
                type: 'POST',
                url: apiChargeGetCardDiscountPrice,
                data: {
                    id: val,
                    pks: pks,
                    iphone: info['iphone'],
                    patientId: info['patient_id'],
                    originalPrice: originalPrice,
                    firstDiagnosisFree: firstDiagnosisFree
                },
                cache: false,
                success: function (json) {
                    $('input[name="recharge-card"]').prop({'checked': true});
                    if (json.errorCode == 0) {
                        cardStatus = 1;
                        $('#recharge-total-price-span').html(json.price);
                        $('#discount-total-price-span').html(json.discountPrice);
                        $('#cardTotalPrice').val(json.price);
                        $('#cardInfo').val(json.chargeInfoArray);
                        $('button[type="submit"]').attr({'disabled': false});

                    } else {
                        cardStatus = 0;
                        $(this).attr({'checked': false});
                        $('button[type="submit"]').attr({'disabled': true});
                        $('#cardTotalPrice').val(json.allPrice);
                        if (json.errorCode == 1005) {
                            $('#recharge-total-price-span').html('0.00');
                            $('#discount-total-price-span').html('0.00');
                        }
                        showInfo(json.msg, '250px', 2);
                    }

                },
                error: function () {

                },
                dataType: 'json',
            });
        },
        generateNewCode: function () {
            var id = getUrlParam('id');
            var pks = $('#selectIds').val();
            var radioType = $('#chargerecord-type').find('input:checked').attr('value');
            if (!id || !pks) {
                showInfo('操作异常，请稍后再试', '180px', 2);
                return;
            }
            if (hasGenerateCode == 1) {//已经生成了二维码，无需重复生成
                return;
            }
            $.ajax({
                type: 'POST',
                url: apiChargeNewGenerateCodeUrl,
                data: {
                    pks: pks,
                    recordId: id,
                    username: info['username'],
                    patient_id: info['patient_id'],
                    outTradeNo: outTradeNo
                },
                cache: false,
                success: function (json) {
                    if (json.errorCode == 0) {
                        $('#alipayCode').html('');
                        $('#wechatPayCode').html('');
                        once = 1;
                        if (json.aliPayUrl != '') {
                            aliPayUrl = json.aliPayUrl;
                            $('#alipayCode').qrcode({width: 200, height: 200, text: json.aliPayUrl});
                            $('#alipayCode').attr('aliPayUrl', aliPayUrl);
                            hasGenerateCode = 1;
                        }
                        if (json.wechatUrl != '') {
                            wechatUrl = json.wechatUrl;
                            $('#wechatPayCode').qrcode({width: 200, height: 200, text: json.wechatUrl});
                            $('#wechatPayCode').attr('wechatUrl', wechatUrl);
                            hasGenerateCode = 1;
                        }
                        main.setTime();
                        if (3 == radioType || 4 == radioType) {//支付宝或者微信支付隐藏确认收费按钮
                            $('button[type="submit"]').hide();
                        }

                    } else {
                        showInfo(json.msg, '280px', 2);
                    }
                },
                error: function () {
                    showInfo('系统异常,请稍后再试', '200px', 2);
                },
                dataType: 'json',
            });
        },
        setTime: function () {
            if (timeout)
                return;
            _self.checkOderStatus(outTradeNo, price);
            clearTimeout(timeId);
            timeId = setTimeout(_self.setTime, 5000); //time是指本身,延时递归调用自己,100为间隔调用时间,单位毫秒  
        },
        validateSingle: function (obj) {//传入input对象
            var re = /^[1-9]+[0-9]*]*$/;
            var time = obj.parents('.card-service-item').find('.card-service-time').html();
            var msg = '扣减次数为1-' + time + '次';
            if(obj.val() == ''){
                obj.addClass('card-service-time-check-input-error');
                obj.parents('.card-service-list').find('.help-block').html('扣减次数不能为空');
                return false;
            }else if (!re.test(obj.val())) {
                obj.addClass('card-service-time-check-input-error');
                obj.parents('.card-service-list').find('.help-block').html(msg);
                return false;
            } else if (obj.val() < 1 || obj.val() > parseInt(time)) {
                obj.addClass('card-service-time-check-input-error');
                obj.parents('.card-service-list').find('.help-block').html(msg);
                return false;
            }
            obj.removeClass('card-service-time-check-input-error');
            obj.parents('.card-service-list').find('.help-block').html('');
            return true;
        },
        ableSubmit: function(obj){//验证是否可以提交
            obj.parents('.card-content-list').find('.card-service-input').each(function(){
                if($(this).parents('.card-service-item').find('input[type=checkbox]').prop('checked')){
                    if($(this).val() == '' || $(this).hasClass('card-service-time-check-input-error')){
                        $('button[type="submit"]').attr({'disabled': true});
                        return false;
                    }
                }
                $('button[type="submit"]').attr({'disabled': false});
            });
            if((obj.parents('.card-content-list').find('input[type=checkbox]:checked').length) == 0){
                $('button[type="submit"]').attr({'disabled': true});
            };
                
        },
    }
    return main;
})