define(function (require) {
    var template = require('template');

    var common=require('js/lib/common');
    var scheduleConfigTpl = require('tpl/scheduleConfig.tpl');
  
    var bootstrap_timepicker = require('plugins/timepicker/bootstrap-timepicker.min');
    var main = {
        init: function () {
            this.addShiftTime();
            this.bindEvent();
            this.initShiftBtn();
        },
        initShiftBtn:function(){
            var len = $('.clinic-shift-time').length;
            if (len >= 2) {
                $(".clinic-delete").show();
                $(".clinic-add").hide();
                $('.clinic-add').last().show();
            }else {
                $(".clinic-delete").first().hide();
            }
        },
        addShiftTime: function () {
            $('body').on('click', '.clinic-add', function () {
                var clinic_len=$('.clinic-shift-time').length;
                if(clinic_len>=5){
                    showInfo('班次时间段数量不超过5个','220px',2);
                    return false;
                }
                var shiftTime = template.compile(scheduleConfigTpl)();
                
                $('#clinic-shift-time').append(shiftTime);
                $('.clinic-add').hide();
                $(".clinic-delete").first().show();
                $('.clinic-add').last().show();
            });

            $('body').on('click', '.clinic-delete', function () {
                $(this).parents('.clinic-shift-time').remove();
                var len = $('.clinic-shift-time').length;
                $('.clinic-add').hide();

                if (len == 1) {
                    $('.clinic-delete').hide();
                }

                $('.clinic-add').last().show();

            });

        },
        bindEvent: function () {
            $('body').on('focus', ".timepicker_start", function () {
                $(this).timepicker({
                    showInputs: false,
                    showMeridian: false,
                    defaultTime: '00:00'
                });
            });
            $('body').on('focus', ".timepicker_end", function () {
                $(this).timepicker({
                    showInputs: false,
                    showMeridian: false,
                    defaultTime: '00:00'
                });
            });
        },
        getQueryString: function (name) {
//	var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)","i");
            var reg = new RegExp("(^|&|&amp;)" + name + "=([^&]*)(&|$)", "i");
            var r = window.location.search.substr(1).match(reg);
            if (r != null)
                return r[2];
            return '';
        }
    };
    return main;
})