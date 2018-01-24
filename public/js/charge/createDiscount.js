
define(function (require) {
    var common = require('js/lib/common');
    var timeout = true;
    var main = {
        init: function () {
            _self = this;
            window.clearInterval();
            this.bindEvent();
        },
        bindEvent: function () {
            main.validateDiscount();
            main.autoPrice();
        },
        autoPrice: function () {
            $('.discount-text').bind('input propertychange', function () {
                var val = $(this).val();
                if(val==''){
                    val=100;
                }
                if(!isNum(val)){
                	return;
                }
                var totalPrice = $(this).parent('td').siblings('.totalPrice').text();

                if(parseFloat(val) >100 || parseFloat(val) < 0){
                    showInfo('折扣（%）需在0%-100%之间', '200px',2);
                    return false;
                }
                if(parseFloat(totalPrice) == 0){
                    return false;
                }
                var result = sub(totalPrice, toDecimal2(div(mul(toDecimal2(val), totalPrice), 100)));
                $(this).parent('td').siblings('td').children('input.discountPrice').val(toDecimal2(result));
                $(this).parent('td').siblings('td.discountPirceAfter').text(toDecimal2(sub(totalPrice, result)));

            });
            $('.discountPrice').bind('input propertychange', function () {
                var val = $(this).val();
                if(val==''){
                    val = 0;
                }
                var totalPrice = $(this).parent('td').siblings('.totalPrice').text();
                //if (isNaN(val)) {
                //    return false;
                //}
                console.log(val,'val = ');
                if(!isNum(val)){
                	return;
                }

                if(parseFloat(val) < 0){
                    showInfo('单项优惠金额不得为负', '180px',2);
                    return false;
                }
                if(parseFloat(val) > parseFloat(totalPrice)){
                    showInfo('单项优惠金额不得大于原价金额', '220px',2);
                    return false;
                }
                if(parseFloat(totalPrice) == 0){
                    return false;
                }
                var result = mul(div(sub(totalPrice,toDecimal2(val)), totalPrice), 100);

                $(this).parent('td').siblings('td').children('input.discount-text').val(toDecimal2(result));
                $(this).parent('td').siblings('td.discountPirceAfter').text(toDecimal2(sub(totalPrice, val)));

            });

        },
        validateDiscount: function () {
            var isCommitted = false;//表单是否已经提交标识，默认为false
            $('#create-discount-form').yiiAjaxForm({
                beforeSend: function () {

                    if (isCommitted == false) {
                        isCommitted = true;//提交表单后，将表单是否已经提交标识设置为true
                        return true;//返回true让表单正常提交
                    } else {
                        return false;//返回false那么表单将不提交
                    }
                },
                complete: function () {

                },
                success: function (data) {

                    if (data.errorCode == 0) {
                        showInfo('保存成功', '180px');
                        if (isCommitted == true) {
                            $('#ajaxCrudModal').modal('hide');
                            window.location.reload()
                        }
                    } else {
                        isCommitted = false;
                        showInfo(data.msg, '250px', 2);
                    }
                }
            });
        }

    };
    return main;
});