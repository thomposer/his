

define(function (require) {
    var _self;
    var main = {
        init: function () {
            _self = this;
            this.bindEvent();
        },
        bindEvent: function () {
            $('body').on('click', '.export-data-button', function () {
                var name = $('#stockinfosearch-name').val();
                var begin_time = $('#stockinfosearch-begin_time').val();
                var end_time = $('#stockinfosearch-end_time').val();
                var href = $('#J-select-box').find('.active').find('a').attr('href');

                var statusArr = href.split("=");
                var status = statusArr[1];

                var queryString = [
                    "StockInfoSearch[name]=" + name,
                    "StockInfoSearch[begin_time]=" + begin_time,
                    "StockInfoSearch[end_time]=" + end_time,
                    "ValidSearch[status]=" + status,
                ];
                queryString = queryString.join("&");
                queryString = '?'+queryString;
                var exportDataUrl = baseUrl + pharmacyIndexStockExportData + queryString;
                window.location.href = exportDataUrl;

            });
        }
    };
    return main;
})