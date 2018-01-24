

define(function (require) {
    var template = require('template');
    var patientInfoTpl = require('tpl/patientInfo.tpl');
    var common = require('js/lib/common');
    var _self;
    var main = {
        init: function () {
            _self = this;
            this.bindEvent();
        },
        bindEvent: function () {
            template.config(escape, false);
            _self.addPatientInfo();//患者个人信息卡片
            $('.select-on-check-all').on('click', function () {}).click();
            _self.updateDispense();
            jsonFormInit = $("form").serialize();//为了表单验证
            _self.addSkinTest();
//            $("[name='PharmacyRecord[remark]']").keyup(function(){
//                if($(this).val().length > 90) {
//                    $(this).val($(this).val().substr(0,90));
//                    showInfo('操作失败,用药须知不得超过90个字','400px',2);
//                }
//            });
            
            $('body').on('click', '.confirm-dispense', function () {
            	var idArr = [];
                var remarkArr = [];
                var recipeOutArr = [];
            	if(status == 4){
            		$('input[name="id[]"]:checked').each(function () {
                        var remark = $(this).parent().parent().find("[name='PharmacyRecord[remark]']").val();
                        idArr.push($(this).val());
                        remarkArr.push(remark);
                    });
            		_self.dispense(this, idArr, remarkArr);
            	}else{
            		$("[name='id[]']").each(function () {
                        var remark = $(this).parent().parent().find("[name='PharmacyRecord[remark]']").val();
                        idArr.push($(this).val());
                        remarkArr.push(remark);
                    });
            		_self.complete(idArr,remarkArr,completeUrl);
            	}
            });
            
        },
        dispense: function (obj, idArr, remarkArr) {
            if (!idArr.length) {
                showInfo('请选择需要退药的药品', '220px',2);
                return false;
            }
            var data = JSON.stringify({idArr: idArr, remarkArr: remarkArr});
            var url = $(this).attr('href');
            var cancel_option = {
					label: "取消",
					className: 'btn-default  btn-form',
				};
			var confirm_option = {
					label: "确定",
					className: 'btn-cancel btn-form',
			};
			btns = {
					cancel: cancel_option,
					confirm: confirm_option,
			}
			bootbox.confirm(
						{
							message: '确认退药吗？确认退药操作后不能再撤销。',
							title: '系统提示',
							buttons: btns,
							callback: function (confirmed) {
								if (confirmed) {
									_self.complete(idArr,remarkArr,preUrl);
								} else {
									return true;
								}
							}
						}
				);
        },
        complete: function (idArr, remarkArr,url) {
            $.ajax({
                cache: true,
                type: "POST",
                url: url + '?id=' + recordId,
                data: {idArr: idArr, remarkArr: remarkArr}, // 你的formid
                dataType: 'json',
                async: false,
                success: function (data, textStatus, jqXHR) {
                	if(data.errorCode == 0){
                		showInfo('操作成功', '150px');
                		window.location.href = completeUrl+"?id="+getUrlParam('id');
                	}else{
                		showInfo(data.msg,'400px',2);
                	}
                	
                },
                error: function () {
                    showInfo('操作失败', '150px',2);
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

        updateDispense: function () {
            $('body').on('click', '.update-dispense', function () {
                $('.L-remark').each(function () {
                    $(this).removeClass('hid').siblings('p').addClass('hid');
                });
                $(this).html('保存');
                $(this).removeClass('update-dispense').addClass('confirm-dispense');
            })
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
                        b = '（' +htmlEncodeByRegExp(recipeData[i].skin_test) + '）';
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
                        a += "<td class=\"cure_result " + red_class + "\" colspan='3' type=" + recipeData[i].cureType + " cure_result=" + recipeData[i].cure_result + ">" + getCureResult[recipeData[i].cure_result] + "</td>";
                    } else {
                        a += "<td class='text-right' colspan='1'></td>";
                        a += "<td class=\"cure_result\" type=" + recipeData[i].cureType + " colspan='3'></td>";
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
        }
     
    };
    return main;
})