define(function (require) {
    var template = require('template');
    var jqprint = require('js/lib/jquery.jqprint-0.3');
    var recipeprinkTpl = require('tpl/recipeprink.tpl');
    var recipePrintA5Tpl = require('tpl/recipePrintA5.tpl');
    var common = require('js/lib/common');
    var _self;
    var inspectSpecimen = '';
    var main = {
//        init: function () {
//            _self = this;
//            this.bindEvent();
//        },
//        bindEvent: function () {
//            _self.recipePrint();
//        },
        addPrintContainer: function () {
            if ($('.recipe-print-container').length < 1) {
                $('.wrapper').after('<div id="recipe-print-container" class="common-print-container recipe-print-container" style="display:none;"> </div>');
            }
        },
        print: function (record_id, recipe_id, recipePrinkInfoUrl, options) {
            main.addPrintContainer();
            var filterType = options.filterType || 0;
            $.ajax({
                type: 'post',
                url: recipePrinkInfoUrl,
                data: {
                    'id': recipe_id,
                    'record_id': record_id,
                    'filterType': filterType,
                },
                dataType: 'json',
                success: function (json) {
                    if (json['errorCode'] == 1001) {
                        showInfo(json['msg'], '180px', 2);
                        return;
                    }
                    var label = main.getLabelByType(filterType); // 儿科 or 精二
                    var spotInfo = json['spotInfo'];
                    var recipeRecordDataProvider = json['recipeRecordDataProvider'];
                    var PharmcyRepiceInfo = json['PharmcyRepiceInfo'];
                    var totalPrice = json['totalPrice'];
                    var firstCheck = json['firstCheck'];
                    var allergy = json['allergy'];
                    var spotConfig = json['spotConfig'];
                    var triageInfo = json['triageInfo'];
                    var printType = json.spotConfig.recipe_rebate;//打印类型 1-A4打印 2-A5打印
                    var recipePrintTpl;
                    var logo_img = '';
                    var pageSize; //单页打印的数量
                    if(printType == 1){//A4
                        pageSize = 2;
                        recipePrintTpl = recipeprinkTpl; //A4打印的Tpl
                        if (spotConfig.logo_shape == 1) {
                            logo_img = "clinic-img"
                        } else {
                            logo_img = "clinic-img-long"
                        }
                    }else if(printType == 2){//A5
                        pageSize = 4;
                        recipePrintTpl = recipePrintA5Tpl; //A5打印的Tpl
                        if (spotConfig.logo_shape == 1) {
                            logo_img = "clinic-img-A5"
                        } else {
                            logo_img = "clinic-img-long-A5"
                        }
                    }
                    template.helper('mbsubstr', function (str, sub_length) {
                        // var  = 80 ;
                        if (str == "" || str == null)
                            return "";
                        var temp1 = str.replace(/[^\x00-\xff]/g, "**"); //精髓   
                        var temp2 = temp1.substring(0, sub_length);
                        //找出有多少个*   
                        var x_length = temp2.split("\*").length - 1;
                        var hanzi_num = x_length / 2;
                        sub_length = sub_length - hanzi_num; //实际需要sub的长度是总长度-汉字长度   
                        var res = str.substring(0, sub_length);
                        if (sub_length < str.length) {
                            var end = res;
                        } else {
                            var end = res;
                        }
                        return end;
                    });
                    var a = '';
                    
                    var pageCount = Math.ceil(recipeRecordDataProvider.length / pageSize);
                    for (var i = 1; i <= pageCount; i++) {
                        var offset = (i - 1) * pageSize;
                        var jsonArr = [];
                        var single_total_price = 0;
                        for (var j = 0; j < pageSize; j++) {
                            if (recipeRecordDataProvider[offset + j] == undefined) {
                                break;
                            } else {
                                single_total_price = toDecimal2(add(recipeRecordDataProvider[offset + j].single_total_price, single_total_price));// recipeRecordDataProvider[offset + j].single_total_price;
                                jsonArr.push(recipeRecordDataProvider[offset + j]);
                            }
                        }
                        var recipeNo = main.preFixInterge(i, 3);
                        var prinkRecipeRecordInfoModel = template.compile(recipePrintTpl)({
                            soptInfo: spotInfo,
                            triageInfo: triageInfo,
                            repiceInfo: PharmcyRepiceInfo,
                            recipeRecordDataProvider: jsonArr,
                            getCureRecord: options.getCureRecord,
                            getStatusOtherDesc: options.getStatusOtherDesc,
                            record_id: record_id,
                            totalPrice: totalPrice,
                            firstCheck: firstCheck,
                            allergy: allergy,
                            cdnHost: options.cdnHost,
                            baseUrl: options.baseUrl,
                            spotConfig: spotConfig,
                            logo_img: logo_img,
                            recipeNo: recipeNo,
                            single_total_price: single_total_price,
                            label: label
                        });
                        a += prinkRecipeRecordInfoModel;
                    }
                    $('#recipe-print-container').html(a);
                    setTimeout(function () {
                        window.print();
                    }, 100);
                },
                error: function () {
                },
            });
        },
        getLabelByType: function(type){
            type = type ? type : 2; //默认儿科
            if(type == 1){
                return '精二';
            }
            if(type == 2){
                return '儿科';
            }
        },
        preFixInterge: function (num, n) {
            //num代表传入的数字，n代表要保留的字符的长度  
            return (Array(n).join(0) + num).slice(-n);
        },
    };
    return main;
})