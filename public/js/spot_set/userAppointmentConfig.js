define(function (require) {
    var common = require('js/lib/common');
    var _self;
    var main = {
        init: function () {
            _self = this;
            this.bindEvent();
        },
        bindEvent: function () {
            var oldStatus = $('input[name="UserSpot[status]"]:checked').val();
            $('body').on('click', '.confirm-config', function (e) {
                if(!_self.validateSubmit()){
                    return ;
                }
                var idArr = [];
                var remarkArr = [];

                var jsonFormCurr = $("form").serialize();
                var status = $('input[name="UserSpot[status]"]:checked').val();

                if ((oldStatus != status && status == 2) || ( oldStatus != status && status == 1)) {
                    var confirm_option = {
                        label: "确认保存",
                        className: 'btn-default btn-form'
                    };
                    var cancel_option = {
                        label: "放弃保存",
                        className: 'btn-cancel btn-form'
                    };
                    btns = {
                        cancel: cancel_option,
                        confirm: confirm_option
                    };
                    if (status == 2) {
                        $message = '医生未开放预约，用户将无法预约该医生的任何服务，并且第三方平台上会隐藏该医生，请确认';
                    } else {
                        $message = '医生开放预约，用户可预约该医生已开放的所有服务，并且第三方平台上会展示该医生，请确认';
                    }
                    bootbox.confirm(
                        {
                            message: $message,
                            title: '系统提示',
                            buttons: btns,
                            callback: function (confirmed) {
                                if (confirmed) {
                                    $('#appointment-config').submit();
                                } else {
                                    return true;
                                }
                            }
                        }
                    );
                } else {
                    $('#appointment-config').submit();
                }
            });
            
            $('body').off('click', '.select-type').on('click', '.select-type', function(){
                $('.type-help-block span').hide();
                if($(this).prop('checked')){
                    $(this).parents('.type-price').find('.doctor-service-type-price').attr('disabled',false);
                }else{
                    $(this).parents('.type-price').find('.error-tips').hide();
                    $(this).parents('.type-price').find('.doctor-service-type-price').val('');
                    $(this).parents('.type-price').find('.doctor-service-type-price').attr('disabled',true);
                }
            });
            
            $('body').off('input propertychange', '.doctor-service-type-price').on('input propertychange', '.doctor-service-type-price', function () {
                $(this).parents('.type-price-config').find('.error-tips').hide();
            });
            
            $('#appointment-config').yiiAjaxForm({
                beforeSend: function () {
                    return _self.validateSubmit()
                },
                complete: function () {


                },
                success: function (data) {
                    if(data['errorCode'] != 0){
                        showInfo(data['msg'], '180px', 2);
                    }
                },
            });
        },
        validatePrice: function (val, parentObj) {
            var re = /^\d+(\.\d{1,2})?$/;
            if (val == '') {
                parentObj.find('.error-tips').html('诊金不能为空');
                parentObj.find('.error-tips').show();
                return false;
            } else if (!re.test(val)) {
                parentObj.find('.error-tips').html('最多保留两位小数');
                parentObj.find('.error-tips').show();
                return false;
            } else if (val > 100000) {
                parentObj.find('.error-tips').html('诊金金额的值必须不大于100000');
                parentObj.find('.error-tips').show();
                return false;
            }
            return true;
        },
        validateSubmit: function () {
            var submitStatus = true;
            $('.type-price .doctor-service-type-price').each(function () {
                if ($(this).parents('.type-price').find('.select-type').prop('checked')) {
                    if (!_self.validatePrice($(this).val(), $(this).parents('.type-price'))) {
                        submitStatus = false;
                    }
                }
            });
            $('.simple-outpatient .doctor-service-type-price').each(function () {
                if (!_self.validatePrice($(this).val(), $(this).parents('.simple-outpatient'))) {
                    submitStatus = false;
                }
            });
            if ($('.select-type:checked').length == 0) {
                submitStatus = false;
                $('.type-help-block span').html('可提供服务不能为空');
                $('.type-help-block span').show();
            }
            return submitStatus;
        }
    };
    return main;
})