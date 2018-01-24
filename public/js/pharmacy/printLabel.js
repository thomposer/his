

define(function (require) {
    var common = require('js/lib/common');
    var _self;
    var main = {
        init: function () {
            _self = this;
            this.bindEvent();
        },
        bindEvent: function () {
            _self.addPrintContainer();
            _self.printRecipeLabelModal();
        },
        addPrintContainer: function () {
            var con = $('#recipe-print-container').html();
            if (!con) {
                $('.wrapper').after('<div id="recipe-print-container" class="common-print-container recipe-print-container" style="display:none;"> </div>');
            }
        },
        printRecipeLabelModal: function () {
            var isCommitted = false;//表单是否已经提交标识，默认为false
            $('#printLabelForm').yiiAjaxForm({
                beforeSend: function () {
                    if (isCommitted == false) {
                        isCommitted = true;//提交表单后，将表单是否已经提交标识设置为true
                        return true;//返回true让表单正常提交
                    } else {
                        return false;//返回false那么表单将不提交
                    }
                },
                complete: function () {

                },
                success: function (data) {
                    if (data.errorCode == 0) {
                        recipePrintData = data.recipePrintData;
                        _self.printRecipeLabel(recipePrintData, data.spotConfig);
                        if (isCommitted == true) {
                            $('#ajaxCrudModal').modal('hide');
//                            $.pjax.reload({container: '#crud-datatable-pjax', cache: false, timeout: 5000});  //Reload
                        }
                    } else {
                        isCommitted = false;
                        showInfo(data.msg, '180px', 2);
                    }
                },
            });
        },
        /**
         * [字符串截断函数(中文、数字、字母都算一个字符)]
         * @param  {[string]} str  [原始字符串]
         * @param  {[number]} len  [要截断的长度]
         * @param  {[number]} type [截断方式(1-超出截断，2-超出省略,默认为1)]
         * @return {[string]}      [返回截断之后的字符串]
         */
        subStr: function(str,len,type){
            var newStr = '';
            var strLen = str.length;
            type = type?type:1; // 默认超出截断
            if(strLen <= len){ // 没有超过限制字数时,直接返回原字符
                return str;
            }
            newStr = str.substring(0,len);
            if(type == 2){ // 超出省略
                newStr = newStr + '...';
            }
            return newStr;
        },
        printRecipeLabel: function (recipePrintData, spotConfig) {//打印药品标签
            var container = $('#recipe-print-container');
            var a = '';
            for (var i in recipePrintData) {
                var json = recipePrintData[i];
                a += '<div class="recipe-print-container-parent">';
                a += '<div class="top-left">';
                a += '<div class="text-overflow">姓名：' + htmlEncodeByRegExp(json.userName) + '</div>';
                a += '<div>性别：' + json.sex + '</div>';
                a += '</div>';
                a += '<div class="top-right">';
                a += '<div>病历号：' + json.patientNumber + '</div>';
                a += '<div>年龄：' + json.age + '</div>';
                a += '</div>';
                a += '<div class="clearfix"></div>';
                a += '<div class="top-hr ma-top-hr"></div>';
                a += '<div class="recipe-name-info">' +  htmlEncodeByRegExp(_self.subStr(json.recipeName,20,2)) + '('+htmlEncodeByRegExp(_self.subStr(json.productName,30,2))+')</div>';
                a += '<div>';
                a += '<span class="text-overflow-recipe">' + htmlEncodeByRegExp(_self.subStr(json.specification,15,2)) + '</span>';
                a += '<span class="fr">' + json.unit + '</span>';
                a += '</div>';
                a += '<div class="recipe-print-title-h2"><b>S. </b>' + json.frequency + '</div>';
                a += '<div class="note-fz"><b>用药须知：</b>' + _self.subStr(json.remark,100,2) + '</div>';
                a += '<div class="bottom-info">';
                a += '<div class="ma-bottom-hr  bottom-hr"></div>';
                a += '<div>';
                a += '<span>' + htmlEncodeByRegExp(spotConfig.spot_name) + '</span>';
                a += '<span class="fr">电话：' + htmlEncodeByRegExp(spotConfig.label_tel) + '</span>';
                a += '</div>';
                a += '</div>';
                a += '</div>';
            }
            $('#recipe-print-container').html(a);
            window.print();
        }
    };
    return main;
})