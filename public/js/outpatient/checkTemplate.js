
define(function (require) {
    var template = require('template');
    var common = require('js/lib/common');
    var checkTpl = require('tpl/outpatient/check.tpl');
    var _self;
    var main = {
        init: function () {
            _self = this;
            this.bindEvent();
        },
        bindEvent: function () {
            _self.saveForm();
            _self.checkEdit();//检查医嘱
            _self.initSelect2();
        },
        saveForm: function () {
            $('#checkTemplate').yiiAjaxForm({
                beforeSend: function () {
                },
                complete: function () {

                },
                success: function (data) {

                    if (data.errorCode == 0) {
                        window.location.href = indexUrl;
                    } else {
                        showInfo(data.msg, '180px', 2);
                    }

                },
            });
        },
        checkEdit: function () {
            $('body').on('click', '#checkTemplate .op-group>img', function () {
                $(this).parents('.check-list').hide();
                $(this).parent('.op-group').siblings('.check-id').children('input[name="CheckTemplateInfo[deleted][]"]').val(1);
            })

            $('body').on('change', '#checktemplateinfo-checkname', function (e) {
            	var data = $(this).select2("data")[0];
                _self.addCheck(data,data.name, 'CheckTemplateInfo[clinic_check_id][]', 'CheckTemplateInfo[deleted][]', 'check-list', '.check-content', 2);
                $(this).val('');
            });
        },
        addCheck: function (list, name, inputName, deleted, parentClass, appendClass, type) {
            var itmTitle = '';
            var checkModel = template.compile(checkTpl)({
                list: htmlEncodeByRegExp(JSON.stringify(list)),
                name: name,
                deleted: deleted,
                inputName: inputName,
                parentClass: parentClass,
                baseUrl: baseUrl,
                type: type,
                itmTitle: itmTitle
            });
            $(appendClass).append(checkModel);
        },
        initSelect2: function () {
            var searchData = null;
                $('#checktemplateinfo-checkname').select2({
                	language: "zh-CN",
                    selectOnBlur : true,
                    placeholder : '请输入影像学检查进行搜索',
            		minimumInputLength : 1,
            		minimumResultsForSearch : 1,
            		allowClear : false,
            		ajax: { // instead of writing the function to execute the request we use Select2's convenient helper
            	        url: checkSearchUrl,
            	        dataType: 'json',
            	        quietMillis: 2000,
            	        type : 'post',
            	        delay: 2000,
            	        beforeSend: function (xhr) {
            	        	var searchData = $('.select2-search__field').val();
            	        	if($.trim(searchData) == ""){
            	        		return false;
            	        	}
            	        	return true;
            	    　　	},
            	        data: function (params) {
                            return {
                                name: params.term, // search term
                            };
                        },
                        processResults : function (data, page) { // parse the results into the format expected by Select2.
            	            // since we are using custom formatting functions we do not need to alter the remote JSON data
            	        	return { results: data.data};
            	        },
            	        cache: true
            	    },
            	  templateResult : function(data){
            		  searchData = $('.select2-search__field').val();
                      if(typeof(searchData) != 'undefined'  && typeof(data.name) != 'undefined'){
                          searchData = searchData.toString();
                          return highLightKeywords(data.name,$.trim(searchData));
                      }
                      return htmlEncodeByRegExp(data.text);
            	  },
            	  templateSelection : function(data){
            		  return htmlEncodeByRegExp(data.name)||data.text;
            	  },
            	  escapeMarkup: function (m) { return m; } // we do not want to escape markup since we are displaying html in results

                });
        },
    };
    return main;
})
