define(function (require) {
    var common = require('js/lib/common');
    var _self;
    var once = 0;
    var main = {
        canReadStatus: 1,
        init: function () {
            _self = this;
            this.bindEvent();
        },
        bindEvent: function () {
            _self.canRead();
            _self.generate();
            jsonFormInit = $("form").serialize();//为了表单验证
            _self.check();
        },
        generate: function () {  //发药
            $('body').off('click', '.qrcode-view').on('click', '.qrcode-view', function (e) {
                e.preventDefault();
                var formInfo = $('#qrcode-view').serialize();
                var appid = $('#paymentconfig-appid').val();
                var mchid = $('#paymentconfig-mchid').val();
                var payment_key = $('#paymentconfig-payment_key').val();
                if (!appid || !mchid || !payment_key) {
                    showInfo('请填写完整相关信息', '180px', 2);
                    return false;
                }
                _self.getCode(this, formInfo);
                return;

            });
        },
        codeModal: function (obj, codeUrl) {
            modal.open(obj, 'codeUrl=' + codeUrl + '&sendType=2');
        },
        getCode: function (obj, formInfo) {
            var _csrf = $("[name='_csrf']").val();
            var url = $(obj).data('url');
            $.ajax({
                cache: false,
                type: "POST",
                url: url,
                data: formInfo + '&sendType=1', // 你的formid
                dataType: 'json',
                async: false,
                success: function (data, textStatus, jqXHR) {
                    var code_url = data.code_url;
                    if (code_url !== '' && code_url != null) {
                        _self.reloadForm();
                        once = 1;
                        _self.codeModal(obj, code_url);
                    } else {
                        showInfo('内容填写错误，请核对', '180px', 2);
                        return false;
                    }
                },
                error: function () {
                    showInfo('操作失败', '100px');
                    return false;
                }
            });
        },
        check: function () {
            $('.wechat_update').click(function () {
                $('.form-control').removeAttr('readonly');
                $(this).hide();
                $('.wechat_save').attr('disabled','disabled');
                $('.wechat_save').show();
            });
        },
        canRead: function () {
            if (_self.canReadStatus == 1) {//有数据
                $('.form-control').attr('readonly', 'readonly');
            } else {
                $('.form-control').removeAttr('readonly');
                $('.wechat_save').show();
            }
        },
        reloadForm: function () {
            $('.wechat_save').removeAttr('disabled');
            $('.form-control').attr('readonly', 'readonly');
        }

    };
    return main;
})