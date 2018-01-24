
define(function (require) {
    var template = require('template');
    var recipeTpl = require('tpl/outpatient/recipeTemplate.tpl');

    var _self;
    var main = {
        init: function () {
            _self = this;
            this.bindEvent();
        },
        bindEvent: function () {
        	$('#recipetemplateinfo-recipename').select2({
        		
        		language: "zh-CN",
                selectOnBlur : true,
                placeholder : '请输入处方进行搜索',
        		minimumInputLength : 1,
        		minimumResultsForSearch : 1,
        		allowClear : false,
        		ajax: { // instead of writing the function to execute the request we use Select2's convenient helper
        	        url: recipeSearchUrl,
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
                            product_name : params.term,
                            specification : params.term,
                            manufactor : params.term,
                            meta : params.term
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
                  if(typeof(searchData) != 'undefined' && typeof(data.name) != 'undefined'){
                     searchData = searchData.toString();
                      
                     var name = highLightKeywords(data.name,$.trim(searchData));
                     var meta = '';
                     if(data.meta){
                    	 name += '-'+highLightKeywords(data.meta,$.trim(searchData));
                     }
                     name += '(';
                     if(data.product_name){
                         name += highLightKeywords(data.product_name,$.trim(searchData))+',';
                     }
                     if(data.specification){
                         name += highLightKeywords(data.specification,$.trim(searchData))+',';
                     }
                     if(data.manufactor){
                         name += highLightKeywords(data.manufactor,$.trim(searchData))+',';
                     }
                     name += data.price+'元';
                     name += ')';
                     return name;
                     
                  }
                  return htmlEncodeByRegExp(data.text);
        	  },
        	  templateSelection : function(data){
        		  if(typeof(data.name) != 'undefined'){
        			  var name = htmlEncodeByRegExp(data.name);
        			  
                      var meta = '';
                      if(data.meta){
                     	 name += '-'+htmlEncodeByRegExp(data.meta);
                      }
                      name += '(';
                      if(data.product_name){
                          name += htmlEncodeByRegExp(data.product_name)+',';
                      }
                      name += htmlEncodeByRegExp(data.specification)+',';
                      if(data.manufactor){
                          name += htmlEncodeByRegExp(data.manufactor)+',';
                      }
                      name += data.price+'元';
                      name += ')';
                      return name;
        		  }
        		  return data.text;
        		  
        	  },
        	  escapeMarkup: function (m) { return m; } // we do not want to escape markup since we are displaying html in results
        	    
        		
        	});
        	_self.saveForm();
            _self.recipeEdit();//处方检查
        },
        saveForm: function () {
            $('#recipeTemplate').yiiAjaxForm({
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
        recipeEdit: function () {
            $('body').on('click', '.recipe-delete>img', function () {
                $(this).parents('tr').prev('tr').hide();
                $(this).parents('tr').hide();
                $(this).parents('tr').next('tr.skinTestTr').hide();
                $(this).siblings('input[name="RecipeTemplateInfo[deleted][]"]').val(1);
            })
            $('body').on('change', '#recipetemplateinfo-recipename', function (e) {
            	var data = $(this).select2("data")[0];
                _self.addRecipe(data);
                $(this).val('');
                $('.status-recipe-column').each(function () {
                    $(this).css("display", "table-cell");
                });
            });
          //判断皮试的选择
            $(document).on('change', '.skinTestStatus', function () {
                var id = $(this).val();
                if (id == 1) {
                    $(this).parent().parent().find('.skin-test-status').show();
                } else {
                    $(this).parent().parent().find('.skin-test-status').hide();
                }
            });

        },
        addRecipe: function (data) {

            var showValue = '';
            if (data.manufactor != '') {
                showValue += '生产商：' + htmlEncodeByRegExp(htmlEncodeByRegExp(data.manufactor)) + '<br/>';
            }
            showValue += '零售价：' + data.price + '元';
            var recipeModel = template.compile(recipeTpl)({
                id: data.id,
                unit: unit,
                list: data,
                showValue: showValue,
                recipeList: JSON.stringify(data),
                defaultUsed: defaultUsed,
                dosage_form: dosage_form,
                defaultFrequency: defaultFrequency,
                skinTestList: skinTestList,
                defaultAddress: defaultAddress,
                baseUrl: baseUrl,
                itemUrl: itemUrl,
                skinTestStatusList: skinTestStatusList//皮试的状态列表
            });
            $('.recipe-form tbody').append(recipeModel);
        },
    };
    return main;
})
