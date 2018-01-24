

define(function (require) {
    var template = require('template');
    var patientInfoTpl = require('tpl/patientInfo.tpl');
    var inspectSpecimenTpl = require('tpl/inspect/inspectSpecimen.tpl');
    var common = require('js/lib/common');
    var JsBarcode = require('js/lib/JsBarcode.all.min');
    var _self;
    var main = {
        init: function () {
            _self = this;
            this.bindEvent();
        },
        bindEvent: function () {
            template.config(escape, false);
            var hash = window.location.hash ? window.location.hash : '';
            $('ul.nav-tabs>li>a').on('click', function () {
                var href = $(this).attr('href');
                window.location.hash = href;
                hash = href;

            });
            if (hash != '') {
                $('ul.nav-tabs>li').removeClass('active');
                $('a[href="' + hash + '"]').parent('li').addClass('active');
                $(hash).siblings('.tab-pane').removeClass('active');
                $(hash).addClass('active');
            }

            _self.addPatientInfo();//患者个人信息卡片
            _self.inspect();
            _self.updateInspect();
            _self.addPrintContainer();
            _self.print();
            //弹窗提醒
            if (status == 1) {//已完成的  检验医嘱  检测是否有危机值的报警
                _self.autoWarning(hash);
                _self.clickWarning();
            }
            if (status == 1 || status == 4) {
//            	$('.file-drop-zone-title').hide();
                $('.kv-file-remove').hide();
                $('.input-group-btn').find('input').attr("disabled", "disabled");
                $('.input-group-btn').find('.btn-browse-custom').addClass("btn-browse-custom-disabled");
                $('.input-group-btn').find('input').addClass('no-click');

            }

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
        },
        addPatientInfo: function () {
            var triageInfoModel = template.compile(patientInfoTpl)({
                list: triageInfo,
                baseUrl: baseUrl,
                cdnHost: cdnHost,
                allergy: allergy
            });
            $('#outpatient-patient-info').html(triageInfoModel);
        },
        inspect: function () {  //修改治疗
            $('body').on('click', '.confirm-inspect', function () {

                var pid = $('.tab-pane.active').attr('id');

                var remarkArr = [];
                var unionidArr = [];

                $("input[name='id" + pid + "']").each(function () {
                    var result = $(this).siblings('input[name="InspectRecord[result]"]').val();
                    unionidArr.push($(this).val());
                    remarkArr.push(result);

                });

                dispensingUrl = $("form").attr("action");

                $.ajax({
                    cache: true,
                    type: "POST",
                    url: dispensingUrl,
                    data: {idArr: pid, remarkArr: remarkArr, unionidArr: unionidArr}, // 你的formid
                    dataType: 'json',
                    async: false,
                    success: function (data, textStatus, jqXHR) {
                        if (data.errorCode == 0) {

                            window.location.href = document.referrer;//返回上一页并刷新

                        } else {
                            showInfo(data.msg, '180px', 2);
                        }
                    },
                    error: function () {
                        showInfo('系统异常,请稍后再试', '180px', 2);
                    }
                });
            });
        },
        updateInspect: function () {
            $('body').on('click', '.update-inspect', function () {
                var inspectForm = $(this).parent().parent();

                inspectForm.find('.L-remark').each(function () {
                    $(this).removeClass('hid');
                    $(this).siblings('span').text("");
                });
                $(this).html('保存');
                $(this).removeClass('update-inspect').addClass('confirm-inspect');

                inspectForm.find('.input-group-btn').find('input').removeAttr('disabled');
                inspectForm.find('.input-group-btn').find('.btn-browse-custom').removeClass("btn-browse-custom-disabled");
                inspectForm.find('.input-group-btn').find('input').removeClass('no-click');
                inspectForm.find('.kv-file-remove').show();
                inspectForm.find('.file-drop-zone-title').show();

                inspectForm.find('.print-check').hide();
            })
        },
        addPrintContainer: function () {
            $('.wrapper').after('<div id="specimen-print-container" class="common-print-container specimen-print-container" style="display:none;"> </div>');
//            $('.wrapper').after('<div id="specimen-print-container" class="common-print-container specimen-print-container " style="display:block;"> </div>');
        },
        print: function () {
            $('body').on('click', '.print-check', function () {
                var id = $(this).attr('name');
                $('#' + id).jqprint();
            });
            $('body').on('click', '.print-label', function () {
                var specimen_number = $(this).attr('specimen_number');
                _self.prinLabel(specimen_number);
            });

        },
        prinLabel: function (specimen_number) {
            var arr = [inspectSpecimen[specimen_number]];
            var specimenText = template.compile(inspectSpecimenTpl)({
                list: arr,
            });
            $('#specimen-print-container').html(specimenText);
            $('#specimen-print-container .barcode').each(function () {
                var barcode = $(this).attr('barcode');
                if (barcode != '') {
                    $(this).JsBarcode(barcode, {
//                        format: "CODE128",
                        displayValue: false,
                        height: 200,
                        width: 6,
                    });
                }
            });
            setTimeout(function () {
                window.print();
            }, 500);
//            $('#print-view').jqprint();
        },
        autoWarning: function (hash) {
            if (!hash) {
                hash = $('.patient-form ul li.active a').attr('href');
                if(!hash){
                    hash=$('.item_title').attr('href');
                }
            }
            var insRecord = hash.substring(1);
            if (warning[insRecord]) {
                $('.warnModal-' + insRecord).click();
            }
        },
        clickWarning: function () {
            $('.patient-form ul li a').on('click', function () {
                var insRecordId = $(this).attr('href').substring(1);
                if (insRecordId && warning[insRecordId]) {
                    $('.warnModal-' + insRecordId).click();
                }
            })
        }
    };
    return main;
})