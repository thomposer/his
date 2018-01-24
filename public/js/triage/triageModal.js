/* 
 * time: 2017-3-1 10:43:32.
 * author : yu.li.
 */
define(function (require) {
//    var allergyTpl = require('tpl/allergy.tpl');
    var healthEducationTpl = require('tpl/healthEducation.tpl');
    var allergyContentTpl = require('tpl/allergyContent.tpl');
    var childAssesmentTpl = require('tpl/childAssesmentTpl.tpl');
    var template = require('template');
    var _self;
    var main = {
        item_length: 0,
        init: function () {
            _self = this;
            this.healthEducation();
            this.nursingRecord();
            this.resetDelBtn();
            this.bindEvent();
            this.editAssesment();
            this.editFall();
            this.bindAssesment();
            this.saveForm();
        },
        bindEvent: function () {
//            $('body').off('click','.have-allergy').on('change','.have-allergy',function(){
//                if($(this).val() == 1){
//                    $('.allergy-content').hide();
//                }else{
//                    $('.allergy-content').show();
//                }
//            });
//            
//            $('body').off('change','#allergyoutpatient-type').on('change','#allergyoutpatient-type',function(){
//                if($(this).val() != 0){
//                    var content = $(this).find("option:selected").text();
//                    $(this).parents('.allergy-line').find('#allergyoutpatient-allergy_content').val(content);
//                }else{
//                    $(this).parents('.allergy-line').find('#allergyoutpatient-allergy_content').val('');
//                }
//                        
//            });
//            
//            $('body').off('click','.allergy-add').on('click','.allergy-add',function(){
//                var key = parseInt($(this).attr('data-key')) + 1;
//                var html = template.compile(allergyContentTpl)({
//                    key : key,
//                });
//                $('.allergy-content').append(html);
//                $('.allergy-add').hide();
//                $('.allergy-delete').show();
//                $('.allergy-delete').last().show();
//                $('.allergy-add').last().show();
//            });
//            $('body').off('click','.allergy-delete').on('click','.allergy-delete',function(){
//                $(this).parents('.allergy-line').remove();
//                $('.allergy-add').hide();
//                $('.allergy-delete').show();
//                $('.allergy-delete').last().show();
//                $('.allergy-add').last().show();
//            });
              // 身高精确度控制
              $('body').on('input','#triageinfo-heightcm',function(e){
                    accuracyControl($(this),1);
              });
              // 体重精确度控制
              $('body').on('input','#triageinfo-weigstkg',function(e){
                    accuracyControl($(this),2);
              });
              // 头围精确度控制
              $('body').on('input','#triageinfo-head_circumference',function(e){
                    accuracyControl($(this),1);
              });
              // 体温精确度控制
              $('body').on('input','#triageinfo-temperature',function(e){
                    accuracyControl($(this),1);
              });
              
        },
        nursingRecord: function () {
            $('#nursingrecord-template_id').change(function () {
                $.ajax({
                    cache: true,
                    type: "POST",
                    url: getTemplateContentUrl,
                    data: {
                        template_id: $(this).val()
                    },
                    dataType: 'json',
                    async: false,
                    success: function (result, textStatus, jqXHR) {
                        $('#nursingrecord-content').val(result.data.content);
                    },
                    error: function () {
                        alert('操作失败');
                    }
                });
            });
        },
        healthEducation: function () {
            $('.add-health-education').unbind('click').click(function (e) {
                _self.setItemLength();
                if (_self.item_length >= 1) {
                    $('.health-edu-right-delete').show();
                }
                var html = template.compile(healthEducationTpl);
                $('.health-edu-content').append(html);
            });
            $('body').off('click', '.del-health-education').on('click', '.del-health-education', function (e) {
                _self.setItemLength();
                if (_self.item_length > 1) {
                    $(this).parents('.health-edu-item').remove();
                    _self.resetDelBtn();
                }
            })
        },
        resetDelBtn: function () {
            _self.setItemLength();
            if (_self.item_length <= 1) {
                $('.health-edu-right-delete').hide();
            }
        },
        setItemLength: function () {
            _self.item_length = $('.health-edu-item').length;
        },
        editAssesment: function () {//疼痛评分
            _self.initAssesmentBtn('.assesment-content');
            _self.addAssesmentTime('.assesment-content', 1);
        },
        editFall: function () {//跌倒评分
            _self.initAssesmentBtn('.fall-content');
            _self.addAssesmentTime('.fall-content', 2);
        },
        initAssesmentBtn: function (parentObj) {
            var len = $(parentObj + ' .assesment-config').length;
            if (len >= 2) {
                $(parentObj + " .clinic-delete-score").show();
                $(parentObj + " .clinic-add-score").hide();
                $(parentObj + ' .clinic-add-score').last().show();
            } else {
                $(parentObj + " .clinic-delete-score").first().hide();
            }
        },
        addAssesmentTime: function (parentObj, type) {
            $('body').off('click', parentObj + ' .clinic-add-score').on('click', parentObj + ' .clinic-add-score', function () {
                var data;
                if (type == 1) {//疼痛评分
                    var acbtLen = $('.child-assesment-time').length;
                    var key = acbtLen + 1;
                    data = {
                        scoreLabel: '疼痛评分(0-10)',
                        scoreName: 'score',
                        scoreTime: 'assesment_time',
                        scoreRemark: 'remark',
                        selectId: 'child-assesment-time' + key,
                        selectClass: 'child-assesment-time',
                        scoreClass: 'child-assessment-score',
                        remarkClass: 'child-assessment-remark'
                    };
                } else {
                    var acbtLen = $('.child-fall-time').length;
                    var key = acbtLen + 1;
                    data = {
                        scoreLabel: '跌倒评分',
                        scoreName: 'fallScore',
                        scoreTime: 'fallTime',
                        scoreRemark: 'fallRemark',
                        selectId: 'child-fall-time' + key,
                        selectClass: 'child-fall-time',
                        scoreClass: 'child-fall-score',
                        remarkClass: 'child-fall-remark'
                    };
                }
                var shiftTime = template.compile(childAssesmentTpl)(data);
                $(parentObj).append(shiftTime);
                _self.initTimePicker();
                $(parentObj + ' .clinic-add-score').hide();
                $(parentObj + "  .clinic-delete-score").first().show();
                $(parentObj + ' .clinic-add-score').last().show();
            });

            $("body").on('focus', '.form-datetime', function () {
                _self.initTimePicker(this);
            });
            $("body").on('click', '.form_datetime', function () {
                _self.initTimePicker(this);
            });
            $('body').off('click', parentObj + ' .clinic-delete-score').on('click', parentObj + ' .clinic-delete-score', function () {
                $(this).parents('.assesment-config').remove();
                var len = $(parentObj + ' .assesment-config').length;
                $(parentObj + ' .clinic-add-score').hide();
                if (len == 1) {
                    $(parentObj + ' .clinic-delete-score').hide();
                }
                $(parentObj + ' .clinic-add-score').last().show();
                var timeObj = (parentObj == '.assesment-content') ? 'child-assesment-time' : 'child-fall-time';
                _self.eachTimeValidate(timeObj);
            });
        },
        initTimePicker: function (obj) {
            var target = obj || '.form-datetime'
            $(target).datetimepicker({
                showInputs: true,
                autoclose: true,
                language: 'zh-CN',
                minuteStep: 10,
                allowInputToggle: true,
                pickerPosition: 'bottom-right',
                endDate: new Date(),
                clientOptions: {
                    allowInputToggle: true,
                }
            });
        },
        bindAssesment: function () {
            $('body').on('focusout', '.child-assessment-score', function (e) {
                var value = $(this).val();
                var messages = [];
                _self.validateScore(value, messages);
                _self.saveError($(this), messages);
            });
            $('body').on('focusout', '.child-fall-score', function (e) {
                var value = $(this).val();
                var messages = [];
                _self.validateFallScore(value, messages);
                _self.saveError($(this), messages);
            });
            $('body').on('focusout', '.child-assessment-remark', function (e) {
                var value = $(this).val();
                var messages = [];
                _self.validateRemark(value, messages);
                _self.saveError($(this), messages);
            });
            $('body').on('focusout', '.child-fall-remark', function (e) {
                var value = $(this).val();
                var messages = [];
                _self.validateRemark(value, messages);
                _self.saveError($(this), messages);
            });
            $('body').on('change', '.child-assesment-time', function (e) {
                _self.eachTimeValidate('child-assesment-time');
            });
            $('body').on('change', '.child-fall-time', function (e) {
                _self.eachTimeValidate('child-fall-time');
            });
        },
        eachTimeValidate: function (obj) {
            $('.' + obj).each(function (i, el) {
                var messages = [];
                var value = $(this).val() || 0;
                var lastTime = $(this).parents('.assesment-config').prev('.assesment-config').find('.' + obj).val();
                console.log(lastTime);
                if (typeof (lastTime) == 'undefined') {
                    _self.saveError($(this), messages, 2);
                    return;
                }
                lastTime = lastTime ? lastTime : 0;
                console.log(value, 'value');
                console.log(lastTime, 'lastTime');
                if ((lastTime == 0 && value != 0) || (value != 0 && value <= lastTime)) {
                    messages.push('不得早于上一次的评估时间');
                }
                _self.saveError($(this), messages, 2);
            })
        },
        validateScore: function (value, messages) {
            yii.validation.number(value, messages, {
                "pattern": /^\s*[+-]?\d+\s*$/,
                "message": "疼痛评分（0-10）必须是整数。",
                "min": 0,
                "tooSmall": "疼痛评分（0-10）的值必须不小于0。",
                "max": 10,
                "tooBig": "疼痛评分（0-10）的值必须不大于10。",
                "skipOnEmpty": 1
            });
        },
        validateFallScore: function (value, messages) {
            yii.validation.number(value, messages, {
                "pattern": /^\s*[+-]?\d+\s*$/,
                "message": "跌倒评分（HDFS 6-20）必须是整数。",
                "min": 6,
                "tooSmall": "跌倒评分（HDFS 6-20）的值必须不小于6。",
                "max": 20,
                "tooBig": "跌倒评分（HDFS 6-20）的值必须不大于20。",
                "skipOnEmpty": 1
            });
        },
        validateRemark: function (value, messages) {
            yii.validation.string(value, messages, {
                "message": "备注必须是一条字符串。",
                "max": 30,
                "tooLong": "备注只能包含至多30个字符。",
                "skipOnEmpty": 1
            });
        },
        saveError: function (obj, messages) {
            var type = arguments[2] ? arguments[2] : 1;
            var sb = type == 1 ? obj : obj.parent();
            console.log(messages, 'sdd');
            if (messages[0]) {
                sb.parent().removeClass('has-success').addClass('has-error');
                sb.siblings('.help-block').html(messages[0]);
            } else {
                if (sb.parent().hasClass("has-error")) {
                    if (type == 1) {
                        sb.parent().removeClass('has-error').addClass('has-success');
                    } else {
                        sb.parent().removeClass('has-error');
                    }
                    sb.siblings('.help-block').html('');
                }

            }
        },
        saveForm: function () {
//            var isCheckCommitted = false;//表单是否已经提交标识，默认为false
            $('#j_tabForm_2').yiiAjaxForm({
                beforeSend: function () {
//                    if (isCheckCommitted == false) {
//                        isCheckCommitted = true;//提交表单后，将表单是否已经提交标识设置为true
//                        return true;//返回true让表单正常提交
//                    } else {
//                        return false;//返回false那么表单将不提交
//                    }
                    var error = $('.has-error').length;
                    if (error >= 1) {
                        return false;
                    }
                },
                complete: function () {
                },
                success: function (data) {
                    if (data.errorCode == 0) {
                        //$('#progressWizard').find('li.border-none').eq(2).find('a').click();
                        showInfo('保存成功', '180px');
                    } else {
//                        isCheckCommitted = false
                        if (data.msg.attr) {
                            _self.showError(data.msg.key, data.msg.attr, data.msg.msg);
                        } else {
                            showInfo('操作失败', '250px', 2);
                        }

                    }
                },
            });
        },
        showError: function (key, sel, msg) {
            var message = [msg];
            var k = key;
            $('.' + sel).each(function (i, el) {
                if (k == i) {
                    if (sel == 'child-assesment-time' || sel == 'child-fall-time') {
                        _self.saveError($(this), message, 2);
                    } else {
                        _self.saveError($(this), message);
                    }

                }
            })
        },
    };
    return main;
});

