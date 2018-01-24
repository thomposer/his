
define(function (require) {
    var common = require('js/lib/common');
    var main = {
        init: function () {
            _self = this;
            this.initView($('#membershippackagecardflow-transaction_type').attr('data-value'));
            this.ableSubmit();
            this.bindEvent();
        },
        bindEvent: function () {
            $(document).off('change', '#membershippackagecardflow-transaction_type').on('change', '#membershippackagecardflow-transaction_type', function(){//选择框change事件  
                var val = $('#membershippackagecardflow-transaction_type').attr('data-value');
                if( (val == 0 && $(this).val() == 1) || (val == 1 && $(this).val() == 0) ){//0跟1任意切换  不做任何操作
                    $('#membershippackagecardflow-transaction_type').attr('data-value',($(this).val() ? $(this).val() : '0'));
                    _self.ableSubmit();
                }else{
                    $('#membershippackagecardflow-transaction_type').attr('data-value',$(this).val());
                    _self.initPackageFrom($(this).val());
                    _self.ableSubmit();
                }
            });
            
            $(document).off('input propertychange', '#membershippackagecardflow-flow_item,#membershippackagecardflow-remark').on('input propertychange', '#membershippackagecardflow-flow_item,#membershippackagecardflow-remark', function () {//交易项，备注输入事件
                _self.ableSubmit();
            });
            
            $(document).off('blur', '.package-card-service-input').on('blur', '.package-card-service-input', function () {
                if ($(this).parents('.package-card-service-item').find('input[type=checkbox]').prop('checked')) {
                    _self.validateSingle($(this),$('#membershippackagecardflow-transaction_type').val());//验证
                }
                _self.ableSubmit();
            });

            $(document).off('input propertychange', '.package-card-service-input').on('input propertychange', '.package-card-service-input', function () {//input输入事件  propertychange兼容ie9.0以下版本
                if ($(this).parents('.package-card-service-item').find('input[type=checkbox]').prop('checked')) {
                    _self.validateSingle($(this),$('#membershippackagecardflow-transaction_type').val());//验证
                }
                _self.ableSubmit();
            });

            $(document).off('click', '.package-card-form input[type=checkbox]').on('click', '.package-card-form input[type=checkbox]', function () {//复选框
                if ($(this).prop('checked')) {
                    $(this).parents('.package-card-service-item').find('.package-card-service-input').attr('disabled', false);
                } else {
                    $(this).parents('.package-card-service-item').find('.package-card-service-input').removeClass('package-card-service-time-check-input-error');
                    $(this).parents('.package-card-service-list').find('.help-block').html('');
                    $(this).parents('.package-card-service-item').find('.package-card-service-input').val('');
                    $(this).parents('.package-card-service-item').find('.package-card-service-input').attr('disabled', true);
                }
                _self.ableSubmit();
            });
        },
        validateSingle: function (obj,type) {//传入input对象
            if(type == 3){//消费退还
                var re = /^[1-9]+[0-9]*]*$/;
                var time = obj.parents('.package-card-service-item').find('.package-card-service-time').html();
                if (obj.val() == '') {
                    obj.addClass('package-card-service-time-check-input-error');
                    obj.parents('.package-card-service-list').find('.help-block').html('增加次数不能为空');
                    return false;
                } else if (!re.test(obj.val())) {
                    obj.addClass('package-card-service-time-check-input-error');
                    obj.parents('.package-card-service-list').find('.help-block').html('增加次数为1-999');
                    return false;
                } else if (obj.val() < 1 || obj.val() > 999) {
                    obj.addClass('package-card-service-time-check-input-error');
                    obj.parents('.package-card-service-list').find('.help-block').html('增加次数为1-999');
                    return false;
                }
                obj.removeClass('package-card-service-time-check-input-error');
                obj.parents('.package-card-service-list').find('.help-block').html('');
                return true;         
            }else{//消费
                var re = /^[1-9]+[0-9]*]*$/;
                var time = obj.parents('.package-card-service-item').find('.package-card-service-time').html();
                var msg = '扣减次数为1-' + time + '次';
                if (obj.val() == '') {
                    obj.addClass('package-card-service-time-check-input-error');
                    obj.parents('.package-card-service-list').find('.help-block').html('扣减次数不能为空');
                    return false;
                } else if (!re.test(obj.val())) {
                    obj.addClass('package-card-service-time-check-input-error');
                    obj.parents('.package-card-service-list').find('.help-block').html(msg);
                    return false;
                } else if (obj.val() < 1 || obj.val() > parseInt(time)) {
                    obj.addClass('package-card-service-time-check-input-error');
                    obj.parents('.package-card-service-list').find('.help-block').html(msg);
                    return false;
                }
                obj.removeClass('package-card-service-time-check-input-error');
                obj.parents('.package-card-service-list').find('.help-block').html('');
                return true;
            }
        },
        ableSubmit: function () {//验证是否可以提交
            $('.package-card-content .package-card-service-input').each(function () {
                if ($(this).parents('.package-card-service-item').find('input[type=checkbox]').prop('checked')) {
                    if ($(this).val() == '' || $(this).hasClass('package-card-service-time-check-input-error')) {
                        $('.card-check-btn').attr({'disabled': true});
                        return false;
                    }
                }
                $('.card-check-btn').attr({'disabled': false});
            });
            if (($('.package-card-form input[type=checkbox]:checked').length) == 0 || $('#membershippackagecardflow-flow_item').val() == '' 
                || $('#membershippackagecardflow-transaction_type').val() == 0  || $('#membershippackagecardflow-remark').val() == '') {
                $('.card-check-btn').attr({'disabled': true});
            }
        },
        initPackageFrom: function (type) {
            $('.package-card-content input[type=checkbox]').prop({'checked':false});//去掉复选框勾选
            $('.package-card-content .package-card-service-input').val('');//输入框清空
            $('.package-card-content .package-card-service-input').removeClass('package-card-service-time-check-input-error');//输入框错误提示
            $('.package-card-content .help-block').html('');//错误提示文案清空
            $('.package-card-content .package-card-service-input').attr('disabled', true);//输入框禁止
            if (type == 3) {//消费退还
                $('.package-card-content .package-card-service-title').removeClass('service-name-disabled');//样式
                $('.package-card-content input[type=checkbox]').attr('disabled', false);//复选框可选
                $('.package-card-content .add-deduct-title').html('增加');//文案
                $('.package-card-content .add-deduct').show();//显示输入框
            } else{//消费
                $('.package-card-content .add-deduct-title').html('扣减');//文案
                $('.package-card-content .package-card-service-list').each(function () {
                    var remainTime = $(this).find('.package-card-service-time').html();
                    if (parseInt(remainTime) == 0) {//剩余次数为0
                        $(this).find('.package-card-service-title').addClass('service-name-disabled');//样式
                        $(this).find('.package-card-service-title input[type=checkbox]').attr('disabled', true);//复选框禁止
                        $(this).find('.add-deduct').hide();//隐藏输入框
                    }
                });
            } 
        },
        initView: function (type) {
            if (type == 3) {//消费退还
                $('.package-card-content .add-deduct-title').html('增加');//文案
            } else{//消费
                $('.package-card-content .add-deduct-title').html('扣减');//文案
                $('.package-card-content .package-card-service-list').each(function () {
                    var remainTime = $(this).find('.package-card-service-time').html();
                    if (parseInt(remainTime) == 0) {//剩余次数为0
                        $(this).find('.package-card-service-title').addClass('service-name-disabled');//样式
                        $(this).find('.package-card-service-title input[type=checkbox]').attr('disabled', true);//复选框禁止
                        $(this).find('.add-deduct').hide();//隐藏输入框
                    }
                });
            }
            
            $('.package-card-content .package-card-service-input').each(function () {
                if($(this).parents('.package-card-service-item').find('input[type=checkbox]').prop('checked')){
                    _self.validateSingle($(this));
                }
            });
            
            
        },
    }
    return main;
})