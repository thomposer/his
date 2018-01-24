/* 
 * time: 2017-2-21 11:41:45.
 * author : yu.li.
 */
define(function (require) {
    var jqprint = require('js/lib/jquery.jqprint-0.3');
    var common = require('js/lib/common');
    var main = {
        init: function () {
            $('.print_label').unbind('click').click(function (e) {
                e.preventDefault();
                var obj = $(this).parent();
                var user_name = obj.attr('user_name');
                var sex = obj.attr('sex');
                var birthday = obj.attr('birthday');
                var phone = obj.attr('phone');
                var patient_number = obj.attr('patient_number');
                var sexStr;
                if (sex == 1) {
                    sexStr = 'Male 男';
                } else if (sex == 2) {
                    sexStr = 'Female 女';
                } else if (sex == 3) {
                    sexStr = 'Unknown 不详';
                } else if (sex == 4){
                    sexStr = 'Other 其他';
                }else{
                    sexStr='';
                }
                var a = '';
                a += '<div><span class="print-label-l ver-bottom">Name:</span><span class="print-label-r text-overflow">' + htmlEncodeByRegExp(user_name) + '</span></div>';
                a += '<div><span class="print-label-l">Sex:</span><span class="print-label-r">' + sexStr + '</span></div>';
                a += '<div><span class="print-label-l">DOB:</span><span class="print-label-r">' + birthday + '</span></div>';
                a += '<div><span class="print-label-l">Tel:</span><span class="print-label-r">' + phone + '</span></div>';
                a += '<div><span class="print-label-l">MRN:</span><span class="print-label-r">' + patient_number + '</span></div>';
                $('#print-view').html(a);
                $('#print-view').jqprint();
            });
        }
    }
    return main;
})

