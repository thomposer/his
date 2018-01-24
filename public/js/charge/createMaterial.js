

define(function (require) {
    var template = require('template');
    var select = require('plugins/select2/select2.full.min');
    var inboundTpl = require('tpl/charge/createMaterial.tpl');
    var bootstrap_timepicker = require('plugins/timepicker/bootstrap-timepicker.min');
    var cropper = require('dist/cropper');
    var uploadFile = require('tpl/uploadModal.tpl');
    var common = require('js/lib/common');
    var upload = require('upload/main');
    var appointment = require('tpl/appointment.tpl');
    var _self;
    var main = {
        init: function () {
            _self = this;
            this.bindEvent();
        },
        bindEvent: function () {


            $('.select2').select2();
            $('body').on('change', '#materialcharge-id', function (e) {
                var val = $(this).val();
                _self.addMaterial(val);
                $(this).val('');
            });
            $('body').on('click', '#createMaterial .op-group>img', function () {
                $(this).parents('tr').remove();
//                    $(this).siblings('input[name="MaterialCharge[deleted][]"]').val(1);
            });
            $('body').on('focus', '.date .form-control', function () {
                $(this).datepicker({
                    format: 'yyyy-mm-dd',
                    language: 'zh-CN',
                    inline: false,
                    autoclose: true
                })

            });
            var uploadModal = template.compile(uploadFile)({
                title: '上传头像',
                url: uploadUrl,
            });
            $('#crop-avatar').append(uploadModal);
            $('body').on('click', '.avatar-save', function () {
                var avatar = document.getElementById('avatarInput');
                var filename = avatar.value;
                var fileExtension = filename.substring(filename.lastIndexOf('.') + 1).toLowerCase();
                if (!filename && fileExtension != 'jpg' && fileExtension != 'png' && fileExtension != 'jpeg' && fileExtension != 'gif') {
                    showInfo('请上传正确的图片格式', '180px', 2);
                    return false;
                }
            });
            jsonFormInit = $("form").serialize();  //为了表单验证

            $('.field-patient-username').bind('input propertychange', function () {

                $.ajax({
                    type: 'post',
                    url: getPatients,
                    data: {
                        'patientName': $('#patient-username').val()
                    },
                    success: function (json) {
                        $('.J-search-name').remove();
                        if (json.data.length >= 1) {

                            var appointmentModal = template.compile(appointment)({
                                list: json.data,
                                baseUrl: baseUrl,
                                cdnHost: cdnHost
                            });
                            $('.field-patient-username').append(appointmentModal);
                        }
                    },
                    error: function () {

                    },
                    dataType: 'json'

                });

            });
            $('.field-patient-iphone').bind('input propertychange', function () {

                $.ajax({
                    type: 'post',
                    url: getIphone,
                    data: {
                        'patientIphone': $('#patient-iphone').val()
                    },
                    success: function (json) {
                        $('.J-search-name').remove();
                        if (json.data.length >= 1) {

                            var appointmentModal = template.compile(appointment)({
                                list: json.data,
                                baseUrl: baseUrl,
                                cdnHost: cdnHost
                            });
                            $('.field-patient-iphone').append(appointmentModal);
                        }
                    },
                    error: function () {

                    },
                    dataType: 'json'

                });

            });
            $('body').on('click', '.J-name-search-submit', function () {

                var id = $(this).attr('id');

                if (id) {
                    window.location.href = createUrl + '?patientId=' + id;
                    return;
                }
            });
            $('body').on('click', function () {
                $('.J-search-name').remove();
            });

            $('.empty').parents('tr').remove();
            $(".timepicker").timepicker({
                showInputs: false,
                showMeridian: false,
                minuteStep: 10,
                defaultTime: '00:00',
            });

            if ($('#patient-hourmin').val() == '00:00') {
                $('#patient-hourmin').val('');
            }
            var isCommitted = false;//表单是否已经提交标识，默认为false
            $('#createMaterialForm').find(".btn-default").one('click', function () {
                $('#createMaterialForm').yiiAjaxForm({
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
                        var actionUrl = $('#chargeForm').attr('actionurl');
                        if (data.similarUser) {
                            isCommitted = false;
                            var data = {postParam: data.postParam, similarUser: data.similarUser, actionUrl: actionUrl};
                            modal.open('#chargeForm', data);
                            return false;
                        } else if (data.errorCode == 0) {
                            isCommitted = false;
                            window.location.href = indexUrl;
                        } else {
                            var $button = $(this).data('yiiActiveForm').submitObject;
                            if ($button) {
                                $button.prop('disabled', false);
                            }
                            isCommitted=false;
		                    showInfo(data.msg,'300px',2);
		               }
		            },
		        });
            });
        },
        addMaterial: function (id) {
            var list = materialList[id];
            var totalNum = materialTotal[id];
            var showValue = '';
            if (list.manufactor != '') {
                showValue += '生产商：' + htmlEncodeByRegExp(htmlEncodeByRegExp(list.manufactor)) + '<br/>';
            }
            showValue += '零售价：' + list.price + '元';
            var inboundModel = template.compile(inboundTpl)({
                list: list,
                baseUrl: baseUrl,
                showValue: '',
                totalNum : totalNum ? totalNum : 0
            });
            $('#createMaterial tbody').append(inboundModel);

        }


    };
    return main;
})