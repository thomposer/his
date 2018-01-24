define(function (require) {
    var template = require('template');
    var prinkTpl = require('tpl/prink.tpl');
    var teethTpl = require('tpl/teeth.tpl');
    var childprinkTpl = require('tpl/childPrint.tpl');
    var doctorPrint=require('js/nurse/doctorPrint');
    var orthodonticsReturnvisitTpl = require('tpl/outpatient/orthodonticsReturnvisit.tpl');
    var orthodonticsFirstRecordTpl = require('tpl/outpatient/orthodonticsFirstRecord.tpl');
    var _self;
    var main = {
        init: function () {
            _self = this;
            this.checkBoxState();
            this.bindEvent();
            this.saveInfo();
            this.similarPatient();
            doctorPrint.init();
        },
        bindEvent: function () {
            //判断checkboxlist是否显示确定按钮
            $('.doc-selected-form').find('input').unbind('click').click(function () {
                main.checkBoxState();
            });

            //点击查看我的医生，显示checkboxlist页面以及样式
            $('.focus-doc').bind('click').click(function () {
                if($('.focus-doc').attr('state') == 1) {
                    $('.choice-doc-form').addClass('choice-doc-form-after');
                    $('.cover').fadeIn(500);
                    $('.choice-btn').fadeIn(500);
                    $('.focus-doc').attr('state', 2);
                    $('.doc-info-form-display').hide();
                    $('.doc-selected-form').removeClass('hidden');
                    $(this).find('i').attr('class','fa fa-angle-up');
//            $('.doc-info .doc-info-form').find('input').html();
                } else {
                    $('.choice-doc-form').removeClass('choice-doc-form-after');
                    $('.cover').fadeOut(500);
                    $('.choice-btn').fadeOut(500);
                    $('.focus-doc').attr('state', 1);
                    $('.doc-info-form-display').fadeIn(500);
                    $('.doc-selected-form').addClass('hidden');
                    $(this).find('i').attr('class','fa fa-angle-down');
//            $('.doc-info .doc-info-form').find('input').html();
                }
            });

            //医生列表展示页点击跳转
            $('.doc-info-form-display').find('label').unbind('click').click(function () {
                var doctorId = $(this).attr('doctor-id');
                var doctorDefalutId = doctorId;
                for(var i=0;i<doctorSelectId.length;i++){
                    if(doctorId == doctorSelectId[i]){
                        doctorSelectId.splice($.inArray(doctorId,doctorSelectId),1);
                        doctorId ='';
                    }
                }
                var doctor = '';
                if(doctorId){
                     doctor = doctorId + ','+ doctorSelectId;
                }else{
                     doctor =  doctorSelectId;
                }
                if(doctor ==''){
                    doctor = doctorDefalutId;
                }
                var date = $('#nursesearch-nursedate').val();
                window.location.href = nurseIndexIndex +'?doctor_id='+doctor + '&date='+date;
            });

            //日期选择器监听input值变化
            $('#nursesearch-nursedate').change('input propertychange', function() {
                var date = $(this).val();
                $(this).val(date+' '+_self.getWeekByDate(date));
                var doctorId = $(this).attr('doctor-id');
                window.location.href = nurseIndexIndex +'?date='+date+main.getQueryString('doctor_id');
            });
            
            $('.record-print').unbind('click').click(function () {
                var record_id = $(this).attr('record-id');
                $.ajax({
                    type: 'post',
                    url: getDoctorRecordData,
                    data: {
                        'record_id': record_id
                    },
                    dataType: 'json',
                    success: function (json) {
                        if(json['errorCode'] == 0){
                            json = json['data'];
                            var userInfo = json['userInfo'];
                            var spotInfo = json['spotInfo'];
                            var spotConfig = json['spotConfig'];
							 var logo_img = '';
                            if(spotConfig.logo_shape == 1){
                                logo_img = "clinic-img"
                            }else{
                                logo_img = "clinic-img-long"
                            }
                            if (json['recordType'] == 4 || json['recordType'] == 5) {

                                var dentalBaseInfo = json['dentalBaseInfo'];
                                var dentalRelation = json['dentalRelation'];

                                var allergyInfo = json['allergyInfo'];

                                var printTeethModel = template.compile(teethTpl)({
                                    userInfo: userInfo,
                                    dentalRelation: dentalRelation,
                                    dentalBaseInfo: dentalBaseInfo,
                                    spotInfo: spotInfo,
                                    recordId: record_id,
                                    allergyInfo: allergyInfo,
                                    cdnHost: cdnHost,
                                    baseUrl: baseUrl,
                                    spotConfig : spotConfig,
                                    logo_img : logo_img,
                                });
                                $('#record-print-view').html(printTeethModel);
                                $('#teethPrint' + record_id).jqprint();
                            } else if(json['recordType'] == 6){//正畸初诊打印
                                var allergyInfo = json.allergy; //过敏信息
                                var baseInfo = json.baseInfo;
                                var firstCheck = json.firstCheck; //初步诊断

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

                                var printOrthodonticsFirstModel = template.compile(orthodonticsFirstRecordTpl)({
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
                                $('#record-print-view').html(printOrthodonticsFirstModel);
                                $('#teethPrint' + record_id ).jqprint();
                            } else if(json['recordType'] == 7) {
                                var baseInfo = json['baseInfo'];
                                var firstCheck = json['firstCheck'];
                                var allergy = json['allergy'];
                                var prinkRecordInfoModel = template.compile(orthodonticsReturnvisitTpl)({
                                    spotInfo: spotInfo,
                                    spotConfig: spotConfig,
                                    logo_img: logo_img,
                                    record_id: record_id,
                                    userInfo: userInfo,
                                    firstCheck: firstCheck,
                                    baseInfo: baseInfo,
                                    allergy: allergy,
                                    cdnHost: cdnHost,
                                    baseUrl: baseUrl,
                                });
                                $('#record-print-view').html(prinkRecordInfoModel);
                                $('#orthodontics-print' + record_id + 'myshow').jqprint();
                            } else {
                                var repiceInfo = json['repiceInfo'];
                                var recipeRecordDataProvider = json['recipeRecordDataProvider'];
                                var outpatientInfo = json['outpatientInfo'];
                                var firstCheck = json['firstCheck'];
                                var allergy = json['allergy'];
                                var logo_img = '';
                                if(spotConfig.logo_shape == 1){
                                    logo_img = "clinic-img"
                                }else{
                                    logo_img = "clinic-img-long"
                                }
                                var prinkRecordInfoModel = template.compile(prinkTpl)({
                                    spotInfo: spotInfo,
                                    triageInfo: userInfo,
                                    repiceInfo: repiceInfo,
                                    record_id: record_id,
                                    outpatientInfo: outpatientInfo,
                                    recipeRecordDataProvider: recipeRecordDataProvider,
                                    firstCheck: firstCheck,
                                    allergy: allergy,
                                    cdnHost: cdnHost,
                                    baseUrl: baseUrl,
                                    spotConfig : spotConfig,
                                    logo_img : logo_img,
                                });
                                $('#record-print-view').html(prinkRecordInfoModel);
                                $('#record' + record_id + 'myshow').jqprint();
                            }
                        }

                    },
                    error: function () {
                    },
                });
            });

            $('body').off('click').on('click', '.btn-nurse-list-print', function (e) {
                e.preventDefault();
                var record_id = $(this).attr('record-id');
                $.ajax({
                    type: 'post',
                    url: getChildRecordData,
                    data: {
                        'record_id': record_id,
                    },
                    success: function (json) {
                        if (json['errorCode'] == 0) {
                            json = json['data'];
                            var spotInfo = json['spotInfo'];
                            var spotConfig = json['spotConfig'];
                            var logo_img = '';
                            if (spotConfig.logo_shape == 1) {
                                logo_img = "clinic-img"
                            } else {
                                logo_img = "clinic-img-long"
                            }
                            var triageInfo = json['userInfo'];
                            var childExaminationInfo = json['childExaminationInfo'];
                            var getSummary = json['childBasicConfig']['summary'];
                            var getCommunicate = json['childBasicConfig']['communicate'];
                            var getType = json['childBasicConfig']['type'];
                            var allergy = json['allergy'];
                            var childPrintInfo = template.compile(childprinkTpl)({
                                triageInfo: triageInfo,
                                getSummary: getSummary, //总结
                                getCommunicate: getCommunicate, //正常，随访，转诊
                                getType: getType, //正常，随访，转诊
                                record_id: record_id,
                                spotInfo: spotInfo,
                                childExaminationInfo: childExaminationInfo,
                                allergy: allergy,
                                cdnHost: cdnHost,
                                baseUrl: baseUrl,
                                spotConfig: spotConfig,
                                logo_img: logo_img,
                            });
                            $('#child-record-print-view').find('#child-record-print-view-child').html(childPrintInfo);
                            $('#child-record-print-view-child').jqprint();
                        }
                    },
                    error: function () {

                    },
                    dataType: 'json'

                });
            });
        },
        checkBoxState: function () {
            var length = $('.doc-selected-form').find('input:checked')['length'];
            if(length > 0){
                $('.choice-btn .submit').attr('disabled',false);
                $('.choice-btn .submit').attr('title','');
            }else {
                $('.choice-btn .submit').attr('disabled','disabled');
                $('.choice-btn .submit').attr('title','请选择您关注的医生');

            }
        },
        getQueryString: function (name) {
            var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)", "i");
            var r = window.location.search.substr(1).match(reg);
            if (r != null) return '&doctor_id='+unescape(r[2]);
            return '';
        },
        saveInfo: function () {
            $('body').off('click', '.report-confirm').on('click', '.report-confirm', function (e) {
                var actionUrl = $(this).attr('actionurl');
                $.ajax({
                    type: 'post',
                    url: actionUrl ,
                    data: {
                        'entrance' : 2
                    },
                    success: function (data) {
                        if (data.similarUser) {
                            var data = {postParam: data.postParam, similarUser: data.similarUser, actionUrl: actionUrl};
                            modal.open('.report-confirm', data);
                            return false;
                        } else if (data.errCode == 1001) {
                            showInfo('操作失败', '100px');
                        } else {
                            showInfo('操作成功', '100px');
                            window.location.href = data.locationUrl;
                        }
                    },
                    error: function () {

                    },
                    dataType: 'json'

                });
            });
        },
        similarPatient: function () {
            $('body').on('click', '.patient-card', function () {
                $(this).addClass('active').siblings('.patient-card').removeClass('active');
                $('.patient-report').removeAttr('disabled');
            });
            $('body').on('click', '.patient-report', function () {
                var id = $('.patient-card.active').attr('patientId');
                var actionurl = $('.patient-card.active').attr('actionurl');
                if (!id) {
                    showInfo('请选择需要报到的用户', '200px', 2);
                } else {
                    var locationUrl = actionurl.indexOf('update') > 0 ? actionurl + '&patientId=' + id : actionurl + '?id=' + id;
                    location.href = locationUrl;
                }
            });
        },
        /**
         * [根据日期得到当前星期]
         * @param  {[string]} date [日期]
         * @return {[string]}      [返回星期几,出错返回-1]
         */
        getWeekByDate:function(date){
            try{
             var d = new Date(date);
            }catch(e){
              return -1;
            }
            var day = d.getDay();
            var week;
            switch(day)
            {
            case 0:
                week = '日';
                break;
            case 1:
                week = '一';
                break;
            case 2:
                week = '二';
                break;
            case 3:
                week = '三';
                break;
            case 4:
                week = '四';
                break;
            case 5:
                week = '五';
                break;
            case 6:
                week = '六';
                break;
            default:
                break;
            }
            if(typeof(week) == "undefined"){
                return -1;
            }else{
                return '周'+week;
            }
        },
        // 牙位数据处理
        teethDataFilter:function(dataStr){
            var arr = [];
            if(dataStr == null || dataStr.length == 0){
                return ['','','',''];
            }else{
                arr = dataStr.split(',');
                return arr;
            }
        },
};
    return main;
});