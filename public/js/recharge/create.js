/**
 * 
 */
define(function (require) {
    var select = require('plugins/select2/select2.full.min');
    var common = require('js/lib/common');
    var template = require('template');
    var familyInfoCellTpl = require('tpl/familyInfoCell.tpl');
    var main = {
        init: function () {
            $('.select2').select2();
            $('body').on('mouseover', '.select2-results__options--nested > li', function () {
                var serviceIdStr = $(this).attr('id');
                var serviceIdArr = serviceIdStr.split('-');
                var serviceId = serviceIdArr[serviceIdArr.length - 1];
                var a = '';
                a += '<div class="service-desc">';
                a += '服务折扣：' + serviceJson[serviceId].service;
                a += '</div>';
                a += '<div class="card-service-desc">';
                a += '卡片描述：' + serviceJson[serviceId].serviceDesc;
                a += '</div>';
                $('.card-service-container').html(a);
            })
            main.bindEvent();
        },
        bindEvent: function () {
//            $('.buy-time #cardrecharge-f_buy_time').datetimepicker({
//                language: 'zh-CN',
//                minuteStep: 1,
//                format: 'yyyy-mm-dd hh:ii',
//                autoclose: true,
//                size: 'lg',
//            });
            $('.btn-create-card-flow').click(function () {
                $('.cardSubmitType').val(2);
                $('.btn-create-card').click();
            });

            $('body').on('click', '.familyInfoCell-li', function () {
                $('#cardrecharge-f_user_name').val($(this).attr('data-name'));
                $('#cardrecharge-f_id_info').val($(this).attr('data-card'));
                var babyInfo = $(this).attr('data-username')+" （"+ $(this).attr('data-birthday') + $(this).attr('data-sex') +"）";
                $('#cardrecharge-f_baby_name').val(babyInfo);
                if($('#cardrecharge-f_phone').val() != $(this).attr('data-iphone')){
                    $('#cardrecharge-f_phone').val($(this).attr('data-iphone'));
                    main.getCardCategory(1);
                }

            });

            $('body').on('click', function () {
                $('.J-search-name').remove();
            });

            $('#cardrecharge-f_phone').on("input propertychange",function () {
                main.getCardCategory(0);
            });
            $('.field-cardrecharge-f_phone').css('position', 'relative');
        },
        getCardCategory:function(search_type){

            var phoneLength = $("#cardrecharge-f_phone").val().length;
            var phone = $("#cardrecharge-f_phone").val();
            if(phoneLength <= 11 && phoneLength > 0  &&　!isNaN(phone) && phone){
                $.ajax({
                    type: 'post',
                    url: getPhoneCategory ,
                    data: {
                        phone : phone,
                        search_type: search_type,
                    } ,
                    success: function(json){
                        data = json.data;
                        var a = '';
                        a += '<div>';
                        a += '<div class="card-desc">';
                        a += '输入的手机号已建如下卡种';
                        a += '</div>';
                        a += '<div >';
                        for(var i=0;i<data.length;i++){
                            a += '<a href="'+cardDeatail+'?id='+data[i].f_physical_id+'" target="_blank">';
                            a += '<div class="card-category-list">';
                            a +=' &nbsp';
                            a += data[i].f_buy_time;
                            a +=' &nbsp';
                            a += htmlEncodeByRegExp(data[i].f_user_name);
                            a +=' &nbsp';
                            a += data[i].f_category_name?htmlEncodeByRegExp(data[i].f_category_name):'暂无卡种';
                            a += '</div>';
                            a += '</a>';
                        }
                        a += '</div>';
                        a += '</div>';
                        $('#phone-card-category').html(a);
                        if(data.length == 0){
                            $('#phone-card-category').html('');
                        }

                        var dataFamilyInfo = json.familyData;
                        $('.J-search-name').remove();
                        if (dataFamilyInfo.length >= 1) {

                            var familyInfoModal = template.compile(familyInfoCellTpl)({
                                list: dataFamilyInfo,
                            });
                            $('.field-cardrecharge-f_phone').append(familyInfoModal);
                        }
                    },
                    error : function(){

                    },
                    dataType: 'json'

                });
            }else{
                $('#phone-card-category').html('');
                $('.J-search-name').remove();
            }
        },

    };
    return main;
})