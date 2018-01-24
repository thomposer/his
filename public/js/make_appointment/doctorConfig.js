

define(function (require) {
    var template = require('template');
    var select = require('plugins/select2/select2.full.min');
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

            $('.select-on-check-all').on('click', function () {}).click();
            _self.cure();
            _self.updateCure();
        },
        cure: function () {  //修改治疗
            $('body').on('click', '.confirm-config', function () {

                var idArr = [];
                var remarkArr = [];

                var notRemarkArr=[];

                $("[name='id[]']:checked").each(function () {
                    var remark = $(this).siblings("[name='User[id]']").val();
                    remarkArr.push(remark);
                });


                $("[name='id[]']").not("input:checked").each(function () {
                    var notRemark = $(this).siblings("[name='User[id]']").val();
                    notRemarkArr.push(notRemark);
                });

                var jsonFormCurr = $("form").serialize();

                var confirm_option = {
                    label: "确定",
                    className: 'btn-cancel btn-form',
                };
                var cancel_option = {
                    label: "取消",
                    className: 'btn-default btn-form',
                };
                btns = {
                    cancel: cancel_option,
                    confirm: confirm_option,
                }

                if (jsonFormCurr != jsonFormInit) {
                    bootbox.confirm(
                        {
                            message: '此操作将会影响患者按医生预约，<br/>是否要保存修改?',
                            title: '系统提示',
                            buttons: btns,
                            callback: function (confirmed) {
                                if (confirmed) {
                                    $.ajax({
                                        cache: true,
                                        type: "POST",
                                        url: dispensingUrl,
                                        data: {idArr: idArr, remarkArr: remarkArr,notRemarkArr:notRemarkArr}, // 你的formid
                                        dataType: 'json',
                                        async: false,
                                        success: function (data, textStatus, jqXHR) {
                                            showInfo('保存成功', '100px');
                                            window.location.href = dispensingUrl;//返回上一页并刷新
                                        },
                                        error: function () {
                                            showInfo('保存失败', '100px');
                                        }
                                    })
                                } else {
                                    return true;
                                }
                            }
                        }
                    );
                } else {
                    window.location.href=dispensingUrl;
                }

            });
        },
        updateCure: function () {
            $('body').on('click', '.update-config', function () {
                $('.L-remark').each(function (){
                    $(this).removeClass('hid');
                    $(this).siblings('span').text("");
                });
                $(this).html('保存');
                $(this).removeClass('update-config').addClass('confirm-config');

                var Html="<input type='checkbox' name='id[]' class='checkitemid' value='1'>";
                var Check="<input type='checkbox' name='id[]' class='checkitemid' value='1' checked>";

                $('.remark').each(function(){
                   if($(this).attr("name")=='1'){
                       $(this).after(Check);
                       $(this).remove();
                   }else{
                       $(this).after(Html);
                       $(this).remove();
                   }
                });
                jsonFormInit = $("form").serialize();//为了表单验证
            })
        },

    };
    return main;
})