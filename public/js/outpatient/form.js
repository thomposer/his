define(function (require) {
    var template = require('template');
    var migrate = require('js/lib/jquery-migrate-1.1.0');
    var jqprint = require('js/lib/jquery.jqprint-0.3');
    var cureTpl = require('tpl/outpatient/cure.tpl');
    var checkTpl = require('tpl/outpatient/check.tpl');
    var checkNewTpl = require('tpl/outpatient/checkNew.tpl');
    var inspectTpl = require('tpl/outpatient/inspect.tpl');
    var patientInfoTpl = require('tpl/patientInfo.tpl');
    var prinkTpl = require('tpl/prink.tpl');
    var cureprinkTpl=require('tpl/cureprink.tpl');
    var recipeprinkTpl = require('tpl/recipeprink.tpl');
    var materialprinkTpl = require('tpl/materialprink.tpl');
    var consumablesprinkTpl = require('tpl/consumablesprink.tpl');
    var inspectprinkTpl = require('tpl/inspectprink.tpl');
    var checkprinkTpl = require('tpl/checkprink.tpl');
    var childprinkTpl = require('tpl/childPrint.tpl');
    var nursingRecordPrinkTpl = require('tpl/nursingRecordPrint.tpl');
    var healthEducation = require('tpl/healthEducation.tpl');
    var inspectApplicationPrintTpl = require('tpl/inspect/inspectApplicationPrint.tpl');
    var checkApplicationPrintTpl = require('tpl/check/checkApplicationPrint.tpl');
    var materialTpl = require('tpl/outpatient/material.tpl');
    var consumablesTpl = require('tpl/outpatient/consumables.tpl');
    var treeview = require('js/bootstrap/bootstrap-treeview.min');
    var tooth = require('js/outpatient/tooth');
    var choseTemplate = require('js/outpatient/choseTemplate');
    var allergy = require('js/triage/allergy');
    var firstCheck = require('js/outpatient/firstCheck');
    var recipePrint=require('js/outpatient/recipePrint');
    var checkCloseType = 1;
    var isInTemplateMenu = false;
    var _self;
    var firstCheckMaxLen = 30;
    var cureFirstReload = 0;
    var reportFirstReload = 0;

    var main = {
        init: function () {
            _self = this;
            
            this.bindEvent();
            this.addPrintDom();
            this.recipeBack();
            this.checkBtnState();
            tooth.init();
            choseTemplate.init();
            choseTemplate.initTemplateMenul();
            allergy.init();
//            orthodontics.init(allergy);
            firstCheck.init();
            //this.checkBtnRecipeState();
            this.addItemButton();
        },
        checkBtnState: function () {
            if ($('.recipe-check:checked').length > 0) {
                $('.btn-recipe-back').find('button').attr('disabled', false);
                $('.btn-recipe-back').find('button').attr('style', 'background:#76A6EF');
            } else {
                $('.btn-recipe-back').attr('disabled', true);
                $('.btn-recipe-back').attr('style', 'background:#BAD2F7');
            }
        },
        addItemButton: function () {
            var a = '';
            a += '<span class="add-cure-idea" style="float: right">';
            a += '<botton type="button" class="add-cure-idea-button">导入处方</botton>';
            a += '</span>';

            var b = '';
            b += '<span class="add-check-inspect" style="float: right">';
            b += '<botton type="button" class="add-check-inspect-button">导入检查结果</botton>';
            b += '</span>';

            $('.field-cure_idea .cure_idea_label').after(a);
            $('.field-examination_check .examination_check_label').after(b);
            if (has_save == 1 && state == 1) {
                $('.add-cure-idea').hide();
                $('.add-check-inspect').hide();
            } else if (getUrlParam('case_id') == null && state == 1) {
                $('.add-cure-idea').hide();
                $('.add-check-inspect').hide();
            }
        },
        addPrintDom: function () {
            var a = '';
            a += '<div id="growth_print" class="common-print-container" style="display: none;"></div>';
            $('.wrapper').after(a);
        },
        bindEvent: function () {
            template.config(escape, false);
            if (2 == recordType && window.location.hash == '') {
                window.location.hash = '#childCheck_suffix';
            }
            var pre_hash = window.location.hash.split('_', 1);
            var hash = window.location.hash ? pre_hash[0] : '#record';
            _self.addPatientInfo();//患者个prev人信息卡片
            _self.cureEdit();//治疗
            _self.inspectEdit();//实验室检查
            _self.checkEdit();//辅助检查
            _self.materialEdit();//治疗
            _self.consumablesEdit();//治疗
            _self.changeTemplate();
            _self.materialPrint();
            _self.consumablesPrint();
            _self.choseChildTemplate();
            _self.createFollow();
            console.log(state,'state');
            if (state != 0) {
                $('#record .kv-file-remove').hide();
                $('#record .input-group-btn').hide();
            }

            //儿童体检-收起所有项
            $('.check-close').on('click', function () {
                if (checkCloseType == 1) {
                    checkCloseType = 2;
                    $(this).html('展开所有项<i class="fa fa-angle-down pull-right angle-icon"></i>');
                    $('.checkContent').hide();
                } else {
                    checkCloseType = 1;
                    $(this).html('收起所有项<i class="fa fa-angle-up pull-right angle-icon"></i>');
                    $('.checkContent').show();
                }
            });
            //儿童体检-体格检查
            $('#checkSelect').on('click', function () {
                var value = $(this).attr("checked");
                if (value == 'checked') {
                    $(".checkContent input:radio[value=1]").attr("checked", true);
                } else {
                    $(".checkContent input:radio").removeAttr("checked");
                }
            });
            $('.checkContent input[type="radio"]').on('change', function () {
                var value = $(this).val();
                if (value != 1) {
//                    $(this).parents('.select-option').siblings('.option-remark').removeClass('hide');
                    var check = $('#checkSelect').attr("checked");
                    if (check == 'checked') {
                        $('#checkSelect').attr({'checked': false});
                    } else {
                        $('#checkSelect').attr({'checked': false});
                    }
                } else {
//                    $(this).parents('.select-option').siblings('.option-remark').addClass('hide');
                    main.is_check();
                }
            })

            //存为病例模板 回填 过去用药史 和 随诊
            console.log(dentalRecordStatus, 'dentalRecordStatus');
            console.log(recordType, 'recordType');
            if (state == 1 || dentalRecordStatus == 1) {
                if (recordType == 3) {
                    if (dentalRecordStatus == 1) {
                        $('#record .form-control').attr({'disabled': true});
                        $('#record [type=radio]').attr({'disabled': true});
                        $('.btn-case-template').hide();
                        $('.btn-rebate').show();
                    } else {
                        $('#record .form-control').attr({'disabled': false});
                        $('[type=radio]').attr({'disabled': false});
                        $('.btn-rebate').hide();
                    }
                } else if (hasTemplateCase == 2 || has_save == 1) {
                    $('#record .form-control').attr({'disabled': true});
                    $('#record [type=radio]').attr({'disabled': true});
                    $('.btn-case-template').hide();
                    $('.btn-from-delete-add').hide();
                    $('.btn-rebate').show();
                } else {
                    $('#record .form-control').attr({'disabled': false});
                    $('[type=radio]').attr({'disabled': false});
                    $('.btn-case-template').show();
                    $('.btn-rebate').hide();
                    $('#record .kv-file-remove').show();
                    $('#record .input-group-btn').show();
                    $('.btn-from-delete-add').show();

                    //过敏史加减号
//                    $('#record .allergy-content .allergy-add').hide();
//                    $('#record .allergy-content .allergy-delete').show();
//                    $('#record .allergy-content .allergy-delete').last().show();
//                    $('#record .allergy-content .allergy-add').last().show();
                    allergy.initAllergyBtn();
                    firstCheck.initFirstCheckBtn();
                }

            }else if(recordType == 6 || recordType == 7){
            	//暂时先不处理
            } else {
                $('#record .form-control').attr({'disabled': false});
                $('[type=radio]').attr({'disabled': false});
                $('.allergy').show();
                $('.allergy-add').show();
                $('.btn-rebate').hide();
                allergy.initAllergyBtn();
                firstCheck.initFirstCheckBtn();
            }
            $('a[href="' + hash + '"]').click();
            
            $('ul.nav-tabs>li>a').on('click', function () {
                var suffix = '_suffix';
                var href = $(this).attr('href');
                window.location.hash = href + suffix;
                hash = href;
                if (hash == '#report') {
                	if(reportFirstReload == 1){
       			     	$.pjax.reload({container:'#reportPjax',url:reportRecordUrl,push:false,replace:false,scrollTo:false,cache:false,timeout:5000});  //Reload
                	}
                    reportFirstReload = 1;
                }

                if (hash == '#cure') {
                	
                    $('#cure-template-select-ui').hide();
                    if(cureFirstReload == 1){
       			     	$.pjax.reload({container:'#curePjax',url:cureRecordUrl,push:false,replace:false,scrollTo:false,cache:false,timeout:5000});  //Reload
                    }
                    cureFirstReload = 1;
                }
                

            });
     
            $('.nav-tabs').find('li').removeClass('active');
            $('a[href="' + hash + '"]').parent('li').addClass('active');
            $(hash).siblings('.tab-pane').removeClass('active');
            $(hash).addClass('active');
            
            //若为儿童体检，则在表单的url后面插入锚点
            var childFormAction = $('form#childForm').attr('action');

            $('form#childForm').attr({'action': childFormAction + '#childCheck_suffix'});

            if (!$('div.patient-form>ul.nav-second').find('li').hasClass('active')) {
                $('div.patient-form>ul.nav-second>li').first().addClass('active');
                $('div.patient-form').find('.tab-content').find('.tab-pane').removeClass('active').first().addClass('active');
            }
            if (hash.indexOf('report') != -1) {

                hash = '#report';
                $('a[href="' + hash + '"]').parent('li').addClass('active');
                $(hash).siblings('.tab-pane').removeClass('active');
                $(hash).addClass('active');
            }

            $('body').on('click', '.print-check', function () {
                var id = $(this).attr('name');
                if (hash == '#cure') {
                   // _self.CureRecordPrint(id);//治疗打印
                } else if (hash == '#recipe') {
                    // _self.RecipeRecordPrint(id);//处方打印
                } else if (id.substring(0, 7) == 'Inspect') {
                    _self.InspectReportPrint(id);//实验室检查报告打印
                } else if (id.substring(0, 5) == 'Check') {
                    _self.CheckReportPrint(id);//影像学检查报告打印
                } else if (hash == '#labCheck') {
                    //实验室检验申请单打印
                } else if (hash == '#auxiliary') {
                    //影像学检验申请单打印
                } else if (hash == '#childCheck') {
                    _self.ChildExamination(id);
                } else {
                    _self.RecordPrint(id);//病历库打印
                }
            });

            //实验室检查申请打印
            $('body').on('click', '.btn-inspect-application-print', function () {
                var id = $(this).attr('name');
                var inspect_value = [];
                $('input[name="Inspect[onInspect][]"]:checked').each(function () {
                    inspect_value.push($(this).val());
                });
                if (inspect_value.length == 0) {
                    showInfo('请勾选要打印的项目', '180px', 2);
                    return;
                }
                $.ajax({
                    type: 'post',
                    url: inspectApplicationPrintUrl,
                    data: {
                        'inspect_id': inspect_value,
                        'record_id': record_id,
                        'need_filter': 1,
                    },
                    dataType: 'json',
                    success: function (json) {
                        if (json['errorCode'] == 1001) {
                            showInfo(json['msg'], '180px', 2);
                            return;
                        }
                        var spotInfo = json['spotInfo'];
                        var recipeInfo = json['recipeInfo'];
                        var inspectApplication = json['inspectApplication'];
                        var inspectTotalPrice = json['inspectTotalPrice'];
                        var inspectTime = json['inspectTime'];
                        var spotConfig = json['spotConfig'];
                        var logo_img = '';
                        if (spotConfig.logo_shape == 1) {
                            logo_img = "clinic-img"
                        } else {
                            logo_img = "clinic-img-long"
                        }
                        var inspectApplicationPrint = template.compile(inspectApplicationPrintTpl)({
                            triageInfo: triageInfo,
                            spotInfo: spotInfo,
                            recipeInfo: recipeInfo,
                            inspectApplication: inspectApplication,
                            inspectTotalPrice: inspectTotalPrice,
                            inspectTime: inspectTime,
                            record_id: record_id,
                            cdnHost: cdnHost,
                            baseUrl: baseUrl,
                            spotConfig: spotConfig,
                            logo_img: logo_img,
                        });
                        $('#inspect-application-print').html(inspectApplicationPrint);
                        $('#' + id).jqprint();
                    },
                    error: function () {
                    },
                });
            });

            //影像学检查申请打印
            $('body').on('click', '.btn-check-application-print', function () {
                var id = $(this).attr('name');
                var check_value = [];
                $('input[name="Check[check][]"]:checked').each(function () {
                    check_value.push($(this).val());
                });
                if (check_value.length == 0) {
                    showInfo('请勾选要打印的项目', '180px', 2);
                    return;
                }
                $.ajax({
                    type: 'post',
                    url: checkApplicationPrintUrl,
                    data: {
                        'check_id': check_value,
                        'record_id': record_id,
                    },
                    dataType: 'json',
                    success: function (json) {
                        if (json['errorCode'] == 1001) {
                            showInfo(json['msg'], '180px', 2);
                            return;
                        }
                        var spotInfo = json['spotInfo'];
                        var recipeInfo = json['recipeInfo'];
                        var checkApplication = json['checkApplication'];
                        var checkTotalPrice = json['checkTotalPrice'];
                        var checkTime = json['checkTime'];
                        var spotConfig = json['spotConfig'];
                        var logo_img = '';
                        if (spotConfig.logo_shape == 1) {
                            logo_img = "clinic-img"
                        } else {
                            logo_img = "clinic-img-long"
                        }
                        var checkApplicationPrint = template.compile(checkApplicationPrintTpl)({
                            triageInfo: triageInfo,
                            spotInfo: spotInfo,
                            recipeInfo: recipeInfo,
                            checkApplication: checkApplication,
                            checkTotalPrice: checkTotalPrice,
                            checkTime: checkTime,
                            record_id: record_id,
                            cdnHost: cdnHost,
                            baseUrl: baseUrl,
                            spotConfig: spotConfig,
                            logo_img: logo_img,
                        });
                        $('#check-application-print').html(checkApplicationPrint);
                        $('#' + id).jqprint();
                    },
                    error: function () {
                    },
                });
            });

            //治疗打印
            $('body').on('click', '.btn-cure-application-print', function () {
                var id = $(this).attr('name');
                var cure_value = [];
                $('input[name="Cure[cure][]"]:checked').each(function () {
                    cure_value.push($(this).val());
                });
                if (cure_value.length == 0) {
                    showInfo('请勾选要打印的项目', '180px', 2);
                    return;
                }
                    main.CureRecordPrint(id,cure_value);
            });

            //病例选择医嘱确认打印
            $('body').on('click', '.btn-recipe-check-application-print', function () {
                var check_value = [];
                $('input[name="RecipeRecord[name][]"]:checked').each(function () {
                    check_value.push($(this).val());
                });

                if (check_value.length == 0) {
                    showInfo('请勾选要打印的项目', '180px', 2);
                    return;
                }
                var filterType = $(this).attr('data-type');
                var options = {
                    getCureRecord: getCureRecord,
                    getStatusOtherDesc: getStatusOtherDesc,
                    cdnHost: cdnHost,
                    baseUrl: baseUrl,
                    filterType: filterType,
                };
                recipePrint.print(record_id, check_value, recipePrintUrl, options);
            });

            //治疗意见导入处方模板
            $('body').on('click', '.add-cure-idea-button', function () {
                var c = '';
                c = $('#cure_idea').val();
                $.ajax({
                    type: 'post',
                    url: outpatientDoctorRecipeList,
                    data: {
                        'record_id': record_id,
                    },
                    dataType: 'json',
                    success: function (json) {
                        if (json['errorCode'] != 0) {
                            showInfo(json['msg'], '180px', 2);
                            return;
                        }
                        var recipeList = json.recipeList;
                        var b = '';
                        for (var i = 0; i < recipeList.length; i++) {
                            b += recipeList[i].name + recipeList[i].specification + '　' + recipeList[i].dose + recipeList[i].dose_unit + '　' + recipeList[i].used + '　' + recipeList[i].frequency + '　' + recipeList[i].day + '天' + '　' + recipeList[i].num + recipeList[i].unit;
                            if (i != recipeList.length - 1) {
                                b += "\n";
                            }
                        }
                        var cureText = c ? c + "\n" + b : c + b;
                        $('#cure_idea').val(cureText);
                    },
                    error: function () {
                    },
                });
            });

            //实验室及影像学检查导入检查项目
            $('body').on('click', '.add-check-inspect-button', function () {
                var c = '';
                c = $('#examination_check').val();
                $.ajax({
                    type: 'post',
                    url: outpatientDoctorCheckInspectList,
                    data: {
                        'record_id': record_id,
                    },
                    dataType: 'json',
                    success: function (json) {
                        if (json['errorCode'] != 0) {
                            showInfo(json['msg'], '360px', 2);
                            return;
                        }

                        //影像学检查内容
                        var checkList = json.data.check;
                        var checkText = '';
                        if (checkList) {
                            for (var i = 0; i < checkList.length; i++) {
                                checkText += checkList[i].name;
                                if (i != checkList.length - 1) {
                                    checkText += "\n";
                                }
                            }
                        }
                        //实验室检查内容
                        var inspectUnionList = json.data.inspectUnion;
                        var inspectList = json.data.inspect;
                        var inspectText = '';
                        if (inspectUnionList) {
                            for (var m in inspectUnionList) {
                                var d = '';
                                for (var n in inspectUnionList[m]) {
                                    d += inspectUnionList[m][n].name + '　' + inspectUnionList[m][n].result + '　' + inspectUnionList[m][n].unit + '、';
                                }
                                d = d.substring(0, d.length - 1);
                                inspectText += inspectList[m].name + '　' + d + "\n";

                            }
                        }
                        //影像学检查内容为空时不添加换行
                        if (checkText == '') {
                            inspectText = inspectText.substring(0, inspectText.length - 1);
                        }
                        var checkInspectText = inspectText + checkText;
                        var allText = c ? c + "\n" + checkInspectText : c + checkInspectText;
                        $('#examination_check').val(allText);
                    },
                    error: function () {
                    },
                });
            });


            if (childCheckStatus == 1) {
                $('#childForm .form-control').attr({'disabled': true});
                $('#childForm [type=checkbox]').attr({'disabled': true});
                $('#childForm [type=radio]').attr({'disabled': true});
                $('#childForm .btn-from-delete-add').hide();
            }

            if (dentalRecordStatus == 1 && getUrlParam('dental_case_id') == null) {
                $('#dental-history .form-control').attr({'disabled': true});
                $('#dental-history [type=radio]').attr({'disabled': true});
                $('#dental-history [type=checkbox]').attr({'disabled': true});
                $('#dental-history .add-booth-button').hide();
                $('#dental-history .btn-from-delete-add').hide();
                $('#dental-history .dental-check .control-label').removeClass('dental-check-title');
                $('#dental-history .dental-check .control-label .dentail-content-edit').hide();
            } else if (dentalRecordStatus == 1 && getUrlParam('dental_case_id') != null) {
                dentalRecordStatus = 2;
                $('#dental-history .form-control').attr({'disabled': false});
                $('#dental-history [type=radio]').attr({'disabled': false});
                $('#dental-history [type=checkbox]').attr({'disabled': false});
                $('#dental-history .add-booth-button').show();
                $('#dental-history .btn-from-delete-add').show();
                $('#dental-history .dental-check .control-label').addClass('dental-check-title');
                $('#dental-history .dental-check .control-label .dentail-content-edit').show();
                allergy.initAllergyBtn();
                firstCheck.initFirstCheckBtn();
            }
//            $('#record .btn-from-delete-add').hide();


            $('body').on('click', 'button[type=button]:not(.inspect-back):not(.recipe-back):not(.print-check):not(.btn-cancel):not(.close):not(.btn-click):not(.btn-case-template):not(.kv-file-zoom):not(.kv-file-download):not(.check-close):not(.close-btn-style):not(.btn-inspect-application-print):not(.btn-check-application-print):not(.print-nursing-record):not(.prink-material-record):not(.teeth-print):not(.btn-first-check):not(.popup-btn-first-check):not(.save-birth-more):not(.orthodontics-returnvisit-print)', function () {
                var name = $(this).attr('name');
                var title = $(this).attr('title');
                var count = $(this).find('i');
                if (hash == '#record') {//治疗
                    if (typeof (name) == "undefined") {
                        allergy.initAllergyBtn();
                        firstCheck.initFirstCheckBtn();
//                        $('.first-check-delete').show();
//                        $('.first-check-add').last().show();
//                        $('.allergy-content').each(function () {
////                            $(this).find('.btn-from-delete-add').show();
//                        });
                    }
                    $('.case_template_none').show();
                    $('.add-cure-idea').show();
                    $('.add-check-inspect').show();
                    $('.first-check-input-container').show();

                    $('.edit_button').hide();
                    $('#orthodonticsReturnvisitForm .orthodontics-returnvisit-print').hide();
                    
//                    $('.add-cure-idea-button').hide();
                }

                if (typeof (name) == "undefined" && count.length == 0) {
                    $(hash + ' .form-control').attr({'disabled': false});
                    $(hash + ' [type=radio]').attr({'disabled': false});
                    $(hash + ' [type=checkbox]').attr({'disabled': false});
//                    $(hash+' .btn-from-delete-add').show();
                    if (hash == '#record' && count.length == 0) {
                        $('.btn-case-template').show();
                    }

                    if (hash == '#record' && count.length == 0) {
                        dentalRecordStatus = 2;
                        $(hash + ' .reocrd-btn-custom').html('保存');
                        $('.print-teeth-img').hide();
                        $('.print-teeth-record').hide();
                        $('.print-orthodontics-record').hide();
                        $('#dental-history .add-booth-button').show();
                        $('#dental-history .btn-from-delete-add').show();
                        $('#dental-history .allergy-content .allergy-line-button').show();
                        $('#dental-history .dental-check .control-label').addClass('dental-check-title');
                        $('#dental-history .dental-check .control-label .dentail-content-edit').show();
                        $('#orthodonticsFirstRecord .dental-check .control-label').addClass('dental-check-title');
                        $('#orthodonticsFirstRecord .dental-check .control-label .dentail-content-edit').show();
                        $('#record .input-group-btn').show();
                        $('#record .kv-file-remove').show();
                        $('.teeth-print').hide(); //口腔打印按钮隐藏

                        allergy.initAllergyBtn();
                        firstCheck.initFirstCheckBtn();
                        if(recordType == 6){
                            orthodonticsJs.initInput();
                        }
                    } else if (hash != '#childCheck') {
                        if (typeof (title) == "undefined") {
                            $(hash + ' button:first').html('保存');
                        }
                    } else if (hash != 'material' && hash != '#childCheck') {
                        if (typeof (title) == "undefined") {
                            $(hash + ' button:first').html('保存');
                        }
                    }

                    if (hash.substring(0, 7) != '#report') {
                        $(hash + ' .print-check').attr({"disabled": "disabled"});
                        $(hash + ' .print-check').addClass("btn-readonly");

                        $(hash + ' .recipe-back').attr({"disabled": "disabled"});
                        $(hash + ' .recipe-back').addClass("btn-readonly");

                        $(hash + ' .inspect-back').attr({"disabled": "disabled"});
                        $(hash + ' .inspect-back').addClass("btn-readonly");
                    }
                }
                if (hash == '#childCheck' && childCheckStatus == 1) {
                    $('#childForm .form-control').attr({'disabled': false});
                    $('.childFormButton .child-print').html('保存');
                    $('#childForm [type=checkbox]').attr({'disabled': false});
                    $('#childForm .btn-from-delete-add').show();
                    allergy.initAllergyBtn('#childForm');
                    firstCheck.initFirstCheckBtn();
                    main.is_check();
                    $('#checkDiv').show();
                    setTimeout(function () {
                        $('.childFormButton button').attr({'type': 'submit'});
                    }, 0);

                }
                if (hash == '#cure' && typeof (name) == "undefined") {//治疗
                    $('.cure-form td>span').html('');
                    $('.cure-form td>input.form-control').attr({'type': 'text'});
                    $('.cure-record-form').show();
                    $('.cure-form .op-group').show();
                    $('.cure-form .action-column').show();
                    $('.cure-form .status-column').show();
                    $('#cure-template-select-ui').show();
                    $('#cure-template-select').hide();
                    _self.cureSelect2();//初始化搜索

                }
                if (hash == '#material' && typeof (name) == "undefined") {//其他非药品

                    $('.material-form td>span').html('');
                    $('.material-form td>input.form-control').attr({'type': 'text'});
                    $('.field-materialrecord-materialname').show();
                    $('.material-form .action-column').show();
                    $('.material-form .op-group').show();
                    $('#material .print-material').hide();
                    _self.materialSelect2();

                }
                if (hash == '#consumables' && typeof (name) == "undefined") {//医疗耗材

                    $('.consumables-form td>span').html('');
                    $('.consumables-form td>input.form-control').attr({'type': 'text'});
                    $('.field-consumablesrecord-consumablesname').show();
                    $('.consumables-form .action-column').show();
                    $('.consumables-form .op-group').show();
                    $('#consumables .print-consumables').hide();
                    _self.consumablesSelect2();//初始化搜索
                }
                if (hash == '#recipe' && typeof (name) == "undefined") {//处方
                    $('.field-reciperecord-recipename').show();
                    $('#recipe-template-select-ui').show();
                    $('#recipe-template-select').hide();
                    $('.recipe-form td').each(function () {

                        var unit = $(this).attr('id');
                        var type = $(this).attr('data-type');
                        if (unit != 'unit') {
                            if (type != 'recipeName') {
                                $(this).find('span:not(.skin-test-no-empty)').not('.recipeOutData').html('');
                            }

                        } else {
                            $('#num').remove();
                        }

                    });
                    $('.desc-td').attr({'colspan': 4});
                    $('.recipe-form td>div.hidden').removeClass('hidden');
//                    $('.recipe-form td>div>label.skinTestContent').show();
                    $('.recipe-form td>input.cure-skin-select').show();
                    $('.recipe-form td>label.skin-test-content').show();
                    $('.recipe-form td>input.form-control').attr({'type': 'text'});
                    $('.recipe-form td>select.form-control').show();
                    $('.recipe-form .op-group').show();
                    $('.status-recipe-column').show();
                    $('.no-need-skin-test').hide();
                    mainHighRisk.bind();

                }
                if (hash == '#labCheck') { //实验室检查中单击修改按钮
                    $('.inspectRecordForm .control-label').removeClass('hide');
                    $('.inspect-form .header th').removeClass('action-column');
                    $('.inspect-form .op-group').show();
                    $('.field-inspectrecord-inspectname').show();
                    $('#inspect-template-select-ui').show();
                    $('#inspect-template-select').hide();
                    _self.inspectSelect2();//初始化搜索
                }

                if (hash == '#auxiliary') { //影响学检查中单击修改按钮
                    $('.check-form .op-group').show();
                    $('.check-form .header th').removeClass('action-column');
                    $('.field-checkrecord-checkname').show();
                    $('#check-template-select-ui').show();
                    $('#check-template-select').hide();
                    _self.checkSelect2();//初始化搜索
                }



                if (typeof (name) == "undefined") {//治疗
                    setTimeout(function () {
                        if (hash == '#record' && count.length == 0) {
                            $(hash + ' .reocrd-btn-custom').attr({'type': 'submit'});
                        } else if (hash != '#childCheck') {
                            if (typeof (title) == "undefined") {
                                $(hash + ' button:first').attr({'type': 'submit'});
                            }
                        }
                    }, 0);
                }


            });
            //判断皮试的选择
            $(document).on('change', '.skinTestStatus', function () {
                var id = $(this).val();
                if (id == 1) {
                    $(this).parent().parent().find('.skin-test-status').show();
                } else {
                    $(this).parent().parent().find('.skin-test-status').hide();
                }
            });


            //删除附件
            $('body').on('click', '.kv-file-remove', function () {
                var url = $(this).data('url');
                var key = $(this).data('key');
                var isNew = $(this).data('new');
                if (url && key && isNew == 1) {
                    $.ajax({
                        cache: true,
                        type: "POST",
                        url: url,
                        data: {
                            key: key,
                        }, // 你的formid
                        dataType: 'json',
                        async: false,
                        success: function (data, textStatus, jqXHR) {
                            if (data.errorCode == 0) {

                            }

                        },
                        error: function () {
                            showInfo('系统异常,请稍后再试', '180px', 2);
                        }
                    });
                }
            });

            

            $('body').off('click', '.kv-file-download').on('click', '.kv-file-download', function () {
                var src = $(this).parents('.file-thumbnail-footer').siblings('.kv-file-content').children('.kv-preview-data').attr('src');
                window.open(src);
            });

            jsonFormInit = $("form").serialize();//为了表单验证



            $('body').off('click', '.box-detail').on('click', '.box-detail', function () {
                $('.box-detail').each(function () {
                    $(this).removeClass('box-active');
                });
                $(this).addClass('box-active');
            });

            $('body').off('click', '.btn-box-tool').on('click', '.btn-box-tool', function () {
                if ($(this).attr('data-value') == 0 || $(this).attr('data-value') === undefined) {
                    $(this).parents('.none_radius').addClass('box-open');
                    $(this).attr('data-value', 1);
                } else {
                    $(this).parents('.none_radius').removeClass('box-open');
                    $(this).attr('data-value', 0);
                }
            });

            $('body').off('click', '.print-nursing-record').on('click', '.print-nursing-record', function () {
                var id = $(this).attr('name');
                _self.NursingRecord(id);
            });
//            $('#childCheck input[type=radio]').parent().addClass('add-padding');



            $('body').off('click', '.dental-record-type input').on('click', '.dental-record-type input', function () {
                var radioValue = $(this).val(), localUrl = location.href;
                localUrl = localUrl.replace(/recordType=1&/, '');
                localUrl = localUrl.replace(/recordType=2&/, '');
                console.log(radioValue);
                if (radioValue == 1) {
                    localUrl = localUrl.replace(/\?/, '?recordType=1&');
                    window.location.href = localUrl;
                } else if (radioValue == 2) {
                    localUrl = localUrl.replace(/\?/, '?recordType=2&');
                    window.location.href = localUrl;
                }
            });

            $('body').off('click', '#recordForm #select-first-check,#recordForm .CheckCodeSel2').on('click', '.first-check-input-container', function () {
                $('#dental-history .first-check-error').remove();
            });

            $('#dental-history').yiiAjaxForm({
                beforeSend: function () {
                    var status = 0;
                    var length = 0;
                    if ($('#dental-history input[name="DentalHistory[hasAllergy]"]:checked').val() == 2) {
                        var parentObj = $('#dental-history').find('.allergy-form');
                        status = allergy.allergyValidity(parentObj);
                    }

//                    $('#dental-history .first-check-list').each(function () {
//                        if ($(this).css('display') != 'none') {
//                            length = length + 1;
//                        }
//                    });
//                    if (length == 0 && ordersStatus) {
//                        status = true;
//                        $('#recordForm .first-check-right').find('.first-check-error').remove();
//                        $('#dental-history .first-check-right').append('<div class="first-check-error" style="padding-top: 3px;color: #ff4b00;">已开医嘱，初步诊断不能为空sssssssssss</div>');
//                    }
                    $('#dental-history .select-first-check').each(function () {
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

            $('body').off('click', '.select-package').on('click', '.select-package', function () {
                if ($(this).attr('data-value') == 'false') {
                    showInfo('您还没有填写初步诊断或体重，请先填写', '300px', 2);
                }
            });
        },
        addPatientInfo: function () {
            var triageInfoModel = template.compile(patientInfoTpl)({
                list: triageInfo,
                allergy: allergy,
                baseUrl: baseUrl,
                cdnHost: cdnHost,
                apiGrowthViewUrl: apiGrowthViewUrl
            });
            $('#outpatient-patient-info').html(triageInfoModel);
        },
        //病历打印
        RecordPrint: function (id) {
            $.ajax({
                type: 'post',
                url: getDoctorRecordData,
                data: {
                    'record_id': record_id
                },
                dataType: 'json',
                success: function (json) {
                    json = json['data'];
                    var repiceInfo = json['repiceInfo'];
                    var triageInfo = json['userInfo'];
                    var spotInfo = json['spotInfo'];
                    var recipeRecordDataProvider = json['recipeRecordDataProvider'];
                    var outpatientInfo = json['outpatientInfo'];
                    var firstCheck = json['firstCheck'];
                    var allergy = json['allergy'];
                    var spotConfig = json['spotConfig'];
                    var logo_img = '';
                    if (spotConfig.logo_shape == 1) {
                        logo_img = "clinic-img"
                    } else {
                        logo_img = "clinic-img-long"
                    }
                    var prinkRecordInfoModel = template.compile(prinkTpl)({
                        spotInfo: spotInfo,
                        triageInfo: triageInfo,
                        repiceInfo: repiceInfo,
                        record_id: record_id,
                        outpatientInfo: outpatientInfo,
                        recipeRecordDataProvider: recipeRecordDataProvider,
                        firstCheck: firstCheck,
                        allergy: allergy,
                        cdnHost: cdnHost,
                        baseUrl: baseUrl,
                        spotConfig: spotConfig,
                        logo_img: logo_img
                    });
                    $('#record-print').html(prinkRecordInfoModel);
                    $('#' + id).jqprint();
                },
                error: function () {
                },
            });
        },
        //治疗打印
        CureRecordPrint:function(id,cure_value){
            var cureId = cure_value ? cure_value:[];
            $.ajax({
                type: 'post',
                url: curePrinkInfoUrl,
                data: {
                    'cureId': cureId,
                    'record_id': record_id,
                },
                dataType: 'json',
                success: function (json) {
                    var soptInfo = json['soptInfo'];
                    var recipeRecordDataProvider = json['recipeRecordDataProvider'];
                    var cureRepiceInfo = json['cureRepiceInfo'];
                    var pirntCureRecordInfo = json['pirntCureRecordInfo'];
                    var totalPrice = json['totalPrice'];
                    var firstCheck = json['firstCheck'];
                    var allergy = json['allergy'];
                    var spotConfig = json['spotConfig'];
                    var logo_img = '';
                    if (spotConfig.logo_shape == 1) {
                        logo_img = "clinic-img"
                    } else {
                        logo_img = "clinic-img-long"
                    }
                    var prinkCureRecordInfoModel = template.compile(cureprinkTpl)({
                        soptInfo: soptInfo,
                        triageInfo: triageInfo,
                        repiceInfo: cureRepiceInfo,
                        pirntCureRecordInfo: pirntCureRecordInfo,
                        record_id: record_id,
                        totalPrice: totalPrice,
                        firstCheck: firstCheck,
                        allergy: allergy,
                        cdnHost: cdnHost,
                        baseUrl: baseUrl,
                        spotConfig: spotConfig,
                        logo_img: logo_img

                    });
                    $('#cure_print').html(prinkCureRecordInfoModel);
                    $('#' + id).jqprint();
                },
                error: function () {
                },
            });
        },
        //检验报告接口
        InspectReportPrint: function (id) {
            $.ajax({
                type: 'post',
                url: reportInspectPrinkInfoUrl,
                data: {
                    'record_id': record_id,
                    'id': id.substring(7)
                },
                dataType: 'json',
                success: function (json) {

                    var spotInfo = json['spotInfo'];
                    var inspectRepiceInfo = json['inspectRepiceInfo'];
                    var inspectInfo = json['inspectInfo'];
                    var firstCheck = json['firstCheck'];
                    var allergy = json['allergy'];
                    var spotConfig = json['spotConfig'];
                    var logo_img = '';
                    if (spotConfig.logo_shape == 1) {
                        logo_img = "clinic-img"
                    } else {
                        logo_img = "clinic-img-long"
                    }
                    var prinkInspectReportInfoModel = template.compile(inspectprinkTpl)({
                        spotInfo: spotInfo,
                        triageInfo: triageInfo,
                        repiceInfo: inspectRepiceInfo,
                        inspectReportDataProvider: inspectInfo,
                        firstCheck: firstCheck,
                        allergy: allergy,
                        cdnHost: cdnHost,
                        baseUrl: baseUrl,
                        spotConfig: spotConfig,
                        logo_img: logo_img
                    });
                    $('.inspectprint').html(prinkInspectReportInfoModel);

                    $('#' + id).jqprint();
                },
                error: function () {
                },
            });
        },
        //影像报告打印
        CheckReportPrint: function (id) {
            $.ajax({
                type: 'post',
                url: reportCheckPrinkInfoUrl,
                data: {
                    'record_id': record_id,
                    'id': id.substring(5)
                },
                dataType: 'json',
                success: function (json) {

                    var spotInfo = json['spotInfo'];
                    var checkRepiceInfo = json['checkRepiceInfo'];
                    var checkInfo = json['checkInfo'];
                    var firstCheck = json['firstCheck'];
                    var allergy = json['allergy'];
                    var spotConfig = json['spotConfig'];
                    var logo_img = '';
                    if (spotConfig.logo_shape == 1) {
                        logo_img = "clinic-img"
                    } else {
                        logo_img = "clinic-img-long"
                    }
                    var prinkInspectReportInfoModel = template.compile(checkprinkTpl)({
                        spotInfo: spotInfo,
                        triageInfo: triageInfo,
                        repiceInfo: checkRepiceInfo,
                        checkReportDataProvider: checkInfo,
                        firstCheck: firstCheck,
                        allergy: allergy,
                        cdnHost: cdnHost,
                        baseUrl: baseUrl,
                        spotConfig: spotConfig,
                        logo_img: logo_img,
                    });
                    $('.checkprint').html(prinkInspectReportInfoModel);

                    $('#' + id).jqprint();
                },
                error: function () {
                },
            });
        },
        //儿童检查打印
        ChildExamination: function (id) {
            $.ajax({
                type: 'post',
                url: getChildInfoData,
                data: {
                    'record_id': record_id,
                },
                dataType: 'json',
                success: function (json) {
                    if (json['errorCode'] == 1001) {
                        showInfo('找不到该数据', '180px', 2);
                        return;
                    }
                    json = json['data'];
                    var spotInfo = json['spotInfo'];
                    var spotConfig = json['spotConfig'];
                    var triageInfo = json['userInfo'];
                    var childExaminationInfo = json['childExaminationInfo'];
                    var getSummary = json['childBasicConfig']['summary'];
                    var getCommunicate = json['childBasicConfig']['communicate'];
                    var getType = json['childBasicConfig']['type'];
                    var allergy = json['allergy'];
                    var logo_img = '';
                    if (spotConfig.logo_shape == 1) {
                        logo_img = "clinic-img"
                    } else {
                        logo_img = "clinic-img-long"
                    }
                    var childPrintInfo = template.compile(childprinkTpl)({
                        triageInfo: triageInfo,
                        getSummary: getSummary, //总结
                        getCommunicate: getCommunicate, //正常，随访，转诊
                        getType: getType, //正常，随访，转诊
                        record_id: record_id,
                        spotInfo: spotInfo,
                        childExaminationInfo: childExaminationInfo,
                        allergy: allergy,
                        cdnHost: cdnHost,
                        baseUrl: baseUrl,
                        spotConfig: spotConfig,
                        logo_img: logo_img,
                    });
                    $('#child_print').html(childPrintInfo);

                    $('#' + id).jqprint();
                },
                error: function () {
                },
            });
        },
        //护理记录打印
        NursingRecord: function (id) {
            $.ajax({
                type: 'post',
                url: nursingRecordPrinkInfoUrl,
                data: {
                    'record_id': record_id,
                },
                dataType: 'json',
                success: function (json) {
                    if (json['errorCode'] == 1001) {
                        showInfo('找不到该数据', '180px', 2);
                        return;
                    }
                    var basicInfo = json['basicInfo'];
                    var soptInfo = json['spotInfo'];
                    var nursingRecord = json['nursingRecord'];
                    var healthEducation = json['healthEducation'];
                    var physicalInfo = json['physicalInfo'];
                    var spotConfig = json['spotConfig'];
                    var logo_img = '';
                    if (spotConfig.logo_shape == 1) {
                        logo_img = "clinic-img"
                    } else {
                        logo_img = "clinic-img-long"
                    }
                    var nursingRecordPrintInfo = template.compile(nursingRecordPrinkTpl)({
                        record_id: record_id,
                        soptInfo: soptInfo,
                        cdnHost: cdnHost,
                        baseUrl: baseUrl,
                        triageInfo: triageInfo,
                        basicInfo: basicInfo,
                        physicalInfo: physicalInfo,
                        nursingRecord: nursingRecord,
                        healthEducation: healthEducation,
                        spotConfig: spotConfig,
                        logo_img: logo_img,
                    });
                    $('#nursing-record-print').html(nursingRecordPrintInfo);
                    $('#' + id).jqprint();
                },
                error: function () {
                },
            });
        },
        addCure: function (id, name, price, unit, time, description) {
            var cureModel = template.compile(cureTpl)({
                id: JSON.stringify(id),
                name: name,
                price: price,
                unit: unit,
                time: time,
                description: description,
                baseUrl: baseUrl
            });
            $('.cure-form tbody').append(cureModel);
        },
        cureEdit: function () {

            $('body').off('click', '.cure-form .op-group>img').on('click', '.cure-form .op-group>img', function () {
                $(this).parents('tr').hide();
                $(this).siblings('input[name="CureRecord[deleted][]"]').val(1);
            })
            $('body').off('change', '#curerecord-curename').on('change', '#curerecord-curename', function (e) {
            	var data = $(this).select2("data")[0];
                _self.addCure(data, data['name'], data['price'],data['unit']);
                $('#curerecord-curename').val('');
            });

        },
        inspectEdit: function () {
            $('body').off('click', '#inspect-record .inspect-form .op-group>img').on('click', '#inspect-record .inspect-form .op-group>img', function () {
                $(this).parents('.inspect-list').hide();
                $(this).parent('.op-group').children('input[name="InspectRecord[deleted][]"]').val(1);
            })
            $('body').off('change', '#inspectrecord-inspectname').on('change', '#inspectrecord-inspectname', function (e) {
            	var data = $(this).select2("data")[0];
                if(_self.checkInspect(data)) {
                    _self.addCheck(data,data['name'], 'InspectRecord[inspect_id][]', 'InspectRecord[deleted][]', 'inspect-list', '.inspect-form tbody', 1);
                }else{
                    showInfo('实验室检查没有关联检验项目','250px',2);
                }
                $(this).val('');
            });
        },

        /*
        检查检查医嘱是否关联项目
         */
        checkInspect:function(list){
            var itmTitle=_self.getItmTitle(list);
            if(itmTitle){
                return true;
            }else{
                return false;
            }

        },
        /*
        直接写一个方法获取itmTitle
         */
        getItmTitle:function(list){
            var itmTitle = '';
            for(itm in list['inspectItem']){
                itmTitle+='<p>'+htmlEncodeByRegExp(list['inspectItem'][itm]['item_name']);
                itmTitle+=list['inspectItem'][itm]['english_name']? '(' + htmlEncodeByRegExp(list['inspectItem'][itm]['english_name']) + ')</p>' : '<p>';
            }
            return itmTitle;
        },
        addCheck: function (list, name, inputName, deleted, parentClass, appendClass, type) {
            var itmTitle = '';
            //实验室检查 和 影像学检查
            if (type == 1 || type == 2) {
                var appendHtml = '';//实验室检查 和 影像学检查要插入的html
                //实验室检查
                if (type == 1) {
                    if(list['inspectItem']){
                        itmTitle=_self.getItmTitle(list);
                    }
                        $('.inspect-form .header th').removeClass('action-column');
                        var appendHtml = template.compile(inspectTpl)({//加载实验室tpl
                            list: htmlEncodeByRegExp(JSON.stringify(list)),
                            name: name,
                            price: list.price,
                            deleted: deleted,
                            inputName: inputName,
                            parentClass: parentClass,
                            baseUrl: baseUrl,
                            type: type,
                            itmTitle: itmTitle
                        });
                        $(appendClass).append(appendHtml);

                    return false;
                }
                //影像学检查
                if (type == 2) {
                    $('.check-form .header th').removeClass('action-column');
                    var appendHtml = template.compile(checkNewTpl)({//加载影像学tpl
                        list: htmlEncodeByRegExp(JSON.stringify(list)),
                        name: name,
                        price: list.price,
                        deleted: deleted,
                        inputName: inputName,
                        parentClass: parentClass,
                        baseUrl: baseUrl,
                        type: type
                    });
                    $(appendClass).append(appendHtml);
                    if ($(appendClass).find('.empty') !== undefined) {//判断是否有empty元素
                        $(appendClass).find('.empty').parents('tr').remove();//删除gridview自带的empty显示
                    }
                    return false;
                }
            }
            // 其他
            var checkModel = template.compile(checkTpl)({
                list: htmlEncodeByRegExp(JSON.stringify(list)),
                name: name,
                deleted: deleted,
                inputName: inputName,
                parentClass: parentClass,
                baseUrl: baseUrl,
                type: type,
                itmTitle: itmTitle
            });
            $(appendClass).append(checkModel);
        },
        checkEdit: function () {

            $('body').off('click', '#check-record .op-group>img').on('click', '#check-record .op-group>img', function () {
                $(this).parents('.check-list').hide();
                $(this).parent('.op-group').children('input[name="CheckRecord[deleted][]"]').val(1);
            })
            $('body').off('change', '#checkrecord-checkname').on('change', '#checkrecord-checkname', function (e) {
            	var data = $(this).select2("data")[0];
                _self.addCheck(data, data.name, 'CheckRecord[check_id][]', 'CheckRecord[deleted][]', 'check-list', '.check-form tbody', 2);
                $(this).val('');
            });
        },

        changeTemplate: function () {
            $('body').on('change', '#triageinfo-template', function (e) {
                var csrfToken = $('meta[name="csrf-token"]').attr("content");
                var case_id = $(this).val();
                if (case_id == '') {
                    case_id = 0;
                }
                window.location.href = outpatientUrl + '&case_id=' + case_id;
            });

            $('body').on('change', '#dental-template', function (e) {
                var case_id = $(this).val();
                if (case_id == '') {
                    case_id = 0;
                }
                window.location.href = outpatientUrl + '&dental_case_id=' + case_id;
            });

        },
        
        is_check: function () { // 判断check框是否要选中

            var flag = 0;
            var length = $('.checkContent .select-option').length;
            $('.checkContent input[type = "radio"]:checked').each(function (index) {
//                var value = $('.checkContent input[type = "radio"]:checked').val();
                var value = $(this).val();
                if (value == 1) {
                    flag++;
                }
            });

            if (flag == length) {
                $('#checkSelect').attr({'checked': true});
            } else {
                $('#checkSelect').attr({'checked': false});
            }
        },
        addConsumables: function (list, totalNum) {
            var consumablesModel = template.compile(consumablesTpl)({
                id: JSON.stringify(list),
                list: list,
                baseUrl: baseUrl,
                totalNum: totalNum ? totalNum : 0
            });
            $('.consumables-form tbody').append(consumablesModel);
        },
        consumablesEdit: function () {

            $('body').on('click', '.consumables-form .op-group>img', function () {
                $(this).parents('tr').hide();
                $(this).siblings('input[name="ConsumablesRecord[deleted][]"]').val(1);
            })
            $('body').on('change', '#consumablesrecord-consumablesname', function (e) {
            	var data = $(this).select2("data")[0];
                var consumablesId = data.consumables_id;
                _self.addConsumables(data, consumablesTotal[consumablesId]);
                $('#consumablesrecord-consumablesname').val('');
            });

        },
        addMaterial: function (list, totalNum) {
            var materialModel = template.compile(materialTpl)({
                id: JSON.stringify(list),
                list: list,
                baseUrl: baseUrl,
                totalNum: totalNum ? totalNum : 0
            });
            $('.material-form tbody').append(materialModel);
        },
        materialEdit: function () {

            $('body').on('click', '.material-form .op-group>img', function () {
                $(this).parents('tr').hide();
                $(this).siblings('input[name="MaterialRecord[deleted][]"]').val(1);
            })
            $('body').on('change', '#materialrecord-materialname', function (e) {
            	var data = $(this).select2("data")[0];

                _self.addMaterial(data, materialTotal[data.id]);
                $('#materialrecord-materialname').val('');
            });

        },
        materialPrint: function () {
            //打印其他非处方医嘱
            $('body').on('click', '.btn-material-check-application-print', function () {
                var id = $(this).attr('name');
                var check_value = [];
                $('input[name="MaterialRecord[name][]"]:checked').each(function () {
                    check_value.push($(this).val());
                });

                if (check_value.length == 0) {
                    showInfo('请勾选要打印的项目', '180px', 2);
                    return;
                }

                $.ajax({
                    type: 'post',
                    url: materialPrinkInfoUrl,
                    data: {
                        'materialId': check_value,
                        'recordId': record_id,
                    },
                    dataType: 'json',
                    success: function (json) {

                        if (json['errorCode'] == 1001) {
                            showInfo(json['msg'], '180px', 2);
                            return;
                        }
                        var spotInfo = json['spotInfo'];
                        var materialRecordDataProvider = json['materialRecordDataProvider'];
                        var PharmcyRepiceInfo = json['PharmcyRepiceInfo'];
                        var totalPrice = json['totalPrice'];
                        var spotConfig = json['spotConfig'];
                        var logo_img = '';
                        if (spotConfig.logo_shape == 1) {
                            logo_img = "clinic-img"
                        } else {
                            logo_img = "clinic-img-long"
                        }
                        console.log(PharmcyRepiceInfo, 'PharmcyRepiceInfo');
                        var prinkMaterialRecordInfoModel = template.compile(materialprinkTpl)({
                            soptInfo: spotInfo,
                            triageInfo: triageInfo,
                            repiceInfo: PharmcyRepiceInfo,
                            materialRecordDataProvider: materialRecordDataProvider,
                            record_id: record_id,
                            totalPrice: totalPrice,
                            cdnHost: cdnHost,
                            baseUrl: baseUrl,
                            spotConfig: spotConfig,
                            logo_img: logo_img,
                        });
                        $('#material_print').html(prinkMaterialRecordInfoModel);
                        $('#' + id).jqprint();
                    },
                    error: function () {
                    },
                });
            });
        },
        consumablesPrint: function () {
            //打印其他非处方医嘱
            $('body').on('click', '.btn-consumables-check-application-print', function () {
                var id = $(this).attr('name');
                var check_value = [];
                $('input[name="ConsumablesRecord[name][]"]:checked').each(function () {
                    check_value.push($(this).val());
                });

                if (check_value.length == 0) {
                    showInfo('请勾选要打印的项目', '180px', 2);
                    return;
                }

                $.ajax({
                    type: 'post',
                    url: consumablesPrinkInfoUrl,
                    data: {
                        'consumablesId': check_value,
                        'recordId': record_id,
                    },
                    dataType: 'json',
                    success: function (json) {

                        if (json['errorCode'] == 1001) {
                            showInfo(json['msg'], '180px', 2);
                            return;
                        }
                        var spotInfo = json['spotInfo'];
                        var consumablesRecordDataProvider = json['consumablesRecordDataProvider'];
                        var PharmcyRepiceInfo = json['PharmcyRepiceInfo'];
                        var totalPrice = json['totalPrice'];
                        var spotConfig = json['spotConfig'];
                        console.log(PharmcyRepiceInfo, 'PharmcyRepiceInfo');
                        var logo_img = '';
                        if (spotConfig.logo_shape == 1) {
                            logo_img = "clinic-img"
                        } else {
                            logo_img = "clinic-img-long"
                        }
                        var prinkConsumablesRecordInfoModel = template.compile(consumablesprinkTpl)({
                            soptInfo: spotInfo,
                            triageInfo: triageInfo,
                            repiceInfo: PharmcyRepiceInfo,
                            consumablesRecordDataProvider: consumablesRecordDataProvider,
                            record_id: record_id,
                            totalPrice: totalPrice,
                            cdnHost: cdnHost,
                            baseUrl: baseUrl,
                            spotConfig: spotConfig,
                            logo_img: logo_img,
                        });
                        $('#consumables_print').html(prinkConsumablesRecordInfoModel);
                        $('#' + id).jqprint();
                    },
                    error: function () {
                    },
                });
            });
        },
        choseChildTemplate: function () {
            var childContent = '';

            $(".field-child-template").change(function () {

                var beforeContent = $('#childexaminationassessment-evaluation_guidance').val();
                var addBeforeContent = beforeContent + (beforeContent ? '\n' : '');
                var val = $(this).val();

                childContent = (val ? (childTemplate[val] ? childTemplate[val].content : '') : '') + '';
                if (childContent == "") {
                    $(this).blur();
                    return;
                }
                $('#childexaminationassessment-evaluation_guidance').val(addBeforeContent + childContent);

                $(this).blur();

            }).focus(function () {
                $(this)[0].selectedIndex = '';

            });
        },
        createFollow: function () {
            $('body').on('click', '.follow-create', function () {
                $('.cardSubmitType').val(2);
                $('.record-end').click();
            });
        },
        recipeBack: function () {
            $('body').on('click', '.btn-recipe-back', function () {
                var idArr = [];
                $('input[name="id[]"]:checked').each(function () {
                    idArr.push($(this).val());
                    console.log(idArr);
                });
            });
        },
        inspectSelect2 : function(){
        	 $('#inspectrecord-inspectname').select2({
             	language: "zh-CN",
                selectOnBlur : true,
                placeholder : '请输入实验室检查进行搜索',
         		minimumInputLength : 1,
         		minimumResultsForSearch : 1,
         		allowClear : false,
         		ajax: { // instead of writing the function to execute the request we use Select2's convenient helper
         	        url: inspectSearchUrl,
         	        dataType: 'json',
         	        quietMillis: 2000,
         	        type : 'post',
         	        delay: 2000,
         	        beforeSend: function (xhr) {
         	        	var searchData = $('.select2-search__field').val();
         	        	if($.trim(searchData) == ""){
         	        		return false;
         	        	}
         	        	return true;
         	    　　	},
         	        data: function (params) {
                         return {
                             name: params.term, // search term
                             phonetic : params.term
                         };
                     },
                     processResults : function (data, page) { // parse the results into the format expected by Select2.
         	            // since we are using custom formatting functions we do not need to alter the remote JSON data
         	        	return { results: data.data};
         	        },
         	        cache: true
         	    },
         	  templateResult : function(data){
         		 searchData = $('.select2-search__field').val();
                 if(typeof(searchData) != 'undefined'  && typeof(data.name) != 'undefined'){
                     searchData = searchData.toString();
                     
                     var name = highLightKeywords(data.name,$.trim(searchData));
                     var meta = '';
                     if(data.phonetic){
                    	 name += '-'+highLightKeywords(data.phonetic,$.trim(searchData));
                     }
                     name += '(';
                     name += data.price+'元';
                     name += ')';
                     return name;
                 }
                 return htmlEncodeByRegExp(data.text);
         	  },
         	  templateSelection : function(data){
         		 if(typeof(data.name) != 'undefined'){
         			 var name = htmlEncodeByRegExp(data.name);
                     var meta = '';
                     if(data.phonetic){
                    	 name += '-'+htmlEncodeByRegExp(data.phonetic);
                     }
                     name += '(';
                     name += data.price+'元';
                     name += ')';
                     return name;
	       		 }	
	       		 return data.text;
         	  },
         	  escapeMarkup: function (m) { return m; } // we do not want to escape markup since we are displaying html in results
             	
             });
        },
        checkSelect2 : function(){
        	
        	$('#checkrecord-checkname').select2({
        		language: "zh-CN",
                selectOnBlur : true,
                placeholder : '请输入影像学检查进行搜索',
        		minimumInputLength : 1,
        		minimumResultsForSearch : 1,
        		allowClear : false,
        		ajax: { // instead of writing the function to execute the request we use Select2's convenient helper
        	        url: checkSearchUrl,
        	        dataType: 'json',
        	        quietMillis: 2000,
        	        type : 'post',
        	        delay: 2000,
        	        beforeSend: function (xhr) {
        	        	var searchData = $('.select2-search__field').val();
        	        	if($.trim(searchData) == ""){
        	        		return false;
        	        	}
        	        	return true;
        	    　　	},
        	        data: function (params) {
                        return {
                            name: params.term, // search term
                            meta : params.term
                        };
                    },
                    processResults : function (data, page) { // parse the results into the format expected by Select2.
        	            // since we are using custom formatting functions we do not need to alter the remote JSON data
        	        	return { results: data.data};
        	        },
        	        cache: true
        	    },
        	  templateResult : function(data){
        		  searchData = $('.select2-search__field').val();
                  if(typeof(searchData) != 'undefined'  && typeof(data.name) != 'undefined'){
                      searchData = searchData.toString();
                      
                      var name = highLightKeywords(data.name,$.trim(searchData));
                      var meta = '';
                      if(data.meta){
                     	 name += '-'+highLightKeywords(data.meta,$.trim(searchData));
                      }
                      name += '(';
                      name += data.price+'元';
                      name += ')';
                      return name;
                  }
                  return htmlEncodeByRegExp(data.text);
        	  },
        	  templateSelection : function(data){
        		  if(typeof(data.name) != 'undefined'){
        			  var name = htmlEncodeByRegExp(data.name);
                      var meta = '';
                      if(data.meta){
                     	 name += '-'+htmlEncodeByRegExp(data.meta);
                      }
                      name += '(';
                      name += data.price+'元';
                      name += ')';
                      return name;
        		  }	
        		  return data.text;
        	  },
        	  escapeMarkup: function (m) { return m; } // we do not want to escape markup since we are displaying html in results

        	});
        },
        cureSelect2 : function(){
        	$('#curerecord-curename').select2({
            	
            	language: "zh-CN",
                selectOnBlur : true,
                placeholder : '请输入治疗进行搜索',
        		minimumInputLength : 1,
        		minimumResultsForSearch : 1,
        		allowClear : false,
        		ajax: { // instead of writing the function to execute the request we use Select2's convenient helper
        	        url: cureSearchUrl,
        	        dataType: 'json',
        	        quietMillis: 2000,
        	        type : 'post',
        	        delay: 2000,
        	        beforeSend: function (xhr) {
        	        	var searchData = $('.select2-search__field').val();
        	        	if($.trim(searchData) == ""){
        	        		return false;
        	        	}
        	        	return true;
        	    　　	},
        	        data: function (params) {
                        return {
                            name : params.term, // search term
                            meta : params.term,
                            unit : params.term
                        };
                    },
                    processResults : function (data, page) { // parse the results into the format expected by Select2.
        	            // since we are using custom formatting functions we do not need to alter the remote JSON data
        	        	return { results: data.data};
        	        },
        	        cache: true
        	  },
        	  templateResult : function(data){
        		  searchData = $('.select2-search__field').val();
        		  if(typeof(searchData) != 'undefined'  && typeof(data.name) != 'undefined'){
                      searchData = searchData.toString();
                      
                      var name = highLightKeywords(data.name,$.trim(searchData));
                      var meta = '';
                      if(data.meta){
                     	 name += '-'+highLightKeywords(data.meta,$.trim(searchData));
                      }
                      name += '(';
                      name += data.price+'元';
                      if(data.unit){
                    	  name += '/'+ highLightKeywords(data.unit,$.trim(searchData));
                      }
                      name += ')';
                      return name;
                  }
                  return htmlEncodeByRegExp(data.text);
        	  },
        	  templateSelection : function(data){
        		  if(typeof(data.name) != 'undefined'){
        			  var name = htmlEncodeByRegExp(data.name);
                      var meta = '';
                      if(data.meta){
                     	 name += '-'+htmlEncodeByRegExp(data.meta);
                      }
                      name += '(';
                      name += data.price+'元';
                      if(data.unit){
                    	  name += '/'+ htmlEncodeByRegExp(data.unit);
                      }
                      name += ')';
                      return name;
        		  }	
        		  return data.text;
        	  },
        	  escapeMarkup: function (m) { return m; } // we do not want to escape markup since we are displaying html in results
        	    
            	
            });
        },
        consumablesSelect2 : function(){
        	$('#consumablesrecord-consumablesname').select2({
            	
            	language: "zh-CN",
                selectOnBlur : true,
                placeholder : '请输入医疗耗材进行搜索',
        		minimumInputLength : 1,
        		minimumResultsForSearch : 1,
        		allowClear : false,
        		ajax: { // instead of writing the function to execute the request we use Select2's convenient helper
        	        url: consumablesSearchUrl,
        	        dataType: 'json',
        	        quietMillis: 2000,
        	        type : 'post',
        	        delay: 2000,
        	        beforeSend: function (xhr) {
        	        	var searchData = $('.select2-search__field').val();
        	        	if($.trim(searchData) == ""){
        	        		return false;
        	        	}
        	        	return true;
        	    　　	},
        	        data: function (params) {
                        return {
                            name : params.term, // search term
                            meta : params.term,
                            unit : params.term,
                            specification : params.term
                        };
                    },
                    processResults : function (data, page) { // parse the results into the format expected by Select2.
        	            // since we are using custom formatting functions we do not need to alter the remote JSON data
        	        	return { results: data.data};
        	        },
        	        cache: true
        	  },
        	  templateResult : function(data){
        		  searchData = $('.select2-search__field').val();
        		  if(typeof(searchData) != 'undefined'  && typeof(data.name) != 'undefined'){
                      searchData = searchData.toString();
                      
                      var name = highLightKeywords(data.name,$.trim(searchData));
                      var meta = '';
                      if(data.meta){
                     	 name += '-'+highLightKeywords(data.meta,$.trim(searchData));
                      }
                      name += '(';
                      if(data.specification){
                      	 name += highLightKeywords(data.specification,$.trim(searchData))+',';
                      }
                      name += data.price+'元';
                      if(data.unit){
                    	  name += '/'+ highLightKeywords(data.unit,$.trim(searchData));
                      }
                      name += ')';
                      return name;
                  }
                  return htmlEncodeByRegExp(data.text);
        	  },
        	  templateSelection : function(data){
        		  if(typeof(data.name) != 'undefined'){
        			  var name = htmlEncodeByRegExp(data.name);
                      var meta = '';
                      if(data.meta){
                     	 name += '-'+htmlEncodeByRegExp(data.meta);
                      }
                      name += '(';
                      if(data.specification){
                       	 name += htmlEncodeByRegExp(data.specification)+',';
                      }
                      name += data.price+'元';
                      if(data.unit){
                    	  name += '/'+ htmlEncodeByRegExp(data.unit);
                      }
                      name += ')';
                      return name;
        		  }	
        		  return data.text;
        	  },
        	  escapeMarkup: function (m) { return m; } // we do not want to escape markup since we are displaying html in results
        	    
            	
            });	
        },
        materialSelect2 : function(){
        	$('#materialrecord-materialname').select2({
            	
            	language: "zh-CN",
                selectOnBlur : true,
                placeholder : '请输入其他进行搜索',
        		minimumInputLength : 1,
        		minimumResultsForSearch : 1,
        		allowClear : false,
        		ajax: { // instead of writing the function to execute the request we use Select2's convenient helper
        	        url: materialSearchUrl,
        	        dataType: 'json',
        	        quietMillis: 2000,
        	        type : 'post',
        	        delay: 2000,
        	        beforeSend: function (xhr) {
        	        	var searchData = $('.select2-search__field').val();
        	        	if($.trim(searchData) == ""){
        	        		return false;
        	        	}
        	        	return true;
        	    　　	},
        	        data: function (params) {
                        return {
                            name : params.term, // search term
                            meta : params.term,
                            unit : params.term,
                            specification : params.term
                        };
                    },
                    processResults : function (data, page) { // parse the results into the format expected by Select2.
        	            // since we are using custom formatting functions we do not need to alter the remote JSON data
        	        	return { results: data.data};
        	        },
        	        cache: true
        	  },
        	  templateResult : function(data){
        		  searchData = $('.select2-search__field').val();
        		  if(typeof(searchData) != 'undefined'  && typeof(data.name) != 'undefined'){
                      searchData = searchData.toString();
                      
                      var name = highLightKeywords(data.name,$.trim(searchData));
                      var meta = '';
                      if(data.meta){
                     	 name += '-'+highLightKeywords(data.meta,$.trim(searchData));
                      }
                      name += '(';
                      if(data.specification){
                      	 name += highLightKeywords(data.specification,$.trim(searchData))+',';
                      }
                      name += data.price+'元';
                      if(data.unit){
                    	  name += '/'+ highLightKeywords(data.unit,$.trim(searchData));
                      }
                      name += ')';
                      return name;
                  }
                  return htmlEncodeByRegExp(data.text);
        	  },
        	  templateSelection : function(data){
        		  if(typeof(data.name) != 'undefined'){
        			  var name = htmlEncodeByRegExp(data.name);
                      var meta = '';
                      if(data.meta){
                     	 name += '-'+htmlEncodeByRegExp(data.meta);
                      }
                      name += '(';
                      if(data.specification){
                       	 name += htmlEncodeByRegExp(data.specification)+',';
                      }
                      name += data.price+'元';
                      if(data.unit){
                    	  name += '/'+ htmlEncodeByRegExp(data.unit);
                      }
                      name += ')';
                      return name;
        		  }	
        		  return data.text;
        	  },
        	  escapeMarkup: function (m) { return m; } // we do not want to escape markup since we are displaying html in results
        	    
            	
            });	
        },
        
    };
    return main;
})
