
define(function (require) {
    var common = require('js/lib/common');
    var main = {
        init: function () {
            _self = this;

            this.bindEvent();
            this.checkBtnState();

        },
        checkBtnState: function () {
            if ($('.check-type:checked').length > 0) {
                $('.save-charge').find('button').attr('disabled', false);
                $('.save-charge').find('button').attr('style', 'background:#76A6EF');
            } else {
                $('.save-charge').find('button').attr('disabled', true);
                $('.save-charge').find('button').attr('style', 'background:#BAD2F7');
            }
        },
        bindEvent: function () {

            $('.select-on-check-all').on('click', function () {

                setTimeout('main.addPrice()', 100);
                setTimeout('main.checkBtnState()', 100);

            }).click();
            $('input:checkbox[name="selection[]"]').on('click', function () {
                main.checkBtnState();
                main.addPrice();
            });

            $('body').on('click', '.rebate', function (event) {
                $('.my-show').jqprint();
            });

            $(document).on('click', '[role="modal-create"]', function (event) {
                event.preventDefault();
                var data;
                // Collect all selected ID's
                var selectedIds = [];
                var disabled = $(this).hasClass('disabled');
                $('input:checkbox[name="selection[]"]').each(function () {
                    if (this.checked)
                        selectedIds.push($(this).val());
                });
                var modalCreate = this;
                // alert(selectedIds);
                if (selectedIds.length == 0 || disabled) {
                    // If no selected ID's show warning
                    return false;
                } else {
                	//判断是否有非药品收费
                	$.ajax({
                        type: 'POST',
                        url: apiChargeCheckMaterialRecordNum,
                        data: {
                        	selectedIds: selectedIds,
                        	updateMaterialButtonType : updateMaterialButtonType
                        },
                        cache: false,
                        success: function (json) {
                            if (json.errorCode == 0) {
                            	$('#selectIds').val(selectedIds);
                                modal.open(modalCreate, $('#ModalRemoteConfirmForm').serialize());
                            }else if(json.errorCode != 1001){
                            	showInfo(json.msg,'200px',2);
                            }
                        },
                        error: function () {

                        },
                        dataType: 'json',
                    });
                }
            });

            $('body').on('keyup', '#chargerecord-cash', function () {
                var val = $(this).val();
                if (isNaN(val)) {
                    return false;
                }
                if ($.trim(val) == '') {
                    val = 0;
                }
                val = toDecimal2(val);
                var total_price = $('#chargerecord-price').val();
                var result = sub(val, total_price);
                if (result >= 0) {
                    $('.cost-bg').css({'background': 'url(' + baseUrl + '/public/img/charge/cost_bg_pree.png)', 'background-color': 'rgb(120,167,237)'});
                } else {
                    $('.cost-bg').css({'background': 'url(' + baseUrl + '/public/img/charge/cost_bg.png)', 'background-color': '#55657d'});

                }

                $('.cash').html('¥' + toDecimal2(result));
            })

            $('body').on('change', '#chargerecord-cash', function () {
                var val = $(this).val();
                if (isNaN(val)) {
                    return false;
                }
                if ($.trim(val) == '') {
                    val = 0;
                }
                val = toDecimal2(val);
                var total_price = $('#chargerecord-price').val();
                var result = sub(val, total_price);
                if (result >= 0) {
                    $('.cost-bg').css({'background': 'url(' + baseUrl + '/public/img/charge/cost_bg_pree.png)', 'background-color': 'rgb(120,167,237)'});
                } else {
                    $('.cost-bg').css({'background': 'url(' + baseUrl + '/public/img/charge/cost_bg.png)', 'background-color': '#55657d'});

                }

                $('.cash').html('¥' + toDecimal2(result));
            });
        },
        addPrice: function () {
            var total_price = 0;
            var discountTotalPrice = 0;
            var type;
            var typePrice = [];
            var discountTotal = [];
            typePrice[1] = 0;
            typePrice[2] = 0;
            typePrice[3] = 0;
            typePrice[4] = 0;
            typePrice[5] = 0;
            typePrice[7] = 0;
            typePrice[9] = 0;
            discountTotal[1] = 0;
            discountTotal[2] = 0;
            discountTotal[3] = 0;
            discountTotal[4] = 0;
            discountTotal[5] = 0;
            discountTotal[7] = 0;
            discountTotal[9] = 0;
            $('input:checkbox[name="selection[]"]').each(function () {
                if (this.checked) {
                    var price = $(this).parent('td').siblings('td.total_price').text();
                    console.log(price, 222);
                    var discountPrice = parseFloat($(this).parent('td').siblings('td.discountPrice').text());
                    if (isNaN(discountPrice)) {
                        discountPrice = 0;
                    }
//                    total_price += price * 100;
                    price = main.accMul(price, 100);
                    total_price = total_price + price;
                    console.log(total_price, 333);
                    discountTotalPrice += discountPrice;
                    type = $(this).data('type');
                    if(type == 8){
                    	type = 7;
                    }
                    typePrice[type] += price;
                    discountTotal[type] += discountPrice;
                }
            });
            //console.log(discountTotal[5]);
            $('.five-price').html(toDecimal2(typePrice[5]/100) + (discountTotal[5] != 0 ? '（已优惠' + toDecimal2(discountTotal[5]) + '）' : ''));
            $('.inspect-price').html(toDecimal2(typePrice[1]/100) + (discountTotal[1] != 0 ? '（已优惠' + toDecimal2(discountTotal[1]) + '）' : ''));
            $('.check-price').html(toDecimal2(typePrice[2]/100) + (discountTotal[2] != 0 ? '（已优惠' + toDecimal2(discountTotal[2]) + '）' : ''));
            $('.cure-price').html(toDecimal2(typePrice[3]/100) + (discountTotal[3] != 0 ? '（已优惠' + toDecimal2(discountTotal[3]) + '）' : ''));
            $('.recipe-price').html(toDecimal2(typePrice[4]/100) + (discountTotal[4] != 0 ? '（已优惠' + toDecimal2(discountTotal[4]) + '）' : ''));
            $('.material-price').html(toDecimal2(typePrice[7]/100) + (discountTotal[7] != 0 ? '（已优惠' + toDecimal2(discountTotal[7]) + '）' : ''));
            $('.package-price').html(toDecimal2(typePrice[9]/100) + (discountTotal[9] != 0 ? '（已优惠' + toDecimal2(discountTotal[9]) + '）' : ''));

            console.log(total_price, 444);
            console.log(discountTotalPrice, 555);
            console.log(toDecimal2((total_price + discountTotalPrice * 100) / 100), 666);
            $('.old-selected-total-price').html(toDecimal2((total_price + discountTotalPrice * 100) / 100));
//            $('.selected-price').html('¥' + toDecimal2(total_price));
            $('.discount-total-price').html(toDecimal2(discountTotalPrice));
            $('.receiptAmount').html(toDecimal2(total_price / 100));
            //console.log(discountTotal[2],3333);
            //console.log(toDecimal2(discountTotal[2]),3333);
        },
        accMul: function (arg1, arg2) {
            var m = 0, s1 = arg1.toString(),
                    s2 = arg2.toString();
            try {
                m += s1.split(".")[1].length
            } catch (e) {
            }
            try {
                m += s2.split(".")[1].length
            } catch (e) {
            }
            return Number(s1.replace(".", "")) * Number(s2.replace(".", "")) / Math.pow(10, m)
        }


    }
    return main;
})