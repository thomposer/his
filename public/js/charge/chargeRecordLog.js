/*
 * time: 2017-2-21 11:41:45.
 */
define(function (require) {
    var template = require('template');
    var jqprint = require('js/lib/jquery.jqprint-0.3');
    var common = require('js/lib/common');
    var chargeRecordLogTpl = require('tpl/charge/chargeRecordLog.tpl');
    var main = {
        init: function () {
            $('body').on('click','.print_label',function(e){
                e.preventDefault();
                if(entrance == 1){
                    var obj = $(this).parent();
                }else{
                    var obj = $(this)
                }

                var log_id = obj.attr('id');
                $.ajax({
                    type: 'post',
                    url: chargeRecordLogUrl,
                    data: {
                        'log_id': log_id
                    },
                    success: function (json) {
                        var spotInfo = json['spotInfo'];
                        var chargeInfoLogList = json['chargeInfoLogList'];
                        var chargeRecordLogList = json['chargeRecordLogList'];
                        var chargeRecordLogTplModel = template.compile(chargeRecordLogTpl)({
                            spotInfo: spotInfo,
                            chargeInfoLogList:chargeInfoLogList,
                            chargeRecordLogList:chargeRecordLogList,
                            cdnHost: cdnHost,
                            baseUrl: baseUrl,
                            printWay: json.printWay,
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

