define(function (require) {
    var common = require('js/lib/common');
    var main = {
        init: function () {
            this.bindEvent();
        },
        bindEvent: function () {
            $('body').off('change', '#medicineitem-indicationselect').on('change', '#medicineitem-indicationselect', function () {
                var medicineItemId = $(this).val();
                $.ajax({
                    type: 'post',
                    url: getItemUrl,
                    data: {
                        'id': medicineItemId,
                    },
                    success: function (json) {
                        if (json.errorCode == 0) {
                            var data = json.list;
                            $('#delete-item').attr({'href': medicineIndexDeleteItem + '?id=' + data.id});
                            $('#medicineitem-id').val(data.id);
                            $('#medicineitem-used').text(data.used);
                            $('#medicineitem-side_effect').text(data.side_effect);
                            $('#medicineitem-renal_description').text(data.renal_description);
                            $('#medicineitem-pregnant_woman').text(data.pregnant_woman);
                            $('#medicineitem-liver_description').text(data.liver_description);
                            $('#medicineitem-contraindication').text(data.contraindication);
                            $('#medicineitem-careful').text(data.careful);
                            $('#medicineitem-breast').text(data.breast);
                        } else {
                            showInfo(json.msg, '200px', 2);
                        }
                    },
                    error: function () {

                    },
                    dataType: 'json'

                });
            });
            $('body').on('click', ".btn-cancel,.close", function () {
                window.location.reload();
            })
        },
    };
    return main;
})