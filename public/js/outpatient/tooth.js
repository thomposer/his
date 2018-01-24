/*
 * time: 2017-7-19 9:57:42.
 * author : yu.li.
 */

define(function (require) {
    var _self;
    var template = require('template');
    var teethSelectedTpl = require('tpl/outpatient/teethSelected.tpl');
    var dentalCheckContentTpl = require('tpl/outpatient/dentalCheck.tpl');
    var teethTpl = require('tpl/teeth.tpl');
    var teethImgTpl = require('tpl/teethImg.tpl');//牙位图打印tpl模板
    var orthodonticsFirstRecordTpl = require('tpl/outpatient/orthodonticsFirstRecord.tpl');//正畸初诊病历打印
    hasTeeth = 1;//1代表正常状态没有牙齿弹窗 2代表有一个牙齿弹窗
    var defaultValue = ['1','2','3','4','5','6','7','8','A','B','C','D','E'];
    var main = {
        init: function () {
            _self = this;
            this.bindEvent();
            // this.babyTeethFun();
        },

        /**
         * [获得图片位置信息]
         * @param  {[string]} target [牙齿所处的位置]
         * @param  {[number]} type   [类型,恒牙为1,乳牙为2]
         * @param  {[number]} j      [循环的index]
         * @return {[number]}        [返回x轴定位的px值]
         */
        getPosition: function(target,type,j){
            var c;
            if(target === 'left_top' || target === 'left_bottom'){//左上和左下
                if(type === 1){
                    c = -48*j;
                }else{
                    c = -48*j-144;
                }
            }else {//右上和右下
                c = -(384+48*j+18);
            }
            return c;
        },
        //初始化恒牙数组
        permanentTeethFun: function (value) {
            var target = ['left_top', 'right_top', 'left_bottom', 'right_bottom'];
            var a = [], type, n;
            for (var i = 0; i < 4; i++) {
                if (i == 1 || i == 3) {
                    type = 2;
                    n = 1;
                } else {
                    type = 1;
                    n = 8;
                }

                var b = [];
                for (var j = 0; j < 8; j++) {
                    b[j] = {
                        position:_self.getPosition(target[i],1,j),//图片的x轴定位
                        value: n, //牙齿对应的值
                        selected: value || 0//是否被选中 1是选中 0是未选中
                    };
                    type == 1 ? n-- : n++;
                    // n--;
                }
                a[i] = b;
            }
            return a;
        },
        //初始化乳牙数组
        babyTeethFun: function (value) {
            var target = ['left_top', 'right_top', 'left_bottom', 'right_bottom'];
            var valueArr = ['', 'A', 'B', 'C', 'D', 'E'];
            var a = [], type, n;
            for (var i = 0; i < 4; i++) {
                if (i == 1 || i == 3) {
                    type = 2;
                    n = 1;
                } else {
                    type = 1;
                    n = 5;
                }

                var b = [];
                for (var j = 0; j < 5; j++) {
                    b[j] = {
                        position: _self.getPosition(target[i],2,j),
                        value: valueArr[n], //牙齿对应的值
                        selected: value || 0//是否被选中 1是选中 0是未选中
                    };
                    type == 1 ? n-- : n++;
                    // n--;
                }
                a[i] = b;
            }
            return a;
        },
        bindEvent: function () {
            _self.addTooth();
            _self.setTeethSelectDisable();
            $('body').on('click','.btn-teeth-confirm',function(){
                 var id = $(this).attr('name');
                _self.TeethImgPrint(id);
            });
            $('.print-teeth-record').click(function(){
                 var id = $(this).attr('name');
                _self.TeethPrint(id);
            });
            // 正畸初诊病历打印
            $('.print-orthodontics-record').click(function(){
                var id = $(this).attr('name');
                _self.orthodonticsFirstRecordPrint(id);
            });
            $('body').off('click', '.dental-check-delete').on('click', '.dental-check-delete', function () {//移除当前项，判断剩余项目是否输入正确，正确则隐藏错误提示
                var obj = $(this).parents('.dental-check');
                $(this).parents('.dental-check-content').remove();
                var inputArr = obj.find('.dental-check-input');
                var allRet = true;
                inputArr.each(function () {
                    if ($(this).attr('data-value') == 2) {//data-value 1-输入正确 2-输入错误
                        allRet = false;
                        return false;//跳出循环
                    }
                });
                if (allRet) {
                    obj.find('.help-block').hide();//所有input框填写正确，隐藏错误提示
                }
            });
            $('body').off('click','.dental-check-title').on('click','.dental-check-title',function(){//点击编辑
                if ($(this).parent('.dental-check').find('.box-container')) {//判断当前检查项是否有弹窗，有则移除
                    $(this).parent('.dental-check').find('.box-container').remove();
                    if ($(this).parents('.basic-form-content').find('.box-container').length == 0) {//判断页面是否有牙位弹窗，更新hasTeeth
                        hasTeeth = 1;
                    }

                }
                $(this).attr('data-value',1);// data-value 1-为输入态  2为弹窗态
                $(this).parent('.dental-check').find('.tooth-position').unbind('click');
                //隐藏弹窗态相关内容，显示输入态相关内容
                $(this).parent('.dental-check').find('span').hide();
                $(this).parent('.dental-check').find('.dental-check-input').show();
                $(this).parent('.dental-check').find('.has-dental-disease').show();
                $(this).parent('.dental-check').find('.dental-disease-show').hide();
                //牙位图同步
                var sysc = $('.tooth-sysc').attr('checked');
                if(sysc == 'checked'){
                    $(this).parent('.dental-check').find('.dental-check-btn').find('.dental-check-btn-commit').html('保存并同步');
                }else{
                    $(this).parent('.dental-check').find('.dental-check-btn').find('.dental-check-btn-commit').html('保存');
                }
                $(this).parent('.dental-check').find('.dental-check-btn').show();
                //编辑态时不能再更换牙位同步状态
                $('.tooth-sysc').attr({'disabled': true});
            });
            $('body').off('click','.dental-check-btn-commit').on('click','.dental-check-btn-commit',function(){//点击保存
                if(!_self.validateAll($(this))){//验证所有输入框
                    return false;
                }
               if(recordType ==4 || recordType ==5){
                   if(!_self.validateDentalDisease($(this))){//验证病症框
                       return false;
                   }
               }
                $(this).parents('.dental-check').find('.dental-check-title').attr('data-value',2);//修改状态为弹窗态
                var positionArr = $(this).parents('.dental-check').find('.tooth-position');
                //同步输入态数据到弹窗态
                positionArr.each(function(){ //四个框
                    var leftTopValue = $(this).find('.left-top-input').val();
                    leftTopValue = _self.sortData(leftTopValue,1);//输入排序
                    $(this).find('.left-top-text').html(leftTopValue);//显示
                    $(this).find('.left-top-input').attr('value',leftTopValue);//值


                    var rightTopValue = $(this).find('.right-top-input').val();
                    rightTopValue = _self.sortData(rightTopValue,2);
                    $(this).find('.right-top-text').html(rightTopValue);
                    $(this).find('.right-top-input').attr('value',rightTopValue);

                    var leftBottomValue = $(this).find('.left-bottom-input').val();
                    leftBottomValue = _self.sortData(leftBottomValue,1);
                    $(this).find('.left-bottom-text').html(leftBottomValue);
                    $(this).find('.left-bottom-input').attr('value',leftBottomValue);

                    var rightBottomValue = $(this).find('.right-bottom-input').val();
                    rightBottomValue = _self.sortData(rightBottomValue,2);
                    $(this).find('.right-bottom-text').html(rightBottomValue);
                    $(this).find('.right-bottom-input').attr('value',rightBottomValue);
                    if(leftTopValue || rightTopValue || leftBottomValue || rightBottomValue){
                        var position = leftTopValue + ',' + rightTopValue + ',' + rightBottomValue + ',' + leftBottomValue;
                        $(this).parent('.dental-check-content').find('.dental-history-relation-position').val(position);//拼接四个输入框的值
                    }else{
                        $(this).parent('.dental-check-content').find('.dental-history-relation-position').val('');//空
                    }

                    //牙位不为空。展示牙位病症
                    if(leftTopValue  || rightTopValue  || leftBottomValue  || rightBottomValue){
                        var dentalDiseaseValue = $(this).parent('.dental-check-content').find('.has-dental-disease').find("option:selected").val();
                        $(this).parent('.dental-check-content').find('.has-dental-disease').hide();
                        var diseaseDescription = '';
                        if(typeof(dentalDiseaseType[dentalDiseaseValue]) !="undefined"){
                            diseaseDescription = dentalDiseaseType[dentalDiseaseValue];
                        }
                        $(this).parent('.dental-check-content').find('.dental-disease-show').html('病症：'+diseaseDescription);
                        $(this).parent('.dental-check-content').find('.dental-disease-show').show();
                    }
                    //牙位为空，清楚病症选择项
                    if(!(leftTopValue  || rightTopValue  || leftBottomValue  || rightBottomValue) ){
                        $(this).parent('.dental-check-content').find('.has-dental-disease').find("option:selected").removeAttr('selected');
                        $(this).parent('.dental-check-content').find('.has-dental-disease').hide();
                        $(this).parent('.dental-check-content').find('.dental-disease-show').hide();
                        $(this).parents('.dental-check').find('.help-block-dental-show').hide();
                    }
                });

                //牙位图同步
                var sysc = $('.tooth-sysc').attr('checked');
                //$(this).parents('.dental-check').find('.help-block-dental-show').hide();
                if(sysc) {
                    _self.teethPositionSysc($(this));
                    //显示弹窗态相关内容，隐藏输入态相关内容
                    $(this).parents('.dental-check').find('span').show();
                    $(this).parents('.dental-check').find('.dental-check-input').hide();
                    $(this).parents('.dental-check').find('.dental-check-btn').hide();
                    _self.addSelectedTeeth();//重新绑定弹窗点击事件
                }else{
                    //显示弹窗态相关内容，隐藏输入态相关内容
                    $(this).parents('.dental-check').find('span').show();
                    $(this).parents('.dental-check').find('.dental-check-input').hide();
                    $(this).parents('.dental-check').find('.dental-check-btn').hide();
                    _self.addSelectedTeeth();//重新绑定弹窗点击事件
                }
                //更换牙位同步状态
                if(hasTeeth == 2){
                    $('.tooth-sysc').attr({'disabled': true});
                }else{
                    _self.setTeethStatus($(this));
                }

            });
            $('body').off('click','.dental-check-btn-cancel').on('click','.dental-check-btn-cancel',function(e){
                //if(hasTeeth == 2){
                //    return;
                //}
                var inputArr = $(this).parents('.dental-check').find('.dental-check-input');//所有input框对象
                inputArr.each(function () {//回填值
                    var val = $(this).prev().html();
                    $(this).attr('value',val);
                });
                //显示弹窗态相关内容，隐藏输入态相关内容 去掉错误提示
                $(this).parents('.dental-check').find('.dental-check-title').attr('data-value',2);
                $(this).parents('.dental-check').find('span').show();
                $(this).parents('.dental-check').find('.dental-check-input').hide();
                $(this).parents('.dental-check').find('.dental-check-btn').hide();
                $(this).parents('.dental-check').find('.help-block').hide();
                $(this).parents('.dental-check').find('.dental-check-input').removeClass('dental-check-input-error');//去掉红色边框

                    var positionArr = $(this).parents('.dental-check').find('.tooth-position');
                    //同步输入态数据到弹窗态
                    positionArr.each(function(){ //四个框
                        var leftTopValue = $(this).find('.left-top-input').val();
                        leftTopValue = _self.sortData(leftTopValue,1);//输入排序
                        var rightTopValue = $(this).find('.right-top-input').val();
                        rightTopValue = _self.sortData(rightTopValue,2);
                        var leftBottomValue = $(this).find('.left-bottom-input').val();
                        leftBottomValue = _self.sortData(leftBottomValue,1);
                        var rightBottomValue = $(this).find('.right-bottom-input').val();
                        rightBottomValue = _self.sortData(rightBottomValue,2);
                        //当输入框有值才插入
                        if(leftTopValue  || rightTopValue  || leftBottomValue  || rightBottomValue){
                            $(this).parents('.dental-check').find('.dental-disease-show').show();
                            $(this).parents('.dental-check').find('.has-dental-disease').hide();
                        }else{
                            $(this).parents('.dental-check').find('.dental-disease-show').hide();
                            $(this).parents('.dental-check').find('.has-dental-disease').hide();
                        }
                    });

                //去除病症选择显示错误
                $(this).parents('.dental-check').find('.help-block-dental-show').hide();

                _self.addSelectedTeeth();
                //更换牙位同步状态
                if(hasTeeth == 2){
                    $('.tooth-sysc').attr({'disabled': true});
                }else{
                    _self.setTeethStatus($(this));
                }
            });
            $('body').off('input propertychange', '.dental-check-input').on('input propertychange', '.dental-check-input', function(){//input输入事件  propertychange兼容ie9.0以下版本
                 if(_self.validateSingle($(this))){//验证
                     $(this).attr('data-value',1);//1表示该input框填写错误
                     $(this).removeClass('dental-check-input-error');//去掉红色边框
                     var inputArr = $(this).parents('.dental-check').find('.dental-check-input');//所有input框对象
                     var allRet = true;
                     inputArr.each(function(){
                         if($(this).attr('data-value') == 2){
                             allRet = false;
                             return false;//跳出循环
                         }
                     });
                     if(allRet){
                         $(this).parents('.dental-check').find('.help-block').hide();//所有input框填写正确，隐藏错误提示
                     }
                 }else{
                    $(this).parents('.dental-check').find('.help-block').show();//所有input框填写正确，显示错误提示
                    $(this).addClass('dental-check-input-error');//红色边框
                    $(this).attr('data-value',2);//2表示该input框填写错误
                 }
            });
        },
        validateAll: function(obj){//验证所有input
            var inputArr = obj.parents('.dental-check').find('.dental-check-input');//所有input框对象
            var ret = true;
            inputArr.each(function(){
                if(!_self.validateSingle($(this))){
                    ret = false;
                    $(this).parents('.dental-check').find('.help-block').show();//所有input框填写正确，显示错误提示
                    $(this).addClass('dental-check-input-error');//红色边框
                    $(this).attr('data-value',2);//2表示该input框填写错误
                }
            });
            return ret;
        },
        //验证病症选择状态
        validateDentalDisease:function(obj){
            var positionArr = obj.parents('.dental-check').find('.tooth-position');
            //同步输入态数据到弹窗态
            var ret =true;
            positionArr.each(function(){
                var leftTopValue = $(this).find('.left-top-input').val();
                leftTopValue = _self.sortData(leftTopValue,1);//输入排序

                var rightTopValue = $(this).find('.right-top-input').val();
                rightTopValue = _self.sortData(rightTopValue,2);

                var leftBottomValue = $(this).find('.left-bottom-input').val();
                leftBottomValue = _self.sortData(leftBottomValue,1);

                var rightBottomValue = $(this).find('.right-bottom-input').val();
                rightBottomValue = _self.sortData(rightBottomValue,2);

                if(leftTopValue  || rightTopValue  || leftBottomValue  || rightBottomValue ){
                    var selectedStatus = $(this).parent('.dental-check-content').find(".dental-disease-select option:selected").val();
                    if(!selectedStatus || selectedStatus == 0){
                        //牙位输入框有值，病症为空，提示错误
                        $(this).parents('.dental-check').find('.help-block-dental-show').show();
                        $(this).parent('.dental-check').attr('data-value',2);//2表示该input框填写错误
                        ret =false;
                        return false
                    }else{
                        $(this).parents('.dental-check').find('.help-block-dental-show').hide();
                    }
                }

            });
            return ret;



        },
        validateSingle: function (obj) {//传入input对象
            var map = _self.defaultDataInit();
            var data = obj.val().split("");
            var ret = true;
            if (data.length > 13) {
                ret = false;
            }
            for (var i = 0; i < data.length; i++) {
                if ($.inArray(data[i], defaultValue) == -1) {
                    ret = false;
                } else if (map[data[i]] == 1) {
                    ret = false;
                }
                map[data[i]] += 1;
            }
            return ret;
        },
        defaultDataInit: function(){
            return {"1" : 0,"2" : 0,"3" : 0,"4" : 0,"5" : 0,"6" : 0,"7" : 0,"8" : 0,"A" : 0,"B" : 0,"C" : 0,"D" : 0,"E" : 0}
        },
        sortDataInit: function(type){//初始化排序数据
            if(type == 1){//左边
                return ["8","7","6","5","E","4","D","3","C","2","B","1","A"];
            }else{//右边
                return ["A","1","B","2","C","3","D","4","E","5","6","7","8"];
            }
        },
        sortData: function(str,type){//输入数据按要求排序
            var map = _self.defaultDataInit();//初始化数据
            var sortData = _self.sortDataInit(type);
            var data = str.split("");
            for(var i = 0; i < data.length; i++){
                map[data[i]] += 1;
            }
            var result = '';
            for(var i = 0; i < sortData.length; i++){
                if(map[sortData[i]]){
                    result += sortData[i];
                }
            }
            return result;
        },
        //新增牙位
        addTooth: function () {
            _self.addSelectedTeeth();
            $('body').off('click', '.add-booth-button').on('click', '.add-booth-button', function (e) {
                e.stopPropagation();
                var type = $(this).attr('data-type');
                _self.addDentalCheck(type, $(this));
                if(($(this).parents('.dental-check').find('.dental-check-title').attr('data-value')) == 2){
                    _self.addSelectedTeeth();
                }
            });
        },
        addDentalCheck: function (recordType, obj,special) {
            var dentalCheckContent = template.compile(dentalCheckContentTpl)({
                type: recordType,
                special: special,
                isEdit: obj.parents('.dental-check').find('.dental-check-title').attr('data-value'),//1是编辑框 2否
            });
            obj.parents('.dental-check-content-list').append(dentalCheckContent);


        },
        // 选择牙齿弹窗呼起
        addSelectedTeeth: function () {
            $('.tooth-position').unbind('click').click(function () {
                if($(this).parents('.dental-check').find('.dental-check-title').attr('data-value') == 1){//输入框，禁止弹窗
                    return false;
                }
                //更换牙位同步状态
                $('.tooth-sysc').attr({'disabled': true});
                totalPermanent = 0;//字符串回填弹窗 计算恒牙数量
                totalBaby = 0;//字符串回填弹窗 计算乳牙数量
                var tagertDom = $(this);
                //获取病历里的字符串
                var leftTopText = tagertDom.find('.left-top span').html();
                var rightTopText = tagertDom.find('.right-top span').html();
                var leftBottomText = tagertDom.find('.left-bottom span').html();
                var rightBottomText = tagertDom.find('.right-bottom span').html();

                if (dentalRecordStatus == 1) { //dentalRecordStatus判断页面是保存状态还是修改状态 1：button显示修改 2：button显示保存
                    return false;
                }
                if (hasTeeth == 2) { //2代表页面已经有一个牙齿的弹窗在选择了，其他弹窗不应该被呼出
                    return false;
                }
                permanentTeeth = _self.permanentTeethFun();//初始化恒牙数组
                babyTeeth = _self.babyTeethFun();//初始化乳牙数组
                hasTeeth = 2;
                if (leftTopText || rightTopText || leftBottomText || rightBottomText) { //如果存在字符串就去根据字符串初始化数组，弹窗回填数据
                    _self.getToothStatus(permanentTeeth[0], babyTeeth[0], leftTopText);
                    _self.getToothStatus(permanentTeeth[1], babyTeeth[1], rightTopText);
                    _self.getToothStatus(permanentTeeth[2], babyTeeth[2], leftBottomText);
                    _self.getToothStatus(permanentTeeth[3], babyTeeth[3], rightBottomText);
                }
                //获取牙位同步状态
                var sysc = $('.tooth-sysc').attr('checked');
                //调用TPL 渲染弹窗页面以及初始化绑定事件

                var defaultSel = 1;//打开牙位图model时默认选择
                if(triageInfo.ageOfYear < 12){ //小于12岁 默认选中乳牙
                    defaultSel = 2;
                }else{ //大于等于12岁 默认选中恒牙
                    defaultSel = 1;
                }
                var dentalCheckContent = template.compile(teethSelectedTpl)({
                    baseUrl: baseUrl,
                    sysc: sysc,
                    defaultSel: defaultSel,//默认选择 1-默认选择恒牙 2-默认选择乳牙
                    permanentTeeth: permanentTeeth,
                    babyTeeth: babyTeeth,
                    recordType: recordType,
                });
                $(this).append(dentalCheckContent);

                //获取每个的病症的值
                var dentalDiseaseValue = $(this).parents('.dental-check-content').find('.has-dental-disease').find(".dental-disease-select option:selected").val();
                $(this).find('.box-container').parents('.dental-check-content').find('.dental-disease-window option[value="'+dentalDiseaseValue+'"]').attr("selected","selected");

                if (totalPermanent == 32) {//判断恒牙是否为全口，因为全口为32颗
                    // alert(11);
                    $('#permanent').html('重置');
                    $('#permanent').attr('state', 2);//state为按钮状态，1：重置 2：全口
                } else {
                    $('#permanent').html('全口');
                    $('#permanent').attr('state', 1);
                }

                if (totalBaby == 20) {//判断乳牙是否为全口，因为乳牙全口为20颗
                    $('#baby').html('重置');
                    $('#baby').attr('state', 2);//state为按钮状态，1：重置 2：全口
                } else {
                    $('#baby').html('全口');
                    $('#baby').attr('state', 1);
                }
                $('.tab-panel').unbind('click').click(function (event) { //恒牙乳牙切换
                    event.stopPropagation();
                    var target = $(this).attr('target');
                    var totalBtn = $(this).attr('total-btn');
                    $('.tab-panel').removeClass('active');
                    $('#permanent-teeth').addClass('hidden');
                    $('#baby-teeth').addClass('hidden');
                    $(target).removeClass('hidden');
                    $(this).addClass('active');
                    $('.tab-total').addClass('hidden');
                    $(totalBtn).removeClass('hidden');
                });
                $('.tab-panel').unbind('click').click(function (event) { //恒牙乳牙切换
                    event.stopPropagation();
                    var target = $(this).attr('target');
                    var totalBtn = $(this).attr('total-btn');
                    $('.tab-panel').removeClass('active');
                    $('#permanent-teeth').addClass('hidden');
                    $('#baby-teeth').addClass('hidden');
                    $(target).removeClass('hidden');
                    $(this).addClass('active');
                    $('.tab-total').addClass('hidden');
                    $(totalBtn).removeClass('hidden');
                });
                $('#permanent').unbind('click').click(function (event) { //全口 重置 切换，注意：恒牙配备了一个全口开关，乳牙配备了一个全口开关，彼此相互独立
                    event.stopPropagation();
                    var state = $(this).attr('state');//1是全口 2是重置
                    var target = $('.box-top').find('.active').attr('target');
                    if (state == 1) {
                        $(this).html('重置');
                        $(this).attr('state', 2);
                        $(target).find('.teeth-box').addClass('active');
                        $(target).find('.teeth-value').addClass('blue');
                        $(this).parents('.box-container').find('.permanent-teeth').find('.teeth-box').attr('select', 1);
                        permanentTeeth = _self.permanentTeethFun(1);
                    } else {
                        $(this).html('全口');
                        $(this).attr('state', 1);
                        $(target).find('.teeth-box').removeClass('active');
                        $(target).find('.teeth-value').removeClass('blue');
                        $(this).parents('.box-container').find('.permanent-teeth').find('.teeth-box').attr('select', 0);

                        permanentTeeth = _self.permanentTeethFun();
                    }
                });
                $('#baby').unbind('click').click(function (event) {//全口 重置 切换，注意：恒牙配备了一个全口开关，乳牙配备了一个全口开关，彼此相互独立
                    event.stopPropagation();
                    var state = $(this).attr('state');//1是全口 2是重置
                    var target = $('.box-top').find('.active').attr('target');
                    if (state == 1) {
                        $(this).html('重置');
                        $(this).attr('state', 2);
                        $(target).find('.teeth-box').addClass('active');
                        $(target).find('.teeth-value').addClass('blue');
                        $(this).parents('.box-container').find('.baby-teeth').find('.teeth-box').attr('select', 1);
                        babyTeeth = _self.babyTeethFun(1);

                    } else {
                        $(this).html('全口');
                        $(this).attr('state', 1);
                        $(target).find('.teeth-box').removeClass('active');
                        $(target).find('.teeth-value').removeClass('blue');
                        $(this).parents('.box-container').find('.baby-teeth').find('.teeth-box').attr('select', 0);
                        babyTeeth = _self.babyTeethFun();
                    }
                });
                $('#teeth-close').unbind('click').click(function (event) { //保存按钮
                    hasTeeth = 1;
                    //输出字符串
                    var statusAddPrint = _self.addPrint(tagertDom);
                    if(!statusAddPrint){
                        hasTeeth = 2;
                        return;
                    }
                    event.stopPropagation();
                    _self.setTeethStatus($(this));
                    var saveButton = $(this).parents('.dental-check-content').parents('.dental-check').find('.dental-check-btn-commit');
                    //牙位图同步
                    var sysc = $('.tooth-sysc').attr('checked');
                    if(sysc) {
                        _self.teethPositionSysc(saveButton);
                    }
                    $(this).parents('.box-container').remove();
                    _self.addSelectedTeeth();

                });
                $('.teeth-box').unbind('click').click(function (event) { //点选牙齿
                    //event.stopPropagation();
                    var selected = $(this).attr('select');//selected 为选中态 0：未选中 1：选中
                    var data = $(this).attr('data'); //data为牙齿的值 也是value
                    var target = $(this).attr('target');//判断恒牙的区域 是一象限还是二象限？
                    var targetParent = $(this).attr('target-parent');//判断是恒牙还是乳牙 1：恒牙 2：乳牙
                    if (selected == 0) {
                        $(this).addClass('active');//激活
                        $(this).find('.teeth-value').addClass('blue');//激活
                        $(this).attr('select', 1);//选中
                        // leftTopArr.push(data);
                        _self.correctArr(targetParent, target, data);//修改数组
                    } else {
                        $(this).removeClass('active');
                        $(this).find('.teeth-value').removeClass('blue');
                        $(this).attr('select', 0);
                        _self.removeArr(targetParent, target, data);
                    }
                });
                //取消弹窗 不保存
                $('#cancel').unbind('click').click(function (event) {
                    event.stopPropagation();
                    hasTeeth = 1;
                    _self.setTeethStatus($(this));
                    $('.box-container').remove();
                });
            });
        },
        // 数组输出
        addPrint: function (tagertDom) {

            var leftTop = _self.formatToothOne(permanentTeeth[0], babyTeeth[0]);
            var leftopContent = '<span class="left-top-text">' + leftTop + '</span><input type="text" class="left-top-input dental-check-input" name="" value="' + leftTop + '" style="display: none;" data-value="1">';

            var leftBottom = _self.formatToothOne(permanentTeeth[2], babyTeeth[2]);
            var leftBottomContent = '<span class="left-bottom-text">' + leftBottom + '</span><input type="text" class="left-bottom-input dental-check-input" name="" value="' + leftBottom + '" style="display: none;" data-value="1">';

            var rightTop = _self.formatToothTwo(permanentTeeth[1], babyTeeth[1]);
            var rightTopContent = '<span class="right-top-text">' + rightTop + '</span><input type="text" class="right-top-input dental-check-input" name="" value="' + rightTop + '" style="display: none;" data-value="1">';

            var rightBottom = _self.formatToothTwo(permanentTeeth[3], babyTeeth[3]);
            var rightBottomContent = '<span class="right-bottom-text">' + rightBottom + '</span><input type="text" class="right-bottom-input dental-check-input" name="" value="' + rightBottom + '" style="display: none;" data-value="1">';

            var positionStatus = false;
            if(leftTop || rightTop || rightBottom || leftBottom){
                positionStatus = true;
            }

            //获取弹窗的选择值
            var dentalDisease = tagertDom.find('.box-container').find('.dental-disease-window  option:selected').val();
            //选中了牙齿但是没有选病症
            if(positionStatus){
                if(dentalDisease == '0'){
                    tagertDom.find('.box-container').find('.help-block-dental-show').show();
                    //不能再呼起弹窗
                    return false;
                }else{
                    tagertDom.parent('.dental-check-content').find('.dental-disease-select option[value="'+dentalDisease+'"]').attr("selected","selected");
                    var diseaseDescription = '';
                    if(typeof(dentalDiseaseType[dentalDisease]) !="undefined"){
                        diseaseDescription = dentalDiseaseType[dentalDisease];
                    }
                    tagertDom.parent('.dental-check-content').find('.dental-disease-show').html('病症：'+diseaseDescription);
                    tagertDom.parent('.dental-check-content').find('.has-dental-disease').hide();
                    tagertDom.parent('.dental-check-content').find('.dental-disease-show').show();
                }
            }else{
                //牙位未选中，清除病症的选中选项
                tagertDom.parent('.dental-check-content').find('.dental-disease-select').find("option:selected").removeAttr('selected');
                tagertDom.parent('.dental-check-content').find('.has-dental-disease').hide();
                tagertDom.parent('.dental-check-content').find('.dental-disease-show').hide();
            }

            tagertDom.find('.left-top').html(leftopContent);
            tagertDom.find('.left-bottom').html(leftBottomContent);
            tagertDom.find('.right-top').html(rightTopContent);
            tagertDom.find('.right-bottom').html(rightBottomContent);

            var data = leftTop + ',' + rightTop + ',' + rightBottom + ',' + leftBottom;
            if (leftTop || rightTop || rightBottom || leftBottom) {
                tagertDom.siblings('.dental-history-relation-position').val(data);
            } else {
                tagertDom.siblings('.dental-history-relation-position').val('');
            }

            return true;
        },
        //选一个牙齿 修改一颗牙齿的数据
        correctArr: function (targetParent, target, data) {
            if (targetParent == 1) {
                var obj = permanentTeeth;
            } else {
                var obj = babyTeeth;
            }

            for (var i = 0; i < obj[target].length; i++) {
                if (obj[target][i].value == data) {
                    obj[target][i].selected = 1;
                    return false;
                }
            }
        },
        //选一个牙齿 修改一颗牙齿的数据
        removeArr: function (targetParent, target, data) {
            if (targetParent == 1) {
                var obj = permanentTeeth;
            } else {
                var obj = babyTeeth;
            }

            for (var i = 0; i < obj[target].length; i++) {
                if (obj[target][i].value == data) {
                    obj[target][i].selected = 0;
                    return false;
                }
            }
        },
        formatToothOne: function (permanent, baby) {//格式化输出第一 四  象限牙位图
            var a = '';
            for (var i = 0; i < 8; i++) {
                if (permanent[i].selected == 1) {
                    a += permanent[i].value;
                }
                if (i >= 3 && baby[i - 3].selected == 1) {
                    a += baby[i - 3].value;
                }
            }
            return a;
        },
        formatToothTwo: function (permanent, baby) {//格式化输出第二 三 象限牙位图
            var a = '';
            for (var i = 0; i < 8; i++) {
                if (i <= 4 && baby[i].selected == 1) {
                    a += baby[i].value;
                }
                if (permanent[i].selected == 1) {
                    a += permanent[i].value;
                }
            }
            return a;
        },
        // 初始化牙齿弹窗
        getToothStatus: function (permanent, baby, toothStr) {
            //恒牙
            for (var i = 0; i < 8; i++) {

                if (toothStr.indexOf(permanent[i].value) != -1) {
                    permanent[i].selected = 1;
                    totalPermanent++
                }
            }
            //乳牙
            for (var i = 0; i < 5; i++) {
                if (toothStr.indexOf(baby[i].value) != -1) {
                    baby[i].selected = 1;
                    totalBaby++
                }
            }
        },
        //口腔打印
        TeethPrint: function (id) {
            $.ajax({
                type: 'post',
                url: getDoctorRecordData,
                data: {
                    'record_id': record_id,
                    // 'record_type':1
                },
                dataType: 'json',
                success: function (json) {
                    json = json['data'];
                    // 1 => '口腔检查',
                    // 2 => '辅助检查',
                    // 3 => '诊断',
                    // 4 => '治疗方案',
                    // 5 => '治疗',
                    var userInfo = json.userInfo;
                    var dentalBaseInfo = json.dentalBaseInfo;
                    var dentalRelation = json.dentalRelation;
                    var spotInfo = json.spotInfo;
                    var allergyInfo = json.allergyInfo;
                    var spotConfig = json.spotConfig;
                    var logo_img = '';

                    if(spotConfig.logo_shape == 1){
                        logo_img = "clinic-img"
                    }else{
                        logo_img = "clinic-img-long"
                    }
                    var printTeethModel = template.compile(teethTpl)({
                        userInfo: userInfo,
                        dentalRelation: dentalRelation,
                        dentalBaseInfo: dentalBaseInfo,
                        spotInfo:spotInfo,
                        recordId: record_id,
                        allergyInfo: allergyInfo,
                        cdnHost: cdnHost,
                        baseUrl: baseUrl,
                        spotConfig : spotConfig,
                        logo_img : logo_img,
                    });
                    $('#teeth-print').html(printTeethModel);
                    $('#' + id).jqprint();
                },
                error: function () {
                },
            });
        },
        // 口腔牙位图打印
        TeethImgPrint:function(id){
            $.ajax({
                type: 'post',
                url: getDoctorRecordData,
                data: {
                    'record_id': record_id,
                },
                dataType: 'json',
                success: function (json) {
                    json = json['data'];
                    // 1 => '口腔检查',
                    // 2 => '辅助检查',
                    // 3 => '诊断',
                    // 4 => '治疗方案',
                    // 5 => '治疗',
                    var userInfo = json.userInfo;
                    var dentalBaseInfo = json.dentalBaseInfo;
                    var dentalRelation = _self.filterDentalRelation(json.dentalRelation);
                    var spotInfo = json.spotInfo;
                    var allergyInfo = json.allergyInfo;
                    var spotConfig = json.spotConfig;
                    var logo_img = '';
                    if(spotConfig.logo_shape == 1){
                        logo_img = "clinic-img"
                    }else{
                        logo_img = "clinic-img-long"
                    }
                    var printTeethModel = template.compile(teethImgTpl)({
                        userInfo: userInfo,
                        dentalRelation: dentalRelation,
                        dentalBaseInfo: dentalBaseInfo,
                        spotInfo:spotInfo,
                        recordId: record_id,
                        allergyInfo: allergyInfo,
                        cdnHost: cdnHost,
                        baseUrl: baseUrl,
                        spotConfig : spotConfig,
                        logo_img : logo_img,
                        teethImgPath : 'normalTeeth',//默认图片存储目录
                    });
                    $('#teeth-print').html(printTeethModel);
                    _self.renderTeethData();
                    $('#' + id).jqprint();
                },
                error: function () {
                },
            });
        },
        //得到数据 对牙位进行样式渲染
        renderTeethData:function(){
            /** 病症数据类型
             1 - 龋齿/缺损
             2 - 根尖/牙髓
             3 - 缺失
             4 - 其他
             ---------------*/

            $('.teeth-img-box-number').each(function(){
                var findData = $(this).text();
                var dataTeethPosition = $(this).parents('.teeth-img-col').attr('data');//得到牙位数据
                var dataDisease = $(this).parents('.teeth-img-single').attr('data-teeth');//得到病症数据
                var dataPosition = $(this).parents('.teeth-img-col').attr('data-position');//获取是上部分还是下部分
                var imgDirArr = ['normalTeeth','cariesTeeth','pulpTeeth','missTeeth','otherTeeth'];
                var positionArr = ['Top','Bottom'];
                if(dataTeethPosition){
                    if(dataTeethPosition.indexOf(findData) >= 0){//找到数据 则进行渲染
                        $(this).parents('.teeth-img-box').addClass('teeth-img-box-active');
                        $(this).siblings('.teeth-img-box-show').children('img')
                          .attr('src',baseUrl+'/public/img/outpatient/'+imgDirArr[dataDisease]+'/teeth'+positionArr[dataPosition]+'.png');
                    }
                }

            });
        },
        /**
         * [处理dentalRelation数据,过滤未选择的数据及对恒牙与乳牙数据进行分离]
         * @param  {[object]} dentalRelationData [牙位图数据]
         * @return {[object]}                [返回处理后的数据]
         */
        filterDentalRelation:function(dentalRelationData){
            // 过滤未选择的数据
            _self.findSelect(dentalRelationData);
            // 要排除的属性（既不是牙齿位置的属性）
            var dataExcludeArr = ['content','dental_disease']; //排除备注和病症
            /**
             * [分离既含有数字又有字符串的字符串]
             * @param  {[string]} v [要分离的字符串]
             * @return {[array]}      [返回分离出的数组 0-分离的数字，1-分离的字符串]
             */
            function separateChrAndNum(v){
                if(!v){
                    return [null,null];
                }

                var arrSeparate = [];
                var chr = v.match(/[a-z|A-Z]+/gi);
                var num = v.match(/\d+/gi);
                num = arrToChr(num);
                chr = arrToChr(chr);
                arrSeparate.push(num);
                arrSeparate.push(chr);
                return arrSeparate;
            }
            /**
             * [数组转字符串]
             * @param  {[array]} arr [传入的数组]
             * @return {[string]}    [返回转换的字符串]
             */
            function arrToChr(arr){
                var str = null;
                if(arr instanceof Array){
                    if(arr.length>1){
                        str = arr.join("");
                    }else{
                        str = arr[0];
                    }
                }
                return str;
            }
            //数据处理 对恒牙和乳牙数据进行分离
            for(val in dentalRelationData){
                var arrPosition = dentalRelationData[val];
                for (var i = 0; i < arrPosition.length; i++) {
                    var objPermanentPosition = {};//恒牙位置信息
                    var objBabyPosition = {};//乳牙位置信息
                    var arr = [];
                    for(posiVal in arrPosition[i]){
                        var position = arrPosition[i][posiVal];
                        if(_self.findFromArr(posiVal,dataExcludeArr)){ 
                            objPermanentPosition[posiVal] = position;
                            objBabyPosition[posiVal] = position;
                        }else{
                           objPermanentPosition[posiVal] = separateChrAndNum(position)[0];
                           objBabyPosition[posiVal] = separateChrAndNum(position)[1];
                        }
                    }
                    arr.push(objPermanentPosition);
                    arr.push(objBabyPosition);
                    arrPosition[i] = arr;
                }
            }
            return dentalRelationData;
        },
        /**
         * [处理数据对dentalRelation进行过滤，找到选择了的类型传入到牙位图打印界面]
         * @param  {[object]} dentalRelation [传入标记了的牙位图数据]
         * @return {[object]}                [返回过滤了的牙位图数据]
         */
        findSelect:function(dentalRelation){
            //  类型映射关系
            var typeMap = {
                '口腔检查':'1',
                '辅助检查':'2',
                '诊断':'3',
                '治疗方案':'4',
                '治疗':'5'
            };
            var selectedArr = [];// 存储多选框选中数据的数组
            // 判断选中 将选中的数据压入数组中
            $('.teeth-check').each(function(){
                if($(this).attr('checked')){
                    selectedArr.push(typeMap[$(this).next('.teeth-check-text').text()]);
                }
            });
            
            // 删除detalRelation中未选择的数据
            for(val in dentalRelation){
                if(!_self.findFromArr(val,selectedArr)){
                    delete dentalRelation[val];
                }
            }
            return dentalRelation;
        },
        /**
         * [将数据与数组的数据进行对比]
         * @param  {[any?]} val [要找的值]
         * @param  {[array]} arr [存储数据的数组]
         * @return {[boolean]}     [返回true(找到) or false(未找到)]
         */
        findFromArr:function(val,arr){
            for (var i = 0; i < arr.length; i++) {
                if(val === arr[i]){
                    return true;
                }
            }
            return false;
        },
        /**
         * [判断牙位图是否选择,未选择则设置btn禁用]
         */
        setTeethSelectDisable:function(){

            $('body').on('click','.teeth-check',function(){
                var flag=0;
                $('.teeth-check').each(function(){
                    if($(this).attr('checked')){
                        $('.btn-teeth-confirm').removeAttr('disabled');
                    }else{
                        flag++;
                    }
                });
                if(flag === $('.teeth-check').length){
                    $('.btn-teeth-confirm').attr({disabled:'disabled'});
                }
            });
        },
        //牙位图同步
        teethPositionSysc:function(obj){ //手动输入和牙位图点击
            //获取牙位图的病症
            var dentalDisease = obj.parents('.dental-check').find('.dental-check-content').find('.has-dental-disease');
            var dentalDiseaseArr = [];
            dentalDisease.each(function(){
                var val = $(this).find(".dental-disease-select option:selected").val();
                dentalDiseaseArr.push(val);
            });
            console.log(dentalDiseaseArr);
            //获取当前编辑的牙位图位置
            var positionArr = obj.parents('.dental-check').find('.tooth-position');
            //获取当前编辑的牙位图数据
            var positionSelected = [];
            var arr = [];
            positionArr.each(function(){
                var leftTopValue = $(this).find('.left-top-input').val();
                var rightTopValue = $(this).find('.right-top-input').val();
                var leftBottomValue = $(this).find('.left-bottom-input').val();
                var rightBottomValue = $(this).find('.right-bottom-input').val();
                arr = [leftTopValue, rightTopValue, leftBottomValue,rightBottomValue ];
                positionSelected.push(arr);
            });

            //获取其他检查的牙位图
            var otherType = obj.parents('.dental-check').siblings('.dental-check');
            var otherContent = [];

            otherType.each(function(index,element){
                console.log(index,'index');
                //获取其他检查的牙位图的填写内容
                var contentType = [];
                $(this).find("textarea[name='DentalHistoryRelation[content][]']").each(function(){
                    contentType.push($(this).val());
                });
                otherContent[index] = contentType;

                //增加与当前检查一样数量的牙位图
                for(var i =0;i<positionSelected.length;i++){
                    var type = $(this).find("input[name='DentalHistoryRelation[type][]']").eq(0).val();
                    _self.addDentalCheck(type, $(this).find('.add-booth-button'),1);
                }
                //删除旧的牙位图
                var checkContent = $(this).find('.dental-check-content');
                checkContent.each(function(contentIndex,element){
                    if($(this).attr('class') == "dental-check-content"){
                        $(this).remove('.dental-check-content');
                    }
                    //第一个为始终是添加牙位按钮
                    $(this).parent('.dental-check-content-list').find('.add-booth').eq(0).replaceWith('<div class="add-booth"><botton type="button" class="add-booth-button" data-type="'+type+'" style="display: inline-block;">添加牙位</botton></div>');
                    $(this).removeClass('special');

                    console.log(contentIndex,'contentIndex');


                    //同步牙位图数据
                    var positionFixArr = $(this).parent('.dental-check-content-list').find('.tooth-position');
                    //同步输入态数据到弹窗态
                    positionFixArr.each(function(index,element){ //四个框
                        var leftTopValue = positionSelected[index][0];
                        leftTopValue = _self.sortData(leftTopValue, 1);//输入排序
                        $(this).find('.left-top-text').html(leftTopValue);//显示
                        $(this).find('.left-top-input').attr('value', leftTopValue);//值


                        var rightTopValue = positionSelected[index][1];
                        rightTopValue = _self.sortData(rightTopValue, 2);
                        $(this).find('.right-top-text').html(rightTopValue);
                        $(this).find('.right-top-input').attr('value', rightTopValue);

                        var leftBottomValue = positionSelected[index][2];
                        leftBottomValue = _self.sortData(leftBottomValue, 1);
                        $(this).find('.left-bottom-text').html(leftBottomValue);
                        $(this).find('.left-bottom-input').attr('value', leftBottomValue);

                        var rightBottomValue = positionSelected[index][3];
                        rightBottomValue = _self.sortData(rightBottomValue, 2);
                        $(this).find('.right-bottom-text').html(rightBottomValue);
                        $(this).find('.right-bottom-input').attr('value', rightBottomValue);
                        if (leftTopValue || rightTopValue || leftBottomValue || rightBottomValue) {
                            var position = leftTopValue + ',' + rightTopValue + ',' + rightBottomValue + ',' + leftBottomValue;
                            $(this).parent('.dental-check-content').find('.dental-history-relation-position').val(position);//拼接四个输入框的值
                        }
                        //治疗内容同步
                        $(this).next('.dentail-content').val(contentType[index]);
                    });
                });

            });
            //获取其他检查的牙位图,同步牙位病症
            var otherDisease = obj.parents('.dental-check').siblings('.dental-check');
            otherDisease.each(function(){
                var diseaseContent = $(this).find('.dental-check-content');
                diseaseContent.each(function(index){
                    if(dentalDiseaseArr[index] == 0){
                        $(this).find('.has-dental-disease').hide();
                        $(this).find('.dental-disease-show').hide();
                    }else{
                        $(this).find('.has-dental-disease').find(".dental-disease-select option[value = '"+dentalDiseaseArr[index]+"']").attr("selected","selected");
                        $(this).find('.has-dental-disease').hide();
                        var diseaseDescription = '';
                        if(typeof(dentalDiseaseType[dentalDiseaseArr[index]]) !="undefined"){
                            diseaseDescription = dentalDiseaseType[dentalDiseaseArr[index]];
                        }
                        $(this).find('.dental-disease-show').html('病症：'+ diseaseDescription );
                        $(this).find('.dental-disease-show').show();
                    }

                });

            });

        },
        // 正畸初诊病历打印
        orthodonticsFirstRecordPrint:function(id){

            $.ajax({
                type: 'post',
                url: getDoctorRecordData,
                data: {
                    'record_id': record_id,
                },
                dataType: 'json',
                success: function (json) {
                    json = json['data'];
                    var userInfo = json.userInfo;
                    var spotInfo = json.spotInfo; //诊所信息
                    var allergyInfo = json.allergy; //过敏信息
                    var spotConfig = json.spotConfig;
                    var baseInfo = json.baseInfo;
                    var firstCheck = json.firstCheck; //初步诊断
                    var logo_img = '';

                    //口腔病史牙位数据
                    baseInfo.recordRetention = _self.teethDataFilter(baseInfo.recordRetention);//滞留
                    baseInfo.early_loss = _self.teethDataFilter(baseInfo.early_loss);//早失

                    //牙齿检查牙位数据
                    baseInfo.dental_caries = _self.teethDataFilter(baseInfo.dental_caries);//龋齿
                    baseInfo.reverse = _self.teethDataFilter(baseInfo.reverse);//扭转
                    baseInfo.impacted = _self.teethDataFilter(baseInfo.impacted);//阻生
                    baseInfo.ectopic = _self.teethDataFilter(baseInfo.ectopic);//异位
                    baseInfo.defect = _self.teethDataFilter(baseInfo.defect);//缺失
                    baseInfo.retention = _self.teethDataFilter(baseInfo.retention);//滞留
                    baseInfo.repair_body = _self.teethDataFilter(baseInfo.repair_body);//修复体
                    baseInfo.other = _self.teethDataFilter(baseInfo.other);//其他

                    if (spotConfig.logo_shape == 1) {
                        logo_img = "clinic-img"
                    } else {
                        logo_img = "clinic-img-long"
                    }

                    var printModel = template.compile(orthodonticsFirstRecordTpl)({
                        userInfo: userInfo,
                        baseInfo: baseInfo,
                        spotInfo: spotInfo,
                        recordId: record_id,
                        firstCheck: firstCheck,
                        allergyInfo: allergyInfo,
                        cdnHost: cdnHost,
                        baseUrl: baseUrl,
                        spotConfig: spotConfig,
                        logo_img: logo_img,
                    });
                    $('#teeth-print').html(printModel);
                    $('#' + id).jqprint();
                },
                error: function () {
                },
            });
        },
        // 牙位数据处理
        teethDataFilter:function(dataStr){
            var arr = [];
            if(dataStr.length == 0){
                return ['','','',''];
            }else{
                arr = dataStr.split(',');
                return arr;
            }
        },

        setTeethStatus:function(obj){
            //更换牙位同步状态
            var otherDentalCheckTitle = obj.parents('.dental-check').siblings('.dental-check').find('.dental-check-title');
            var otherTitleValue = [];
            otherDentalCheckTitle.each(function () {
                otherTitleValue.push($(this).attr('data-value'));
            });
            var index = otherTitleValue.indexOf('1');
            if(index == -1){
                $('.tooth-sysc').attr({'disabled': false});
            }
        },

};
    return main;
});
