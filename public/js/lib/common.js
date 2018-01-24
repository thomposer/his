
/**
 * 
 * @param 提示语 msg
 * @param 显示宽度 width
 * @param type 默认为1成功 2为失败
 * @returns 弹框提示
 */
function showInfo(msg, width) {
    var t = arguments[2] ? arguments[2] : 1;
    var w = $('widow').width() / 2 + 200 + 'px';
    var bg = (t == 1) ? '#757E8D' : '#D13C00';
    var title = (t == 1) ? '操作成功' : '操作失败';
    var msg_title = arguments[3] ? arguments[3] : title;
    if (t == 1) {
        var html = '<div class="flex j_center" id="show_tips_box" style="display:none;opacity:0.9;z-index:11111111;line-height: 68px;text-align: center;border-radius: 6px;height: 68px;background-color:' + bg + ' ;position: fixed;top: 0;left: 0;right: 0;bottom: 0;margin: auto;width: ' + width + '">';
        html += '<p style="color: #FFF;font-size: 20px;">' + msg + '</p>';
        html += '</div>';
    } else {
        var html = '<div class="flex j_center" id="show_tips_box" style="display:none;opacity:0.8;z-index:11111111;line-height: 18px;text-align: center;border-radius: 6px;height: 68px;background-color:' + bg + ' ;position: fixed;top: 0;left: 0;right: 0;bottom: 0;margin: auto;width: ' + width + '">';
        html += '<p style="color: #FFF;font-size: 22px;margin-top:13px;">' + msg_title + '</p>';
        html += '<p style="color: #FFF;font-size: 14px;">' + msg + '</p>';
        html += '</p></div>';
    }
    $("body").append(html);
    $('#show_tips_box').fadeIn(500);
    window.setTimeout(function () {
        $('#show_tips_box').fadeOut(800).remove();
    }, 2500);

}
//制保留2位小数，如：2，会在2后面补上00.即2.00 
function toDecimal2(x) {

    var f = parseFloat(x);
    if (isNaN(f)) {
        return false;
    }
    var s = f.toString();
    console.log(s, 'sss');
    if (s.indexOf('.') != '-1') {
        var arr = s.split('.');
        if (arr[1].length == 1) {
            var decimal = arr[1] + '0';
        } else {
            var decimal = arr[1].substring(0, 2);
        }
        return arr[0] + '.' + decimal;
    } else {
        return f + '.00';
    }
}
//加法
function add(a, b) {
    var c, d, e;
    try {
        c = a.toString().split(".")[1].length;
    } catch (f) {
        c = 0;
    }
    try {
        d = b.toString().split(".")[1].length;
    } catch (f) {
        d = 0;
    }
    return e = Math.pow(10, Math.max(c, d)), (mul(a, e) + mul(b, e)) / e;
}
//减法 
function sub(a, b) {
    var c, d, e;
    try {
        c = a.toString().split(".")[1].length;
    } catch (f) {
        c = 0;
    }
    try {
        d = b.toString().split(".")[1].length;
    } catch (f) {
        d = 0;
    }
    return e = Math.pow(10, Math.max(c, d)), (mul(a, e) - mul(b, e)) / e;
}
//乘法 
function mul(a, b) {
    var c = 0,
            d = a.toString(),
            e = b.toString();
    try {
        c += d.split(".")[1].length;
    } catch (f) {
    }
    try {
        c += e.split(".")[1].length;
    } catch (f) {
    }
    return Number(d.replace(".", "")) * Number(e.replace(".", "")) / Math.pow(10, c);
}
//除法
function div(a, b) {
    var c, d, e = 0,
            f = 0;
    try {
        e = a.toString().split(".")[1].length;
    } catch (g) {
    }
    try {
        f = b.toString().split(".")[1].length;
    } catch (g) {
    }
    c = Number(a.toString().replace(".", ""));
    d = Number(b.toString().replace(".", ""));
    var h = (c / d).toString();
    if ((h.indexOf('e') != -1) || (h.indexOf('E') != -1)) {
        h = new Number(h);
        h = h.toLocaleString();
    }
    return mul(parseFloat(h), Math.pow(10, f - e));
}
//错误提示
//str不传时为隐藏
function showValidity($this, str) {
    var a = '',
            $father = $this.parent();
    if (str) {
        if (!$father.find('.easyhin-valid-error').length) {
            a += '<div class="easyhin-valid-error"><i class="fa icon-info-sign"></i>' + str + '</div>';
            if ($father.css('position') == 'static') {
                $father.css('position', 'relative');
            }
            var thisData = $this.data('sourceDate', {
                "borderColor": colorRgbToHex($this.css('border-top-color'))
            });
            $this.css('border-color', '#FF4B00');
            $this.after(a);
        }
    } else {
        var thisData = $this.data('sourceDate');
        if (thisData) {
            $this.css('border-color', thisData.borderColor);
            $this.removeData('sourceDate');
            $father.find('.easyhin-valid-error').remove();
        }
    }
}
function showCloseValidity($this, str) {
    var a = '',
            $father = $this.parent().parent();
    if (str) {
        if (!$('.has-error').length) {
//            a += '<div class="easyhin-valid-error"><i class="fa icon-info-sign"></i>' + str + '</div>';
//            if ($father.css('position') == 'static') {
//                $father.css('position', 'relative');
//            }
//            var thisData = $this.data('sourceDate', {
//                "borderColor": colorRgbToHex($this.css('border-top-color'))
//            });
//            $this.css('border-color', '#FF4B00');
            $father.parent().addClass('has-error');
            $father.find('.help-block').html(str);
        }
    } else {
        $father.parent().removeClass('has-error');
        $father.find('.help-block').html('');
    }
}

function showAllergyValidity($this, str) {
    var $father = $this.parent();
    if (str) {
        if (!$('.has-error').length) {
            $father.addClass('has-error');
            $father.find('.help-block').html(str);
        }
    } else {
        $father.removeClass('has-error');
        $father.find('.help-block').html('');
    }
}
//颜色rgb转16进制
function colorRgbToHex(rgb) {
    rgb = rgb.match(/^rgb\((\d+),\s*(\d+),\s*(\d+)\)$/);
    function hex(x) {
        return ("0" + parseInt(x).toString(16)).slice(-2);
    }
    rgb = "#" + hex(rgb[1]) + hex(rgb[2]) + hex(rgb[3]);
    return rgb;
}
//时间转换为中文
function formatDate(date, week) {
    var myyear = date.getFullYear();
    var mymonth = date.getMonth() + 1;
    var myweekday = date.getDate();

    if (mymonth < 10) {
        mymonth = "0" + mymonth;
    }
    if (myweekday < 10) {
        myweekday = "0" + myweekday;
    }
    var time = (myyear + "-" + mymonth + "-" + myweekday);
    if (week) {
        var dayNames = ["周日", "周一", "周二", "周三", "周四", "周五", "周六"];
        return {
            "time": time,
            "dayname": dayNames[date.getDay()]
        }
    }
    return time;
}
/*用正则表达式实现html转码*/
function htmlEncodeByRegExp(str) {
    var s = "";
    if (!str) {
        return '';
    }
    if (str.length == 0)
        return "";
    s = str.replace(/&/g, "&amp;");
    s = s.replace(/</g, "&lt;");
    s = s.replace(/>/g, "&gt;");
    s = s.replace(/ /g, "&nbsp;");
    s = s.replace(/\'/g, "&#39;");
    s = s.replace(/\"/g, "&quot;");
    return s;
}
/*2.用正则表达式实现html解码*/
function htmlDecodeByRegExp(str) {
    var s = "";
    if (!str) {
        return '';
    }
    if (str.length == 0)
        return "";
    s = str.replace(/&amp;/g, "&");
    s = s.replace(/&lt;/g, "<");
    s = s.replace(/&gt;/g, ">");
    s = s.replace(/&nbsp;/g, " ");
    s = s.replace(/&#39;/g, "\'");
    s = s.replace(/&quot;/g, "\"");
    return s;
}

//获取url中的参数
function getUrlParam(name) {
    var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)"); //构造一个含有目标参数的正则表达式对象
    var r = window.location.search.substr(1).match(reg); //匹配目标参数
    if (r != null)
        return unescape(r[2]);
    return null; //返回参数值
}

// 从 canvas 提取图片 image
function convertCanvasToImage(canvas) {
    //新Image对象，可以理解为DOM
    var image = new Image();
    // canvas.toDataURL 返回的是一串Base64编码的URL，当然,浏览器自己肯定支持
    // 指定格式 PNG
    image.src = canvas.toDataURL("image/png");
    return image;
}

function tabSwift(parentDomName) {
    $('.list').find('li').unbind('click').click(function () {
        var parentDom = $(this).parents('.' + parentDomName);
        parentDom.find('li').removeClass('current');
        $(this).addClass('current');
        parentDom.find('.detail-view').addClass('hidden');
        parentDom.find('.' + $(this).attr('target')).removeClass('hidden');
    });
}
//验证值是否为数字。是则返回true。否则返回false
function isNum(num) {
    var reNum = /^\s*[-+]?[0-9]*\.?[0-9]+([eE][-+]?[0-9]+)?\s*$/;
    return(reNum.test(num));
}

function CertificateNoParse(certificateNo) {
    var pat = /^\d{6}(((19|20)\d{2}(0[1-9]|1[0-2])(0[1-9]|[1-2][0-9]|3[0-1])\d{3}([0-9]|x|X))|(\d{2}(0[1-9]|1[0-2])(0[1-9]|[1-2][0-9]|3[0-1])\d{3}))$/;
    if (!pat.test(certificateNo))
        return null;

    var parseInner = function (certificateNo, idxSexStart, birthYearSpan) {
        var res = {};

        var year = (birthYearSpan == 2 ? '19' : '') +
                certificateNo.substr(6, birthYearSpan);
        var month = certificateNo.substr(6 + birthYearSpan, 2);
        var day = certificateNo.substr(8 + birthYearSpan, 2);
        res.birthday = year + '-' + month + '-' + day;
        return res;
    };

    return parseInner(certificateNo, certificateNo.length == 15 ? 14 : 16, certificateNo.length == 15 ? 2 : 4);
}
;
/**
 * [搜索高亮效果]
 * @param  {[string]} matchStr [要搜索的字符串]
 * @param  {[string]} result   [搜索出来的结果]
 * @return {[string]}          [返回修饰过的html]
 */
function searchHighlight(matchStr, result) {
    result = htmlDecodeByRegExp(result);
    var showHtml = '';//显示在下拉列表里的html(一列)
    var reg = null;//要替换的正则内容
    var pattern = new RegExp("[`~!@#$^&*%()+=|{}':;',\\[\\].<>/?~！@#￥……&*（）——|{}【】‘；：”“'。，、？]", "g");

    matchStr = matchStr.replace(pattern, function (special) {//对特殊字符进行处理
        return '\\' + special;
    });

    reg = new RegExp(matchStr, "gi");


    function replaceFuc(str) {
        var replaceHtml = '';//将要替换成的html(即高亮的地方)
        replaceHtml += '<span style="color:#76a6ef;text-decoration:underline;">';
        replaceHtml += htmlEncodeByRegExp(str);
        replaceHtml += '</span>';
        return replaceHtml;
    }

    showHtml = result.replace(reg, function (str) {
        return replaceFuc(str);
    });
    return showHtml;
}

function mbsubstr(str, n) {
    var r = /[^\x00-\xff]/g;
    if (str.replace(r, "mm").length <= n) {
        return str;
    }
    var m = Math.floor(n / 2);
    for (var i = m; i < str.length; i++) {
        if (str.substr(0, i).replace(r, "mm").length >= n) {
            return str.substr(0, i);
        }
    }
    return str;
} 

/**
 * [input数值精确度控制函数]
 * @param  {[object]} ele [元素的jq对象]
 * @param  {[number]} dig [精确的位数]
 * @return NULL
 */
function accuracyControl(ele,dig){
    var digStr = '';
    for (var i = 0; i < dig; i++) {
        digStr += '\\d';
    }
    var regStr = "^(\\d+)\\.("+digStr+").*$"; //构造的正则表达式
    var reg = new RegExp(regStr);
    ele.val(ele.val().replace(/^0*(0\.|[1-9])/,'$1'));//解决 粘贴不生效
    ele.val(ele.val().replace(/^\./,''));//
    ele.val(ele.val().replace(/[^\d.]/g,''));//清除“数字”和“.”以外的字符 
    ele.val(ele.val().replace(/\.{2,}/g,'.'));//只保留第一个. 清除多余的
    ele.val(ele.val().replace(".","$#$").replace(/\./g,"").replace("$#$","."));
    ele.val(ele.val().replace(reg,'$1.$2'));
}

function highLightKeywords(text, words, tag) {
	tag = tag || 'span';// 默认的标签，如果没有指定，使用span
	var i, len = words.length, re;
    var result = '';
    if(words == ""){
        return text;
    }
    if(typeof(text) != 'undefined'){
        //匹配整个关键词 不拆分
        var pattern = new RegExp("[`~!@#$^&*%()+=|{}':;',\\[\\].<>/?~！@#￥……&*（）——|{}【】‘；：”“'。，、？]", "gi");

        words = words.replace(pattern, function (special) {//对特殊字符进行处理
            return '\\' + special;
        });
        re = new RegExp(words, 'gi');
        if (re.test(text)) {
            // text = text.replace(re, '<'+ tag +' class="highlight">$&</'+ tag +'>');
            var matchWord = text.match(re);
            for (i=0;i<matchWord.length ;i++ ){
               var testArray = text.split(matchWord[i]);
               result +=   htmlEncodeByRegExp(testArray[i])+  '<'+ tag +' class="highlight">'+htmlEncodeByRegExp(matchWord[i])+'</'+ tag +'>';
               if(i == testArray.length - 2){
                    result +=  htmlEncodeByRegExp(testArray[i+1]);
                }
            }
        }else{
            return htmlEncodeByRegExp(text);
        }
     
    }

	return result;
}
