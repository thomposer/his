
define(function (require) {
    var template = require('template');
    var jqprint = require('js/lib/jquery.jqprint-0.3');
    var common = require('js/lib/common');
    var chargeRecordLogTpl = require('tpl/charge/print.tpl');
    var main = {
        init: function () {
            $('body').off('click').on('click', '.btn-charge-list-print', function (e) {
                e.preventDefault();
                var logId = $('.charge-print-list').attr('data-id');
                var selectId = 0;
                $("input[name='printList[]']").each(function(){
                    if($(this).is(':checked')){
                        selectId  = selectId + parseInt($(this).attr('value'));
                    }
                });
                if(selectId == 0){
                    showInfo('至少选择一项', '250px', 2);//错误提示
                    return false;
                }
                $.ajax({
                    type: 'post',
                    url: chargeLogPrintData,
                    data: {
                        'logId': logId,
                        'selectId': selectId,
                    },
                    success: function (json) {
                        console.log(json);
                        var spotInfo = json["data"]['spotInfo'];
                        var orderLogList = json["data"]['orderLogList'];
                        var materialList = json["data"]['materialList'];
                        var chargeRecordLog = json["data"]['chargeRecordLog'];
                        var spotConfig = json['data']['spotConfig'];
                        var packageRecord = json['data']['packageRecord'];
                        var logo_img = '';
                        if(spotConfig.logo_shape == 1){
                            logo_img = "clinic-img"
                        }else{
                            logo_img = "clinic-img-long"
                        }
                        var chargeRecordLogTplModel = template.compile(chargeRecordLogTpl)({
                            spotInfo: spotInfo,
                            orderLogList: orderLogList,
                            materialList: materialList,
                            chargeRecordLogList: chargeRecordLog,
                            cdnHost: cdnHost,
                            baseUrl: baseUrl,
                            printType: parseInt(json["data"]['print']),
                            spotConfig : spotConfig,
                            packageRecord : packageRecord,
                            logo_img: logo_img,
                        });
                        $('#print-view').html(chargeRecordLogTplModel);
                        $('#print-view').jqprint();
                    },
                    error: function () {

                    },
                    dataType: 'json'

                });
            });
        }
    }
    return main;
})

