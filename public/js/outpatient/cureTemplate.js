
define(function (require) {
    var template = require('template');
    var cureTpl = require('tpl/outpatient/cureTemplate.tpl');
    var _self;
    var main = {
        init: function () {
            _self = this;
            this.bindEvent();
        },
        bindEvent: function () {
            $('#curetemplateinfo-curename').select2({
            	
            	language: "zh-CN",
                selectOnBlur : true,
                placeholder : '请输入治疗进行搜索',
        		minimumInputLength : 1,
        		minimumResultsForSearch : 1,
        		allowClear : false,
        		ajax: { // instead of writing the function to execute the request we use Select2's convenient helper
        	        url: cureSearchUrl,
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
            _self.saveForm();
            _self.cureEdit();//检验医嘱
        },
        saveForm: function () {
            $('#cureTemplate').yiiAjaxForm({
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
        cureEdit: function () {
            $('body').on('click', '.cure-form .op-group>img', function () {
                $(this).parents('tr').remove();
            })
            $('body').on('change', '#curetemplateinfo-curename', function (e) {
            	var data = $(this).select2("data")[0];
                _self.addCure(data.id, data.name, data.unit);
                $('#curerecord-curename').val('');
            });

        },
        addCure: function (id, name, unit) {
            var cureModel = template.compile(cureTpl)({
                id: id,
                name: name,
                unit: unit,
                baseUrl: baseUrl
            });
            $('.cure-form tbody').append(cureModel);
        },
    };
    return main;
})
