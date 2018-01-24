/**
 * Created by Administrator on 2017/1/19.
 */


define(function (require) {
    var common = require('js/lib/common');
    var main = {
        init: function () {
            this.bindEvent();
        },
        bindEvent: function () {
            this.trashFile();
            this.checkBoardFile();
        },
        checkBoardFile:function(){
            $('body').on('click', '#board-file', function () {
                var board_name = $('#board_file_name').val();
                if(board_name == ''){
                    showInfo("请上传附件","250px");
                    return false;
                }

            });

        },

        trashFile:function(){
            $('body').on('click', '#trash-file', function () {
                $('#board_name').val('');
                $('#board_file_name').val('');
                $('#board_size').val('');
                $('#board_type').val('');
                $('#board_url').val('');
            });
        }

    };
    return main;
})