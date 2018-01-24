

define(function (require) {
    var template = require('template');
    var patientInfoTpl = require('tpl/patientInfo.tpl');
    var common = require('js/lib/common');
    var _self;
    var  main = {
        init: function () {
            _self = this;
            this.bindEvent();
        },
        bindEvent: function () {
            template.config(escape, false);
            var hash = window.location.hash?window.location.hash:'';

            $('ul.nav-tabs>li>a').on('click',function(){
                var href = $(this).attr('href');
                window.location.hash = href;
                hash = href;
            });
            if(hash != ''){
                $('ul.nav-tabs>li').removeClass('active');
                $('a[href="'+hash+'"]').parent('li').addClass('active');
                $(hash).siblings('.tab-pane').removeClass('active');
                $(hash).addClass('active');
            }
            
            _self.addPatientInfo();//患者个人信息卡片
            _self.check();
            _self.updateCheck();
            _self.print();
            if(status == 1){
//            	$('.file-drop-zone-title').hide();
            	$('.kv-file-remove').hide();
                $('.input-group-btn').find('.btn-browse-custom').addClass("btn-browse-custom-disabled");
            	$('.input-group-btn').find('input').attr("disabled","disabled");
		$('.input-group-btn').find('input').addClass('no-click');
            }
            $('body').on('click','.kv-file-remove',function(){
                var url = $(this).data('url');
                var key = $(this).data('key');
                var isNew = $(this).data('new');
                if(url && key && isNew == 1){
                    $.ajax({
                        cache: true,
                        type: "POST",
                        url: url,
                        data: {
                            key: key,
                       }, // 你的formid
                        dataType: 'json',
                        async: false,
                        success: function (data, textStatus, jqXHR) {
                            if(data.errorCode == 0){

                            }
                            
                        },
                        error: function () {
                           showInfo('系统异常,请稍后再试', '180px',2);
                        }
                });
                }
            })
        },
        addPatientInfo: function () {
            var triageInfoModel = template.compile(patientInfoTpl)({
                list: triageInfo,
                baseUrl: baseUrl,
                cdnHost : cdnHost,
                allergy: allergy
            });
            $('#outpatient-patient-info').html(triageInfoModel);
        },
        check: function () {  //修改治疗
            $('body').on('click', '.confirm-check', function () {

                var pid = $('.tab-pane.active').attr('id');

                var result = $('#result_'+pid).val();
                var description = $('#description_'+pid).val();
                var avatar = new Array();
                var fileNameList = new Array();
                var sizeList = new Array();
                $(".J-ipt-hidden").each(function(){
	　　　　　　　　　　var val = $(this).val();
					var name = $(this).data('name');
					var size = $(this).data('size');
					avatar.push(val);
					fileNameList.push(name);
					sizeList.push(size);
	　　　　　　　　});
                	
                dispensingUrl=$("form").attr("action");

                $.ajax({
                    cache: true,
                    type: "POST",
                    url: dispensingUrl,
                    data: {
                    	idArr: pid, 
                    	description: description,
                    	result:result,
                    	avatar:avatar,
                    	fileNameList:fileNameList,
                    	sizeList : sizeList
                   }, // 你的formid
                    dataType: 'json',
                    async: false,
                    success: function (data, textStatus, jqXHR) {
                        if(data.errorCode == 0){
                            window.location.href = document.referrer;//返回上一页并刷新
                        }else{
                            showInfo(data.msg,'180px',2);   
                        }
                    },
                    error: function () {
                       showInfo('系统异常,请稍后再试', '180px',2);
                    }
                });
            });
        },
        updateCheck: function () {
            $('body').on('click', '.update-check', function () {
                var checkForm = $(this).parent().parent();
                checkForm.find('.form-control').attr({'readonly':false});
                $(this).html('保存');
                $(this).removeClass('update-check').addClass('confirm-check');
            
                var checkForm = $(this).parent().parent();
            	checkForm.find('.input-group-btn').find('input').removeAttr('disabled');
                checkForm.find('.input-group-btn').find('.btn-browse-custom').removeClass("btn-browse-custom-disabled");
                checkForm.find('.input-group-btn').find('input').removeClass('no-click');
            	checkForm.find('.kv-file-remove').show();
            	checkForm.find('.file-drop-zone-title').show();
                checkForm.find('.print-check').hide();
            })
        },

        print: function () {
            $('body').on('click', '.print-check', function () {
                var id = $(this).attr('name');
                $('#'+id).jqprint();
            });
        }
    };
    return main;
})