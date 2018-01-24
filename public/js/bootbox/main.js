yii.confirm = function (message, ok, cancel) {
    var confirm_delete = $(this).attr('delete');
    var title = $(this).attr('title');
    var confirm_word = $(this).attr('data-confirm-ok');
    var cancel_word = $(this).attr('data-confirm-cancel');
    console.log();
    var confirm_option = {
        label: confirm_word?confirm_word:'确定',
        className: 'btn-default btn-form',
    };
    var cancel_option = {
        label: cancel_word?cancel_word:"取消",
        className: 'btn-cancel btn-form',
    };
    if ( ! (typeof (confirm_delete) === "undefined") ) {
        confirm_option['className'] = 'btn-cancel btn-form';
        cancel_option['className'] = 'btn-default btn-form';
    }
    
    if (typeof (title) === "undefined" || title === "") {
        title = '系统提示';
    }
    
    var btns = {
        cancel: cancel_option,
        confirm: confirm_option,
    };
    
    bootbox.confirm(
            {
                message: message,
                title: title,
                buttons: btns,
                callback: function (confirmed) {
                    if (confirmed) {
                        !ok || ok();
                    } else {
                        !cancel || cancel();
                    }
                }
            }
    );
    // confirm will always return false on the first call
    // to cancel click handler
    return false;
}