

define(function (require) {
    var template = require('template');
    var select = require('plugins/select2/select2.full.min');
    var patientInfoTpl = require('tpl/patientInfo.tpl');
    var common = require('js/lib/common');
    var _self;
    isSave=0;
    var main = {
        init: function () {
            _self = this;
            this.bindEvent();
        },
        bindEvent: function () {
            template.config(escape, false);
            _self.addPatientInfo();//患者个人信息卡片
            $('.select-on-check-all').on('click', function () {}).click();
            _self.cure();
            _self.updateCure();
            _self.print();
            jsonFormInit = $("form").serialize();//为了表单验证
        },
        addPatientInfo: function () {
            var triageInfoModel = template.compile(patientInfoTpl)({
                list: triageInfo,
                allergy: allergy,
                baseUrl: baseUrl,
                cdnHost : cdnHost
            });
            $('#outpatient-patient-info').html(triageInfoModel);
        },
        cure: function () {  //修改治疗
            $('body').on('click', '.confirm-cure', function () {
                //$status 值为1时 治疗结束  2为 治疗中
                var idArr = [];
                var remarkArr = [];
                var cureResultArr = [];
                var cureResultError = 0;
                var cureRemarkError = 0;
                if (status == 2) {
                    $(".box-show [name='id[]']:checked").each(function () {

                        var remark = $(this).parent().parent().find("[name='CureRecord[remark]']").val();
                        var cureResult = $(this).parent().parent().find("[name='CureRecord[cure-result]']").val();
                        var inputCureResult = $(this).parent().parent().find(".cure-result-status").val();
                        console.log(inputCureResult);
                        if(inputCureResult == 0){
                            console.log(inputCureResult);
                            isSave = 1;
                            return false;
                        }else {
                            isSave = 0;
                        }

                        idArr.push($(this).val());
                        remarkArr.push(remark);
                        cureResultArr.push(cureResult);
                    });
                } else {
                    $(".box-show [name='id[]']").each(function () {
                        var remark = $(this).parent().parent().find("[name='CureRecord[remark]']").val();
                        var cureResult = $(this).parent().parent().find("[name='CureRecord[cure-result]']").val();
                        var inputCureResult = $(this).parent().parent().find(".L-cure-result").val();
                        // console.log($(this).parent().parent().find(".L-cure-result"),12222);
                        // console.log(inputCureResult,111);
                        if(inputCureResult == 0){
                            isSave = 1;
                            return false;
                        }else {
                            isSave = 0;
                        }
                        if(cureResult.length > 10){
                        	 cureResultError = 1;
                        }
                        if(remark.length > 255){
                        	cureRemarkError = 1;
                        }
                        idArr.push($(this).val());
                        remarkArr.push(remark);
                        cureResultArr.push(cureResult);
                    });
                }
                if(isSave == 1){
                    showInfo('皮试结果不能为空','200px;',2);
                    return false;
                }
                if(!idArr.length){
                    showInfo('请选择需要的治疗','200px;');
                    return false;
                }
//                if(cureResultError == 1){
//                	showInfo('执行结果不能大于10个字符','200px;',2);
//                    return false;
//                }
//                if(cureRemarkError == 1){
//                	showInfo('备注不能大于255个字符','220px;',2);
//                    return false;
//                }

               $.ajax({
                    cache: true,
                    type: "POST",
                    url: dispensingUrl,
                    data: {idArr: idArr, remarkArr: remarkArr,cureResultArr:cureResultArr}, // 你的formid
                    dataType: 'json',
                    async: false,
                    success: function (data, textStatus, jqXHR) {
                        if(data.errorCode == 0){
                        	showInfo('保存成功', '120px');
                            window.location.href = document.referrer;//返回上一页并刷新
                        }else{
                        	showInfo(data.msg, '200px',2);
                        	return;
                        }                    
                    },
                    error: function () {
                       showInfo('保存失败', '100px');
                    }
                });
            });
        },
        updateCure: function () {
            $('body').on('click', '.update-cure', function () {
                $('.L-remark').each(function (){
                    $(this).removeClass('hid');
                    $(this).siblings('span').text("");
                });
                $('.L-cure-result').each(function (){
                    $(this).removeClass('cure_result');
                    $(this).attr('disabled',false);
                });
                $('.input-cure-result').each(function (){
                    $(this).removeClass('hid');
                    $(this).siblings('span').text("");
                });
                $(this).html('保存');
                $(this).removeClass('update-cure').addClass('confirm-cure');

                $('.print-check').hide();
            })
        },

        print: function () {
            $('body').on('click', '.print-check', function () {
                var id = $(this).attr('name');
                $('#'+id).jqprint();
            });
        }
    };
    return main;
})