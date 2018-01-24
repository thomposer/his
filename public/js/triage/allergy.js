define(function (require) {
    var allergyContentTpl = require('tpl/allergyContent.tpl');
    var template = require('template');
    var _self;
    var main = {
        init: function () {
            this.bindEvent();
            _self = this;
        },
        bindEvent: function () {
            $('body').off('click', '.have-allergy').on('change', '.have-allergy', function () {
                if ($(this).val() == 1) {
                    $('.allergy-content').hide();
                } else {
                    var parentObj = $(this).parents('.allergy-form');
                    if (parentObj.find('.allergy-line').length == 0) {
                        var key = 0;
                        _self.appendAllergyLine(parentObj,key);
                    } else {
                        parentObj.find('.allergy-content').show();
                    }
                }
            });

//            $('body').off('change','#allergyoutpatient-type').on('change','#allergyoutpatient-type',function(){
//                if($(this).val() != 0){
//                    var content = $(this).find("option:selected").text();
//                    $(this).parents('.allergy-line').find('#allergyoutpatient-allergy_content').val(content);
//                }else{
//                    $(this).parents('.allergy-line').find('#allergyoutpatient-allergy_content').val('');
//                }
//                        
//            });

            $('body').off('click', '.allergy-add').on('click', '.allergy-add', function () {
                var parentObj = $(this).parents('.allergy-form');
                var key = parseInt($(this).attr('data-key')) + 1;
                _self.appendAllergyLine(parentObj,key);
            });
            $('body').off('click', '.allergy-delete').on('click', '.allergy-delete', function () {
                var parentObj = $(this).parents('.allergy-form');
                $(this).parents('.allergy-line').remove();
                
                if (parentObj.find('.allergy-line').length == 0) {
                    //选中无
                    $('.have-allergy').first().prop('checked', 'checked');
                }
                _self.dealWithSymbol(parentObj);
                var len = parentObj.find('.allergy-line').length;
                if (len == 1) {
                    parentObj.find('.allergy-delete').hide();
                }
            });

            $('body').off('click', '#recordForm #select-first-check,#recordForm .CheckCodeSel2').on('click', '.first-check-input-container', function () {
                $('#recordForm .first-check-error').remove();
            });
            
            $('body').off('click', '#childForm #select-first-check,#childForm .CheckCodeSel2').on('click', '.first-check-input-container', function () {
                $('#childForm .first-check-error').remove();
            });

            $('#recordForm').yiiAjaxForm({
                beforeSend: function () {
                    var status = 0;
                    var length = 0;
                    if ($('#recordForm input[name="OutpatientRelation[hasAllergy]"]:checked').val() == 2) {
                        var parentObj = $('#recordForm').find('.allergy-form');
                        status = _self.allergyValidity(parentObj);
                    }

//                    $('#recordForm .first-check-list').each(function () {
//                        if ($(this).css('display') != 'none') {
//                            length = length + 1;
//                        }
//                    });
                    $('#recordForm .select-first-check').each(function () {
                        var val = $(this).val();
                        if (val == 1) {//icd-10
                            var contentId = $(this).parents('.first-check-type').siblings('.first-check-text').find('.CheckCodeSel').val();
                            console.log(contentId, 'contentId');
                            if (contentId == 0) {
                                console.log('haha');
                                $(this).parents('.first-check-type').siblings('.first-check-text').find('.first-check-right').addClass('has-error').find('.help-block:last').text('初步诊断不能为空');
                                status = 1;
                            }
                        } else {
                            var contentText = $(this).parents('.first-check-type').siblings('.first-check-text').find('.first-check-custom').val();
                            console.log(contentText, 'contentId');
                            if (contentText == '') {
                                console.log('haha');
                                $(this).parents('.first-check-type').siblings('.first-check-text').find('.first-check-right').addClass('has-error').find('.help-block:last').text('初步诊断不能为空');
                                status = 1;
                            }
                        }
                    });
//                    if(length == 0 && ordersStatus){
//                        status = true;
//                        $('#recordForm .first-check-right').find('.first-check-error').remove();
//                        $('#recordForm .first-check-right').append('<div class="first-check-error" style="padding-top: 3px;color: #ff4b00;">已开医嘱，初步诊断不能为空</div>');
//                    }
                    if (status) {
                        return false;
                    }
                },
                complete: function () {


                },
                success: function (data) {
                    if (data.errorCode != 0) {
                        showInfo(data.msg, '200px', 2);
                    }
                },
            });
            $('#childForm').yiiAjaxForm({
                beforeSend: function () {
                    var status = 0;
                    var length = 0;
                    if ($('#childForm input[name="ChildExaminationGrowth[hasAllergy]"]:checked').val() == 2) {
                        var parentObj = $('#childForm').find('.allergy-form');
                        status = _self.allergyValidity(parentObj);
                    }
                    
                    $('#childForm .select-first-check').each(function () {
                        var val = $(this).val();
                        if (val == 1) {//icd-10
                            var contentId = $(this).parents('.first-check-type').siblings('.first-check-text').find('.CheckCodeSel').val();
                            if (contentId == 0) {
                                $(this).parents('.first-check-type').siblings('.first-check-text').find('.first-check-right').addClass('has-error').find('.help-block:last').text('初步诊断不能为空');
                                status = 1;
                            }
                        } else {
                            var contentText = $(this).parents('.first-check-type').siblings('.first-check-text').find('.first-check-custom').val();
                            if (contentText == '') {
                                $(this).parents('.first-check-type').siblings('.first-check-text').find('.first-check-right').addClass('has-error').find('.help-block:last').text('初步诊断不能为空');
                                status = 1;
                            }
                        }
                    });
                    
                    if (status) {
                        return false;
                    }
                },
                complete: function () {


                },
                success: function (data) {
                    if (data.errorCode != 0) {
                        showInfo(data.msg, '200px', 2);
                    }
                },
            });
            $('#j_tabForm_4').yiiAjaxForm({
                beforeSend: function () {
                    var status = 0;
                    if ($('#j_tabForm_4 input[name=\"allergyOutpatient[haveAllergyOutpatient]\"]:checked').val() == 2) {
                        var parentObj = $('#j_tabForm_4').find('.allergy-form');
                        status = _self.allergyValidity(parentObj);
                    }
                    if (status) {
                        return false;
                    }
                },
                complete: function () {
                },
                success: function (data) {
                    if (data.errorCode == 0) {
                        showInfo('保存成功', '180px');
                    } else {
                        showInfo(data.msg, '180px', 2);
                    }
                },
            });
        },
        appendAllergyLine: function (parentObj,key) {
            var html = template.compile(allergyContentTpl)({
                key: key,
            });
            parentObj.find('.allergy-content').append(html);

            _self.dealWithSymbol(parentObj);
        },
        dealWithSymbol: function (parentObj) {
            parentObj.find('.allergy-add').hide();
            parentObj.find('.allergy-delete').show();
            parentObj.find('.allergy-delete').last().show();
            parentObj.find('.allergy-add').last().show();
        },
        allergyValidity: function (parentObj) {
            var status = 0;
            parentObj.find('.allergy-line').each(function () {
                var allergyType = $(this).find('#allergyoutpatient-type');
                var allergyContent = $(this).find('#allergyoutpatient-allergy_content');
                if (allergyType.val() == '') {
                    showAllergyValidity(allergyType, "过敏史类型不能为空！");
                    status = 1;
                }
                if (allergyContent.val() == '') {
                    showAllergyValidity(allergyContent, "名称不能为空！");
                    status = 1;
                }
            });
            return status;
        },
        initAllergyBtn: function (parentObj) {
            parentObj = parentObj || '.allergy-content';
            console.log(parentObj, 'par');
            var len = $(parentObj).find('.allergy-line').length;
            console.log(len, 'len');
            if (len >= 2) {
                $(parentObj + " .allergy-delete").show();
                $(parentObj + " .allergy-add").hide();
                $(parentObj + ' .allergy-add').last().show();
            } else {
                $(parentObj + " .allergy-delete").first().hide();
                $(parentObj + ' .allergy-add').last().show();
            }
        },
    };
    return main;
});

