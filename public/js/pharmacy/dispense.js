define(function (require) {
    var template = require('template');
    var migrate = require('js/lib/jquery-migrate-1.1.0');
    var jqprint = require('js/lib/jquery.jqprint-0.3');
    var patientInfoTpl = require('tpl/patientInfo.tpl');
    var recipeTpl = require('tpl/outpatient/recipe.tpl');
    var common = require('js/lib/common');
    var recipePrint = require('js/outpatient/recipePrint');
    var _self;
    var main = {
        init: function () {
            _self = this;
            this.bindEvent();
        },
        bindEvent: function () {
            template.config(escape, false);
            _self.addPatientInfo();//患者个人信息卡片
            $('.select-on-check-all').on('click', function () {
            }).click();
            _self.preserve();//保存用药须知
            _self.dispensing();
            _self.updateDispense();
            _self.print();
            jsonFormInit = $("form").serialize();//为了表单验证
            _self.addSkinTest();
            _self.addSkinPrint();//包含用法用量的打印
//            _self.addPrintContainer();
//            _self.printRecipeLabel();
//            _self.printRecipeLabelModal();
//             $('').click
//            $("[name='PharmacyRecord[remark]']").keyup(function () {
//                if ($(this).val().length > 200) {
//                    $(this).val($(this).val().substr(0, 200));
//                    showInfo('操作失败,用药须知不得超过200个字', '400px', 2);
//                }
//            });
        },
        preserve: function () {
            $('.confirm-preserve').click(function () {
                var remarkArr = [];
                var idArr = [];
                if ($('input[name="id[]"]:checked').length <= 0) {
                    showInfo('请选择需要发放的药品', '200px');
                } else {
                    $('input[name="id[]"]:checked').each(function () {
                        var remark = $(this).parent().parent().find("[name='PharmacyRecord[remark]']").val();
                        var id = $(this).parent().parent().find(".checkitemid").val();
                        if (id) {
                            remarkArr.push(remark);
                            idArr.push(id);
                        }
                    });
                    _self.complete_preserve(this, idArr, remarkArr);
                }
            });
        },
        addPatientInfo: function () {
            var triageInfoModel = template.compile(patientInfoTpl)({
                list: triageInfo,
                allergy: allergy,
                baseUrl: baseUrl,
                cdnHost: cdnHost
            });
            $('#outpatient-patient-info').html(triageInfoModel);
        },
        dispensing: function () {  //发药
            $('body').on('click', '.confirm-dispense', function () {
                var idArr = [];
                var remarkArr = [];
                var recipeOutArr = [];
                var content = '';
                //var index = 0,flag = false;
                if (status == 3) {
                    $('input[name="id[]"]:checked').each(function () {
                        var remark = $(this).parent().parent().find("[name='PharmacyRecord[remark]']").val();
                        var cure_result = $(this).parent().parent().next().find('.cure_result').attr("cure_result");
                        var type = $(this).parent().parent().next().find('.cure_result').attr("type");
                        // type =1;
                        /*index++;
                         if(remark.length > 90) {
                         flag = true;
                         return false;
                         }*/

                        if (type == 1 && cure_result == 2) {  // 阳性
                            content = "待发药品中的皮试结果为阳性，无法完成发药";
                            showInfo(content, '400px', 2);
                            return false;
                        } else if (type == 1 && !cure_result) {
                            content = "待发药品中尚有皮试结果未出，暂不支持发药操作";
                            showInfo(content, '400px', 2);
                            return false;
                        }
                        var recipeOut = $(this).parent().parent().find("[name='recipeOut']").val();
                        idArr.push($(this).val());
                        remarkArr.push(remark);
                        recipeOutArr.push(recipeOut);
                    });
                    /*if(flag){
                     showInfo('操作失败,第'+index+'用药须知不得超过90个字','400px',2);
                     return;
                     }*/
                    if (content.length > 0) {
                        return false;
                    }
                    _self.dispense(this, idArr, remarkArr, recipeOutArr);
                } else {
                    $("[name='id[]']").each(function () {
                        var remark = $(this).parent().parent().find("[name='PharmacyRecord[remark]']").val();
                        var cure_result = $(this).parent().parent().next().find('.cure_result').attr("cure_result");
                        var type = $(this).parent().parent().next().find('.cure_result').attr("type");

                        if (type == 1 && cure_result == 2) {  // 阳性
                            content = "待发药品中的皮试结果为阳性，无法完成发药";
                            showInfo(content, '400px', 2);
                            return false;
                        } else if (type == 1 && !cure_result) {
                            content = "待发药品中尚有皮试结果未出，暂不支持发药操作";
                            showInfo(content, '400px', 2);
                            return false;
                        }

                        idArr.push($(this).val());
                        remarkArr.push(remark);
                    });
                    if (content.length > 0) {
                        return false;
                    }
                    _self.complete(this, idArr, remarkArr, content);
                }
            });
        },
        updateDispense: function () {
            $('body').on('click', '.update-dispense', function () {
                $('.L-remark').each(function () {
                    $(this).removeClass('hid').siblings('p').addClass('hid');
                });
                $(this).html('保存');
                $(this).removeClass('update-dispense').addClass('confirm-dispense');

                $('.print-check').hide();
                $('.print-check-label').hide();
                $('.print-check-modal').hide();
            })
        },
        dispense: function (obj, idArr, remarkArr, recipeOutArr) {
            if (!idArr.length) {
                showInfo('请选择需要发放的药品', '200px');
                return false;
            }
            if (recipeOutArr.indexOf('1') != -1) {//内购和外购一起
                var data = JSON.stringify({idArr: idArr, remarkArr: remarkArr, recipeOutArr: recipeOutArr});
                modal.open(obj, data);
            } else {//纯外购
                _self.complete(obj, idArr, remarkArr);
            }
        },
        complete_preserve: function (obj, idArr, remarkArr) {
            var _csrf = $("[name='_csrf']").val();
            $.ajax({
                cache: true,
                type: "POST",
                url: preserveUrl,
                data: {idArr: idArr, remarkArr: remarkArr, _csrf: _csrf},
                datatype: 'json',
                async: false,
                success: function (json) {
                    var data = JSON.parse(json);
                    if (data.errorCode == 10001) {
                        if (data.message) {
                            showInfo(data.message, '200px', 2);
                        } else {
                            showInfo('操作失败', '150px', 2);
                        }
                    } else {
                        showInfo('操作成功', '150px');
                        window.location.href = dispenseUrl + "?id=" + getUrlParam('id');
                    }

                },
                error: function () {
                    showInfo('操作失败', '150px');
                }
            });

        },
        complete: function (obj, idArr, remarkArr) {

            var _csrf = $("[name='_csrf']").val();
            var id = getUrlParam('id') + 'pharmacyIndex';
            var autoPrint = getUrlParam('autoPrint');
            $.ajax({
                cache: true,
                type: "POST",
                url: dispensingUrl + '?id=' + recordId,
                data: {idArr: idArr, remarkArr: remarkArr, _csrf: _csrf}, // 你的formid
                dataType: 'json',
                async: false,
                success: function (data, textStatus, jqXHR) {
                    if (data.errorCode == 10001) {
                        if (data.message) {
                            showInfo(data.message, '200px', 2);
                        } else {
                            showInfo('操作失败', '150px', 2);
                        }
                    } else {
                        showInfo('操作成功', '150px');

                        window.location.href = completeUrl + "?id=" + getUrlParam('id');
                    }
                },
                error: function () {
                    showInfo('操作失败', '150px');
                }
            });
        },
        print: function () {
            var id = getUrlParam('id') + 'pharmacyIndex';
            var autoPrint = getUrlParam('autoPrint');
            if (localStorage.getItem(id) == '1' && autoPrint == 1) {
                $('.print-check-modal').click(function () {
                    if (localStorage.getItem(id) == '1' && autoPrint == 1) {
                        setTimeout(function () {
                            $('#ajaxCrudModal').find('.btn-form').click().click();
                        }, 500);
                    }

                }).click();
                localStorage.setItem(id, 0);
            }

            $('body').on('click', '.print-check', function () {
//                var id = $(this).attr('name');
//                $('#' + id).jqprint();
                var check_value = [];
                $('.cure-record-index .table .recipe-top').each(function () {
                    check_value.push($(this).attr('data-key'));
                });
                // filterType 1-打印精二处方 2-打印儿科处方
                var filterType = $(this).attr('data-type');
                var options = {
                    getCureRecord: getCureResult,
                    getStatusOtherDesc: getStatusOtherDesc,
                    cdnHost: cdnHost,
                    baseUrl: baseUrl,
                    filterType: filterType
                };
                recipePrint.print(recordId, check_value, recipePrintUrl, options);
            });
        },
        addSkinTest: function () {

            for (var i = 0; i < recipeData.length; i++) {
                var a = '';
                var b = '';
                var red_class = '';
                if (recipeData[i].skin_test_status == 1) {
                    a += "<tr>";
                    a += "<td></td>";
                    if (recipeData[i].skin_test) {
                        b = '（' + htmlEncodeByRegExp(recipeData[i].skin_test) + '）';
                    } else {
                        b = recipeData[i].skin_test;
                    }
                    a += "<td class='text-left' colspan='3'>皮试：需要" + b + "</td>";
                    a += "<td class='text-right' colspan='1'>皮试类型：</td>";
                    if (recipeData[i].cureListName) {
                        a += "<td colspan='2'>" + (recipeData[i].cureListName) + "</td>";
                    } else {
                        a += "<td colspan='2'></td>";
                    }

                    if (recipeData[i].cure_result) {
                        a += "<td class='text-right' colspan='1'>皮试结果：</td>";
                        red_class = (recipeData[i].cure_result == 2) ? 'skinTest' : '';
                        a += "<td class=\"cure_result " + red_class + "\" colspan='2' type=" + recipeData[i].cureType + " cure_result=" + recipeData[i].cure_result + ">" + getCureResult[recipeData[i].cure_result] + "</td>";
                    } else {
                        a += "<td class='text-right' colspan='1'></td>";
                        a += "<td class=\"cure_result\" type=" + recipeData[i].cureType + " colspan='2'></td>";
                    }
                    a += "</tr>";
                } else if (recipeData[i].skin_test_status == 2) {
                    var a = '';
                    a += "<tr >";
                    a += "<td></td>";
                    a += "<td>皮试：免</td>";
                    a += "<td colspan='8'></td>";
                    a += "</tr>";
                }
                $('.skin_test_' + recipeData[i].id).after(a);
            }
        },
        addSkinPrint: function () {
            for (var i = 0; i < recipeData.length; i++) {
                var a = '';
                var b = '';
                var useAge = '';//用法用量
                if (recipeData[i].dose) {
                    useAge += recipeData[i].dose + recipeData[i].dose_unit + ';';
                }
                if (recipeData[i].used && !recipeData[i].frequency) {

                    useAge += recipeData[i].used;

                } else if (!recipeData[i].used && recipeData[i].frequency) {

                    useAge += recipeData[i].frequency;

                } else {

                    useAge += recipeData[i].used + ';' + recipeData[i].frequency;
                }
                a += "<tr class='non-recipe-top' id='non-recipe-top'>";

                a += "<td colspan='6'><span class='recipe-font-weight'>用法用量:</span>" + useAge + "</td> ";

                a += "<td colspan='1'>" + recipeData[i].type + "</td> ";

                a += "</tr>";
                if (recipeData[i].skin_test_status == 1) {
                    a += "<tr class=\"non-recipe-top\" id='non-recipe-top'>";
                    if (recipeData[i].skin_test != '') {
                        b = '（' + htmlEncodeByRegExp(recipeData[i].skin_test) + '）';
                    } else {
                        b = recipeData[i].skin_test;
                    }
                    a += "<td colspan='2'><span class='recipe-font-weight'>皮试：需要</span>" + b + "</td>";

                    cureName = recipeData[i].cureListName ? recipeData[i].cureListName : '';

                    a += "<td class='text-right' colspan='1'><span class='recipe-font-weight'>皮试类型：</span></td>";
                    a += "<td >" + cureName + " </td>"

                    cureResult = recipeData[i].cure_result ? getCureResult[recipeData[i].cure_result] : '';

                    a += "<td ></td>";
                    a += "<td colspan='2'> " + (cureResult ? "<span class='recipe-font-weight'>皮试结果：</span>" + cureResult : '') + "</td>";
                    a += "</tr>";
                } else if (recipeData[i].skin_test_status == 2) {
                    a += "<tr class=\"non-recipe-top\" id='non-recipe-top'>";
                    a += "<td>皮试：免</td>";
                    a += "<td></td>";
                    a += "<td></td>";
                    a += "<td></td>";
                    a += "<td></td>";
                    a += "<td></td>";
                    a += "</tr>";
                }
                $('.skin_test_print_' + recipeData[i].id).after(a);
            }
        },
        addPrintContainer: function () {
            $('.wrapper').after('<div id="recipe-print-container" class="common-print-container recipe-print-container" style="display:none;"> </div>');
        },
        printRecipeLabelModal: function () {
            var isCommitted = false;//表单是否已经提交标识，默认为false
            $('#printLabelForm').yiiAjaxForm({
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
                    if (data.errorCode == 0) {
                        recipePrintData = data.recipePrintData;
                        _self.printRecipeLabel(recipePrintData);
                        if (isCommitted == true) {
                            $('#ajaxCrudModal').modal('hide');
//                            $.pjax.reload({container: '#crud-datatable-pjax', cache: false, timeout: 5000});  //Reload
                        }
                    } else {
                        isCommitted = false;
                        showInfo(data.msg, '180px', 2);
                    }
                },
            });
        },
        printRecipeLabel: function (recipePrintData) {//打印药品标签
            var container = $('#recipe-print-container');
            var a = '';
            for (var i in recipePrintData) {
                var json = recipePrintData[i];
                a += ' <div class="recipe-print-container-parent">';
                a += '<div class="top-left">';
                a += '<div class="text-overflow">姓名：' + htmlEncodeByRegExp(json.userName) + '</div>';
                a += '<div>性别：' + json.sex + '</div>';
                a += '</div>';
                a += '<div class="top-right">';
                a += '<div>病历号：' + json.patientNumber + '</div>';
                a += '<div>年龄：' + json.age + '</div>';
                a += ' </div>';
                a += ' <div class="clearfix"></div>';
                a += '<div class="top-hr ma-top-hr"></div>';
                a += ' <div class="recipe-name-info ma-top-2">';
                a += '<span class="text-overflow-recipe">' + htmlEncodeByRegExp(json.recipeName) + '</span>    ';
                a += '<span class="fr">' + json.unit + '</span>    ';
                a += '</div>';
                a += '<div>' + json.specification + '</div>';
                a += '<div class="recipe-print-title-h2 ma-top-2">' + json.used + '</div>';
                a += '<div class="recipe-print-title-h2">' + json.frequency + '</div>';
                a += ' <div class="ma-top-2">用药须知：' + json.remark + '</div>';
                a += '<div class="bottom-info">';
                a += ' <div class="ma-bottom-hr  bottom-hr"></div>';
                a += '<div>';
                a += '<span>妈咪知道儿科诊所海德门诊部</span>';
                a += '<span class="fr">电话：0755-86546753</span>';
                a += '</div>';
                a += '</div>';
                a += '</div>';
            }
//            $('.print-check-label').click(function () {
            $('#recipe-print-container').html(a);
            window.print();
//            });

        }
    };
    return main;
})