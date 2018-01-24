/* 
 * time: 2017-3-21 18:11:56.
 * author : yu.li.
 */

define(function (require) {
    var common = require('js/lib/common');
    var template = require('template');
    var cardDiscount = require('tpl/card/cardDiscount.tpl');
    var main = {
        init: function () {
            main.bindEvent();
            main.changeInfo();
            var auto_upgrade = $('input[name="CardRechargeCategory[f_auto_upgrade]"]:checked').val()
            if (auto_upgrade == 1) {
                $('.auto-upgrade-content').show();
            } else {
                $('.auto-upgrade-content').hide();
            }
            var isCheckCommitted = false;//表单是否已经提交标识，默认为false
            $('#card-category').yiiAjaxForm({
                beforeSend: function () {
                    if (isCheckCommitted == false) {
                        isCheckCommitted = true;//提交表单后，将表单是否已经提交标识设置为true
                        return true;//返回true让表单正常提交
                    } else {
                        return false;//返回false那么表单将不提交
                    }
                },
                complete: function () {

                },
                success: function (data) {

                    if (data.errorCode == 0) {
                        showInfo(data.msg, '180px');
                        if (isCheckCommitted == true) {
                            $.pjax.reload({container: '#crud-datatable-pjax', cache: false, timeout: 5000});  //Reload
                            $('#ajaxCrudModal').modal('hide');
                        }
                    } else {
                        isCheckCommitted = false
                        if (data.errorCode == 1003) {
                            $('.field-cardrechargecategory-f_upgrade_amount').removeClass('has-success').addClass('has-error');
                            $('.field-cardrechargecategory-f_upgrade_amount .help-block').html(data.msg);
                            return;
                        }
                        showInfo(data.msg, '250px', 2);
                    }
                },
            });
            $('#cardrechargecategory-f_medical_fee_discount').after('%');
        },
        bindEvent: function () {
            $('[name="CardRechargeCategory[f_auto_upgrade]"]').click(function () {
                if ($(this).val() == 1) {
                    $('.auto-upgrade-content').show();
                } else {
                    $('.auto-upgrade-content').hide();
                }
            });
            var data = [];
//            $('.service-card-discount').append(main.setCardDiscount(cardDiscountList));
            main.cardDiscountConfig();

        },
        cardDiscountConfig: function () {
            $('.clinic-add').hide();
            $('.service-card-discount').find('.appointConfig:not(.hide)').last().find('.clinic-add').show();
            var len = $('.appointConfig:not(.hide)').length;
            if (len == 1) {
                $('.clinic-delete').hide();
            }
            $(".appointConfig .btn").unbind("click").click(function (e) {
                $('.clinic-add').hide();
                if ($(this).hasClass("clinic-add")) {
                    $(this).parents(".service-card-discount").append(main.setCardDiscount([]));
                    $(this).prev().show();
//                    $(this).hide();
                }
                if ($(this).hasClass("clinic-delete")) {
                    e.preventDefault();
                    var _par = $(this).parents('.appointConfig');
                    var deleted = _par.find('.deleted').val();
                    var newRecord = _par.find('.new-record').val();
                    if (newRecord == 2) {
                        _par.find('.deleted').val(1);
                        $(this).parents('.appointConfig').addClass('hide');
                    } else {
                        $(this).parents('.appointConfig').remove();
                    }
//                    $(this).parents(".appointConfig").remove();
                }
                $('.service-card-discount').find('.appointConfig:not(.hide)').last().find('.clinic-add').show();
                main.cardDiscountConfig();
            });
        },
        setCardDiscount: function (data) {
            var html = '', data = data || [];
            if (data.length != 0) {
                for (var i = 0; i < data.length; i++) {
                    html += template.compile(cardDiscount)({
                        tagList: tagList,
                        data: data[i]
                    });
                }
            } else {
                html = template.compile(cardDiscount)({
                    tagList: tagList,
                    data: [{tag_id: 0, discount: ''}]

                });
            }
            return html;
        },
        changeInfo: function () {
            $('body').on('change', '.tag-select', function () {
                var that = $(this);
                var selectV = that.attr('selectV')
                that.siblings('.change').val(selectV);
            });
        }
    }
    return main;
});



