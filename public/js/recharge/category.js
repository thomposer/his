/*
 * time: 2017-3-21 18:11:56.
 * author : yu.li.
 */

define(function (require) {
    var common = require('js/lib/common');
    var main = {
        init: function () {
            main.updownContent();
            main.updownCardDesc();
        },
        updownContent: function () {
            var slideHeight = 22; //设置第一行的行高px
            var defHeight = $('#card-wrap').height();
            if (defHeight > slideHeight) {
                $('#card-wrap').height(slideHeight);
                $('#read-more').append('<a href="#" style="float: right">查看更多</a>');
                $('#read-more a').click(function () {
                    $("#card-container").addClass('card-updown');
                    //$("#card-container").css('padding', '10px');


                    //$("#card-container").animate({
                    //    'padding': '10px'
                    //}, 'fast');
                    var curHeight = $('#card-wrap').height();
					$("#card-container").css('border','1px solid #CACFD8');
					$("#card-container").css('z-index','99');
                    if (curHeight == slideHeight) {
                        $('#card-wrap').height(defHeight);
                        //$('#card-wrap').animate({
                        //    'height': defHeight + 'px'
                        //}, '200');
                        //$("#card-container").css('left', '-10px');
                        console.log(slideHeight);
                        console.log(defHeight);
                        $('#read-more a').html('收起详情');
                    } else {
                        $("#card-container").css('z-index','10');
                        //$("#card-container").css('left', '0');
                        $("#card-container").removeClass('card-updown');
                        var length = $("#category-desc").html();
						$("#card-container").css('border','0');
//                        $('#card-wrap').height(slideHeight);
//                        $("#card-container").animate({
//                            'padding': '0'
//                        }, 'fast');
                        //$('#card-wrap').animate({
                        //    'height': slideHeight + 'px'
                        //}, '200');
                        $('#card-wrap').height(slideHeight);
                        $('#read-more a').html('查看更多');
                    }
                    return false;
                });
            }
        },
        updownCardDesc: function () {
            var length = $("#category-desc").html();
            if (length == 0) {
                $("#card-desc-container").html('');
                $("#change-card-category").css('bottom', '0');
                $("#card-desc-container").html(' <span style="font-weight: bold;font-size: 12px;">卡片描述:</span>');
            }
            var cardSlideHeight = 22; //设置第一行的行高px
            var cardDefHeight = $('#card-desc-wrap').height();
            if (cardDefHeight > cardSlideHeight) {
                $('#card-desc-wrap').height(cardSlideHeight);
                $('#card-read-more').append('<a href="#" style="float: right">查看更多</a>');
                $('#card-read-more a').click(function () {

                    $("#card-desc-container").addClass('card-updown');
//                    $("#card-desc-container").css('padding', '10px');
//                    $("#card-desc-container").animate({
//                        'padding': '10px'
//                    }, 'normal');
                    var curDescHeight = $('#card-desc-wrap').height();
                    $("#card-desc-container").css('z-index','98');
                    if (curDescHeight == cardSlideHeight) {
                        $("#card-desc-container").css('border','1px solid #CACFD8');
                        $('#card-desc-wrap').height(cardDefHeight);
//                        $('#card-desc-wrap').animate({
//                            'height': cardDefHeight + 'px'
//                        }, 'normal');
                        $('#card-read-more a').html('收起详情');
                    } else {
                        $("#card-desc-container").css('z-index','0');
                        $("#card-desc-container").removeClass('card-updown');
//                        $('#card-desc-wrap').height(cardSlideHeight);
//                        $("#card-desc-container").animate({
//                            'padding': '0'
//                        }, 'fast');
//                        $('#card-desc-wrap').animate({
//                            'height': cardSlideHeight + 'px'
//                        }, 'normal');
                        $('#card-desc-wrap').height(cardSlideHeight);
                        $("#card-desc-container").css('border','0');
                        $('#card-read-more a').html('查看更多');
                    }
                    return false;
                });
            }
        }

    }
    return main;
});



