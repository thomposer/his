define(function(require){
    var common = require('js/lib/common');
    var main = {
        init : function(){
            main.bindEvent();
            main.setCardTurn();
        },
        bindEvent : function(){
        },
        setCardTurn:function(){
            var a = '',notVipNum = $("#not-vip-num").text(),vipCardNew = $("#vip-card-new").text();
            if(notVipNum == 0){
                var summaryPercent = '--';
            }else{
                var summaryPercent = ((vipCardNew/notVipNum)*100).toFixed(2)+"%";
            }
            $("#recharge-summary-percent").html(summaryPercent);
            $('.date-count').remove();
            a += '<tr>';
            a += '<th class="date-count report-text-for-center" data-col-seq="0" rowspan="2" style="width: 3.09%;">';
            a += '日期';
            a += '</th>';
            a += '<th class="report-text-for-center" data-col-seq="1" style="width: 11.03%;background: #ffffff" colspan="4">';
            a += '充值总览';
            a += '</th>';
            a +='<th class="report-text-for-center" data-col-seq="5" style="width: 11.09%; background: #ffffff" colspan="4">';
            a += '开卡转化';
            a += '</th>';
            a += '</tr>';

            $('#data-report-table-width').find('thead').find('tr').before(a);

        }
    };
    return main;
});