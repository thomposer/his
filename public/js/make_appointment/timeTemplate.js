

define(function (require) {
    var template = require('template');
    var common = require('js/lib/common');
    var appointmentTimeConfig = require('tpl/appointmentTimeConfigRowCreate.tpl');
    var _self;
    var main = {
        init: function () {
            _self = this;
            this.bindEvent();
            var data = main.getFormAppointmentData();
            $('#setScheduleContent').append(main.appointmentConfigRowHtml(data));
            main.appointmentConfigRow();
            jsonFormInit = $("form").serialize();  //为了表单验证
        },

        bindEvent: function () {


            $('.btn-appiontment-save').bind('click',function () {
                main.configFormAppointmentData();
            })

            var isRecipeCommitted = false;//表单是否已经提交标识，默认为false
            $('#template-form').yiiAjaxForm({

                beforeSend: function() {
                    if(isRecipeCommitted == false){
                        isRecipeCommitted = true;//提交表单后，将表单是否已经提交标识设置为true
                        return true;//返回true让表单正常提交
                    }else{
                        return false;//返回false那么表单将不提交
                    }

                    return result;


                },
                complete: function() {

                },
                success: function(data) {

                    if(data.errorCode == 0){
                        window.location.href = appointmentTimeTemplateUrl;

                    }else{
                        var $button = $(this).data('yiiActiveForm').submitObject;
                        if ($button) {
                            $button.prop('disabled', false);
                        }
                        isRecipeCommitted =false;
                        showInfo(data.msg,'200px',2);
                    }
                },
            });

            $('.timepicker').datetimepicker({
                startDate: timeConfig.begin_time,
                endDate: timeConfig.end_time,
                language: 'zh-CN',
                startView: 1,
                minuteStep: 10,
                initialDate: '00:00',
                format: 'hh:ii',
                minView: 0,
                maxView: 1,
                formatViewType: 'time',
                autoclose: true
            });
        },


        appointmentConfigRowHtml: function (data) {
            var html = '', data = data || [];
            if (data.length != 0) {
                for (var i = 0; i < data.length; i++) {
                    data[i].removeTitleState = 1;
                    html += template.compile(appointmentTimeConfig)(data[i]);
                }
            } else {
                html = template.compile(appointmentTimeConfig)({});
            }
            return html;
        },

        appointmentConfigRow: function () {
            $('.clinic-add').hide();
            $('.clinic-add').last().show();

            var clinic_count=$('.appointConfig').length;
            if(clinic_count < 2){
                $('.clinic-delete').hide();
            }else {
                $('.clinic-delete').show();
            }
            $(".appointConfig .btn").unbind("click").click(function () {
                $('.clinic-add').hide();
                if ($(this).hasClass("clinic-add")) {
                    var data = [{removeTitleState: 1}];
                    $(this).parents(".modal-content-details").append(main.appointmentConfigRowHtml(data));
                    $(this).prev().show();
                }
                if ($(this).hasClass("clinic-delete")) {
                    $(this).parents(".appointConfig").remove();
                }
                $('.clinic-add').last().show();
                main.appointmentConfigRow();
            });
            $('.timepicker').datetimepicker({
                startDate: timeConfig.begin_time,
                endDate: timeConfig.end_time,
                language: 'zh-CN',
                startView: 1,
                minuteStep: 10,
                initialDate: '00:00',
                format: 'hh:ii',
                minView: 0,
                maxView: 1,
                formatViewType: 'time',
                autoclose: true
            });

        },

        getFormAppointmentData:function () {
            var data = [];
            var formData = $('#appointmenttimetemplate-appointment_times').val();
            if(formData != null && formData != undefined && formData.length > 0){
                var dataArr = formData.split(',');
                for(var i = 0 ; i < dataArr.length; i++){
                    var  v = dataArr[i];
                    var timeArr = v.split('-');
                    var timeItem = {};
                    timeItem.begin_time = timeArr[0];
                    timeItem.end_time = timeArr[1];
                    data.push(timeItem);
                }
            }

            return data;
        },

        configFormAppointmentData:function () {
            var submitTimes = '';
            $(".appointConfig").each(function () {
                var $beginTime = $(this).find(".appoint-begin-time"),
                    $endTime = $(this).find(".appoint-end-time");

                submitTimes += $beginTime.val() + '-' +  $endTime.val() + ',';
            });

            if(submitTimes.length > 0){
                submitTimes = submitTimes.substring(0, submitTimes.length - 1);
            }
            $('#appointmenttimetemplate-appointment_times').val(submitTimes);
        }


    };
    return main;
})
