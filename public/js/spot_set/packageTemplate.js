

define(function (require) {
    var template = require('template');
    var cureTpl = require('tpl/packageTemplate/cure.tpl');
    var checkTpl = require('tpl/outpatient/check.tpl');
    var recipeTpl = require('tpl/packageTemplate/recipe.tpl');
    var _self;
    var main = {
        init: function () {
            _self = this;
            this.bindEvent();
            
        },
        bindEvent: function () {
        	
        	$('.empty').parents('tr').remove();
        	var isCommitted = false;//表单是否已经提交标识，默认为false
            $('#package-template').yiiAjaxForm({
        	   beforeSend: function() {
             
                    if(isCommitted == false){
                    	isCommitted = true;//提交表单后，将表单是否已经提交标识设置为true
                       return true;//返回true让表单正常提交
                    }else{
                       return false;//返回false那么表单将不提交
                    }		   
        	   },
        	   complete: function() {
        			
        	   },
        	   success: function(data) {
                    
            	   if(data.errorCode == 0){
                        if(isCommitted == true){
                        	window.location.href = indexUrl;
                        }
            	   }else{
            		   isCommitted = false;
            		    showInfo(data.msg,'180px',2);
                    }
        	   },
            });
            _self.cureEdit();
            _self.inspectEdit();
            _self.checkEdit();
            _self.recipeEdit();//处方检查
        },
        addCure: function (id, name, unit, time, description) {
            var cureModel = template.compile(cureTpl)({
                id: id,
                name: name,
                unit: unit,
                time: time,
                description: description,
                baseUrl: baseUrl
            });
            $('.cure-form tbody').append(cureModel);
        },
        cureEdit : function(){
        	$('#outpatientpackagecure-curename').select2({
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
                  if(typeof(searchData) != 'undefined' && typeof(data.name) != 'undefined'){
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
        	$('body').on('change', '#outpatientpackagecure-curename', function (e) {
                var data = $(this).select2("data")[0];
                _self.addCure(data.id, data.name, data.unit);
                $('#outpatientpackagecure-curename').val('');
            });
            $('body').on('click', '.cure-form .op-group>img', function () {
                $(this).parents('tr').remove();
            })
        },
        inspectEdit: function () {
        	$('#outpatientpackageinspect-inspectname').select2({
        		language: "zh-CN",
                selectOnBlur : true,
                placeholder : '请输入实验室检查进行搜索',
        		minimumInputLength : 1,
        		minimumResultsForSearch : 1,
        		allowClear : false,
        		ajax: { // instead of writing the function to execute the request we use Select2's convenient helper
        	        url: inspectSearchUrl,
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
                  if(typeof(searchData) != 'undefined' && typeof(data.name) != 'undefined'){
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
            $('body').on('click', '#package-inspect .op-group>img', function () {
                $(this).parents('.inspect-list').remove();
            });

            $('body').on('change', '#outpatientpackageinspect-inspectname', function (e) {
            	var data = $(this).select2("data")[0];
                _self.addCheck(data,data.name, 'OutpatientPackageInspect[inspect_id][]', 'OutpatientPackageInspect[deleted][]', 'inspect-list', '.inspect-content', 1);
                $(this).val('');
            });
        },
        checkEdit: function () {
        	$('#outpatientpackagecheck-checkname').select2({
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
                  if(typeof(searchData) != 'undefined' && typeof(data.name) != 'undefined'){
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
            $('body').on('click', '#package-check .op-group>img', function () {
                $(this).parents('.check-list').remove();
            });

            $('body').on('change', '#outpatientpackagecheck-checkname', function (e) {
            	var data = $(this).select2("data")[0];
                _self.addCheck(data,data.name, 'OutpatientPackageCheck[check_id][]', 'OutpatientPackageCheck[deleted][]', 'check-list', '.check-content', 2);
                $(this).val('');
            });
        },
        addCheck: function (list, name, inputName, deleted, parentClass, appendClass, type) {
            var itmTitle = '';
            if (type == 1) {//实验室检查
                if (list['inspectItem']) {
                    for (var itm in list['inspectItem']) {
                        itmTitle += '<p>' + htmlEncodeByRegExp(list['inspectItem'][itm]['item_name']);
                        itmTitle += list['inspectItem'][itm]['english_name'] ? '(' + htmlEncodeByRegExp(list['inspectItem'][itm]['english_name']) + ')</p>' : '<p>';
                    }
                }
            }
            console.log(itmTitle);
            var checkModel = template.compile(checkTpl)({
                list: list['id'],
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
        
        recipeEdit: function () {
        	$('#outpatientpackagerecipe-recipename').select2({
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
                            meta: params.term,
                            specification: params.term,
                            manufactor: params.term,
                            product_name: params.term,
                            
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
                      name += htmlEncodeByRegExp(data.manufactor)+',';
                      name += data.price+'元';
                      name += ')';
                      return name;
        		  }
        		  return data.text;
        		  
        	  },
        	  escapeMarkup: function (m) { return m; } // we do not want to escape markup since we are displaying html in results
        	    
        	});
            $('body').on('click', '.recipe-delete>img', function () {
                $(this).parents('tr').prev('tr').remove();
                $(this).parents('tr').next('tr.skinTestTr').remove();
                $(this).parents('tr').remove();
            })
            $('body').on('change', '#outpatientpackagerecipe-recipename', function (e) {
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
                recipeList: data.id,
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