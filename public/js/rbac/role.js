
define(function (require) {
    // 引入模版引擎
    var main = {
        /**
         * [init 初始化]
         * @return {[type]} [description]
         */
        init: function () {

            this.bindEvent();

        },
        /**
         * 绑定事件方法
         */

        bindEvent: function () {
            var me = this;
            $(':checkbox[name="ItemForm[child][]"]').click(function () {
                $(':checkbox', $(this).closest('li')).prop('checked', this.checked);
                var a = this.checked;
//						$(':checkbox[name="ItemForm[child][]"]').each(function () {
//							a = a &&$(this).is(":checked");
//						});;
                if (!a) {
                    $(this).parents('.child-node').find('.child-node-title').children('input').attr("checked", false);
                    $(this).parents('.parent-node').find('.parent-node-title').children('input').attr("checked", false);
                }
                $(this).prop('checked', this.checked);
            });

        },
    }
    return main;
})