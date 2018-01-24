/**
 *
 */
define(function (require) {
    var select = require('plugins/select2/select2.full.min');
    var common = require('js/lib/common');
    var patientInfoCard = require('tpl/recharge/patientInfoCard.tpl');
    var template = require('template');
    var timeOut = '';
    var main = {
        init: function () {
            $('.select2').select2();
            var _self = this;
            main.bindEvent();
        },
        bindEvent: function () {

            $('body').off('change', '#membershippackagecard-package_card_id').on('change', '#membershippackagecard-package_card_id', function () {
                var id = $(this).val();
                var cardInfo = cardList[id];
                main.initFormData(cardInfo);
            });
            if(error == 1 || outTradeNo){
                var id = $('#membershippackagecard-package_card_id').val();
                var cardInfo = cardList[id];
                main.initFormData(cardInfo);
            }
            	main.selectPatient();
        },
        initFormData : function(info){

            console.log(info);
            if (typeof (info) != "undefined") {
                $('#membershippackagecard-price').val(info.price);
                $('#membershippackagecard-validitytime').val(info.validity_period);
                $('#membershippackagecard-content').val(info.content);
            }else{
                $('#membershippackagecard-price').val('');
                $('#membershippackagecard-validitytime').val('');
                $('#membershippackagecard-content').val('');
            }

        },
        selectPatient : function(){
            $('.create-membership-package-card .field-membershippackagecardunion-iphone').bind('input propertychange',function(){

                    var value = $(this).find('#membershippackagecardunion-iphone').val();
                    var disabled =  $(this).attr('disabled');
                    if(value == ''){
                        return;
                    }
                    if(timeOut != ''){
                        clearTimeout(timeOut);
                    }
                    timeOut = setTimeout(function(){
                        main.getPatientInfo(value,$(this));
                    },2000);

            });
            $('body').on('click', '.membership-card-li', function () {
            	var target=$(this).parents('.col-md-6').next('.col-md-6');
                var babyInfo = $(this).attr('data-username')+" （"+ $(this).attr('data-birthday') + $(this).attr('data-sex') +"）";
                target.find('#membershippackagecardunion-patientinfo').val(babyInfo);
                $(this).parents('.col-md-6').find('#membershippackagecardunion-iphone').val($(this).attr('data-iphone'));
                target.find('#membershippackagecardunion-patient_id').val($(this).attr('data-patientId'));
                $(this).parents('.col-md-6').find('.J-search-name').remove();
            });

        },
        getPatientInfo : function(value,obj){
        		// obj = obj || $(this);
          //       console.log(obj);
                $.ajax({

                        type: 'post',

                        url: getIphone ,

                        data: {
                            'patientIphone' : value,
                            'oldPatient' : 1
                        } ,

                        success: function(json){
                            $('.J-search-name').remove();
                            if(json.data.length >= 1){

                                var patientInfoCardModal = template.compile(patientInfoCard)({
                                    list : json.data,
                                    baseUrl : baseUrl,
                                    cdnHost : cdnHost
                                });
                                $('.create-membership-package-card .field-membershippackagecardunion-iphone').append(patientInfoCardModal);
                            }
                        },
                        error : function(){

                        },
                        dataType: 'json'

                    });
        }

    };
    return main;
})