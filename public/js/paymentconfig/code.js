define(function (require) {
    var common = require('js/lib/common');
    var qrcode = require('js/lib/jquery.qrcode.min');
    var main = {
        init: function () {
            this.bindEvent();
        },
        bindEvent: function () {
            if (codeUrl != null&&codeUrl!='') {
                var url = codeUrl;
                jQuery('#qrcode').qrcode({width: 200, height: 200, text: url});
            }else{
                showInfo('内容填写错误，请核对','180px',2);
                return false;
            }
            $('.callback').click(function () {
                $.ajax({
                    cache: true,
                    type: "POST",
                    url: 'http://local.his.easyhin.com/api/callback/notify.html',
                    data: {transaction_id: 21}, // 你的formid
                    dataType: 'xml',
                    async: false,
                    success: function (data, textStatus, jqXHR) {
                        alert('success');
                    },
                    error: function () {
                        alert('操作失败');
                    }
                });
            });
        }
    };
    return main;
})
