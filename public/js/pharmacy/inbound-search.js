

define(function (require) {
    var template = require('template');
    var _self;
    var main = {
        init: function () {
            _self = this;
            this.bindEvent();
        },
        bindEvent: function () {

            $('body').on('mousemove','.date .form-control',function(){
                $('.date .form-control').datepicker({
                    format: 'yyyy-mm-dd',
                    language : 'zh-CN',
                    inline : false,
                    autoclose : true
                })

            });
            

        },
    
    };
    return main;
})