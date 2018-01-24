define(function (require) {

    var main = {
        init: function () {
            $('.disabledClass').attr({'disabled': true}).css('background-color', '#76a6ef');
            $('body').off('click', '.batch-myform').on('click', '.batch-myform', function () {
                main.saveForm(2);
            })
            $('body').off('click', '.batch-myform-print').on('click', '.batch-myform-print', function () {
                main.saveForm(1);
            })

        },
        saveForm: function (isPrint) {
            $('#batch-form').yiiAjaxForm({
                beforeSend: function () {
                },
                complete: function () {

                },
                success: function (data) {
                    var id = getUrlParam('id') + 'pharmacyIndex';
                    if (data.errorCode == 0) {
                        showInfo('操作成功', '100px');
                        if (isPrint == 1) {
                            localStorage.setItem(id, 1);
                            window.setTimeout(function () {
                                window.location.href = completeUrl + "?id=" + getUrlParam('id') + '&autoPrint=1';
                            }, 500);
                        } else {
                            window.setTimeout(function () {
                                window.location.href = completeUrl + "?id=" + getUrlParam('id');
                            }, 500);
                        }

                    } else {
                        showInfo(data.msg, '250px', 2);
                    }
                },
                error: function () {
                    showInfo('网络异常,请稍后再试', '250px', 2);
                }
            });
        }
    };
    return main;
});