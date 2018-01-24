/* 
 * time: 2017-3-21 18:11:56.
 * author : yu.li.
 */

define(function (require) {
    var common = require('js/lib/common');
    var main = {
        init: function () {
            main.cardFlowEvent();
            main.rechargeEvent();
            if (typeof (payType) != 'undefined') {
                main.changeType(payType);
            }
        },
        cardFlowEvent: function () {
            $('#cardflow-f_record_type').on('change', function () {
                var type = $(this).val();
                main.changeType(type);
            }).change();
            $('#cardflow-isdonation').click(function () {
                if ($(this).is(':checked')) {
                    $('.cardflow-donationfee').show();
                } else {
                    $('.cardflow-donationfee #cardflow-donationfee').val('');
                    $('.cardflow-donationfee').hide();
                    var fee = $('#cardflow-f_record_fee').val();
                    var denation = $('#cardflow-donationfee').val();
                    main.getSumAmount(fee, denation);
                }
            });
            $('#cardflow-isempty').click(function () {
                var fee = $('#cardflow-f_record_fee').val();
                main.getSumAmount(fee, 0);
            });
            $('body').on('keyup', '#cardflow-f_record_fee', function () {
                var denation = $('#cardflow-donationfee').val();
                main.getSumAmount($(this).val(), denation);
            });
            $('body').on('keyup', '#cardflow-donationfee', function () {
                var fee = $('#cardflow-f_record_fee').val();
                main.getSumAmount(fee, $(this).val());
            });
            $('body').on('change', '#cardflow-f_record_type', function () {
                var fee = $('#cardflow-f_record_fee').val();
                var denation = $('#cardflow-donationfee').val();
                main.getSumAmount(fee, denation);
            })
        },
        rechargeEvent: function () {
            main.rechargeSumAmount($('#order-total_amount').val(), $('#order-donation_fee').val());
            $('.order-check-upgrade').on('click', function () {
                if ($(this).is(':checked')) {
                    $('.order-isupgrade').val(1);
                } else {
                    $('.order-isupgrade').val(2);
                }
            });
            $('#order-isdonation').click(function () {
                if ($(this).is(':checked')) {
                    $('.order-donation_fee').show();
                } else {
                    $('.order-donation_fee #order-donation_fee').val('');
                    $('.order-donation_fee').hide();
                    var fee = $('#order-total_amount').val();
                    var denation = $('#order-donation_fee').val();
                    main.rechargeSumAmount(fee, denation);
                }
            });
            $('body').on('input propertychange', '#order-total_amount', function () {
                var denation = $('#order-donation_fee').val();
                main.rechargeSumAmount($(this).val(), denation);
            });
            $('body').on('input propertychange', '#order-donation_fee', function () {
                var fee = $('#order-total_amount').val();
                main.rechargeSumAmount(fee, $(this).val());
            });
        },
        rechargeSumAmount: function (fee, donationfee) {
            if (isNaN(fee)) {
                return false;
            }
            if (isNaN(donationfee)) {
                return false;
            }
            if (!($('.donation-amount-checkbox').is(':checked'))) {
                donationfee = 0;
            }
            var total = add(donationfee, oldAmount);
            var result = add(fee, total);
            $('.expect-amount-num').html(toDecimal2(result));
        },
        getSumAmount: function (fee, donationfee) {
            if (isNaN(fee)) {
                return false;
            }
            if (isNaN(donationfee)) {
                return false;
            }
            if (!($('.donation-amount-checkbox').is(':checked'))) {
                donationfee = 0;
            }
            var recordType = $('#cardflow-f_record_type').val();
            if (recordType == 2 || recordType == 3) {
                fee = -fee;
                if (recordType == 3 && $('#cardflow-isempty').is(':checked')) {
                    fee -= donationFee;
                }
            }
            var total = add(donationfee, oldAmount);
            var result = add(fee, total);
            $('.expect-amount-num').html(toDecimal2(result));
        },
        changeType: function (type) {
            if (type == 1) {
                $('.f_pay_type_dis').show();
                $('.cardflow-isdonationfee').show();
                $('.cardflow-returnDonation').hide();
                $('.cardflow-isEmpty').hide();
                $('#cardflow-isempty').attr("checked", false);
                $('#cardflow-returnDonation').attr("checked", false);
            } else if (type == 2) {
                $('.f_pay_type_dis').hide();
                $('#cardflow-f_pay_type').val(0);
//                console.log($('#cardflow-f_pay_type').val(), 'f_pay_type_dis');
                $('.cardflow-isdonationfee').hide();
                $('.cardflow-donationfee').hide();
                $('.cardflow-donationfee #cardflow-donationfee').val('');
                $("#cardflow-isdonation").attr("checked", false);
                $('.cardflow-returnDonation').hide();
                $('.cardflow-isEmpty').hide();
                $('#cardflow-isempty').attr("checked", false);
                $('#cardflow-returndonation').attr("checked", false);
            } else if (type == 3) {
                $('.cardflow-isEmpty').show();
                $('.cardflow-returnDonation').hide();
                $('.f_pay_type_dis').hide();
                $('#cardflow-f_pay_type').val(0);
                $('.cardflow-isdonationfee').hide();
                $('.cardflow-donationfee').hide();
                $('.cardflow-donationfee #cardflow-donationfee').val('');
                $("#cardflow-isdonation").attr("checked", false);
                $('#cardflow-returndonation').attr("checked", false);
            } else if (type == 4) {
                $('.cardflow-returnDonation').show();
                $('.f_pay_type_dis').hide();
                $('#cardflow-f_pay_type').val(0);
                $('.cardflow-isdonationfee').hide();
                $('.cardflow-donationfee').hide();
                $('.cardflow-isEmpty').hide();
                $('.cardflow-donationfee #cardflow-donationfee').val('');
                $("#cardflow-isdonation").attr("checked", false);
                $('#cardflow-isempty').attr("checked", false);
            }
        }
    }
    return main;
});



