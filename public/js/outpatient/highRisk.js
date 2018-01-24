
define(function (require) {
    var recipeTpl = require('tpl/outpatient/recipe.tpl');
    var template = require('template');
    var _self;
    var tree = [];
    var isInTemplateMenu = false;
    mainHighRisk = {
        init: function () {
            _self = this;
            this.initRecipeRecord();
            this.initRecipePage();
            this.recipeEdit();
            this.changeStock();
            recipeRecordUrl = $('a[href=\"#recipe\"]').attr('data-url');
        },
        initRecipeRecord:function(){
            if(recipeCount == 0){
                $('#recipe button').html('保存');
                $('#recipe .print-check').hide();

                $('.btn-recipe-check-application').html('');
                $('.field-reciperecord-recipename').show();
                $('#recipe-template-select-ui').show();
                setTimeout(
                    function () {
                    	mainHighRisk.bind();
                        $('#recipe button').attr({'type': 'submit'});
                    },
                500);
            };
            $('body').on('click','.recipe-template-desc',function(){
            	_self.formatRecipeTemplateMenu();
            	_self.initTemplateMenul();
            });
            $('.recipe-template-desc').click();
          //处方数量失去焦点后，校验
            $('body').off('blur', '.recipeNum').on('blur', '.recipeNum', function () {
                var totalNum = [];
                var error = 0;
                var nums = $(this).val();
                var recipeNumObj = [];
                var id = $(this).data('id');
                if (typeof (id) == "undefined") {
                    return;
                }
                if ((nums == '' || nums == 0)) {
                    $(this).focus();
                    showInfo('数量必须在1~100范围内', '180px', 2);
                    return;
                }

                $('.num_' + id).each(function (idx, value) {
                    var numsPlus = 0;
                    var nums = $(this).val() ? $(this).val() : $(this).attr('readOnlyNum');
                    var deleted = $(this).parents('tr').next('tr').find('input[name="RecipeRecord[deleted][]"]').val();
                    if (deleted == 1) {
                        return;
                    }

                    if (typeof (totalNum[id]) == "undefined") {
                        totalNum[id] = 0;
                    }
                    var assert = $(this).closest('tr').next('tr').find('td').find('.recipeOut').val();
                    var outVal = assert ? assert : $(this).closest('tr').next('tr').find('td').find('.recipeOutData').attr('recipeOutData');
                    if (outVal == 1) {
                        totalNum[id] += parseInt(nums);
                    }
                    recipeNumObj[idx] = $(this).val();
                });
                var outVal = $(this).parents('.recipeNameTd').next('tr').find('.recipeOut').val();
                if (1 == outVal) {
                    var totalObj = 0;
                    var hasOtherNums = 0;
                    if (!(typeof (recipeTotalNumsList[id]) == "undefined")) {
                        var totalObj = parseInt(eval(recipeTotalNumsList[id].join('+')));
                    }

                    if (!(typeof (recipeUsedTotalNums[id]) == "undefined")) {
                        hasOtherNums = parseInt(eval(recipeUsedTotalNums[id].join('+')));
                        totalObj = parseInt(totalObj) - hasOtherNums;
                    }
                    var hasChargedNum = 0;
//                    if (hasChargeed != null) {
//                        if (!(typeof (hasChargeed[id]) == "undefined")) {
//                            hasChargedNum = parseInt(eval(hasChargeed[id]));
//                        }
//                    }
                    var rows = parseInt(totalObj) - parseInt(totalNum[id]) - hasChargedNum;

                    if (rows < 0) {
                        showInfo('数量不能大于总库存量', '180px', 2);
                    }
                    if (!isNaN(rows) && rows >= 0) {
                        $('.totalNum_' + id).html(rows);

                    }
                }

            });

        },
        bind:function(){
            $('#reciperecord-recipename').select2({
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
                      
                     var name = '';
                     if(data.high_risk == 1){
                    	 name += '<span class="high-risk">高危</span>';
                     }
                     	 name += highLightKeywords(data.name,$.trim(searchData));
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
        			  
        			  var name = '';
        			  if(data.high_risk == 1){
                     	 name += '<span class="high-risk">高危</span>';
                      }
        			  name += htmlEncodeByRegExp(data.name);
        			  
                      var meta = '';
                      if(data.meta){
                     	 name += '-'+htmlEncodeByRegExp(data.meta);
                      }
                      name += '(';
                      if(data.product_name){
                          name += htmlEncodeByRegExp(data.product_name)+',';
                      }
                      if(data.specification){
                          name += htmlEncodeByRegExp(data.specification)+',';
                      }
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
            
            $('body').on('input','#triageinfo-weightkg',function(e){// 处方弹窗补充体重时的精确度控制
                accuracyControl($(this),2);
            });
        },
        
        initRecipePage:function(){
            var isRecipeCommitted = false;//表单是否已经提交标识，默认为false
            $('#recipe-record').yiiAjaxForm({
                beforeSend: function() {
                    if(isRecipeCommitted == false){
                        isRecipeCommitted = true;//提交表单后，将表单是否已经提交标识设置为true
                        return true;//返回true让表单正常提交
                    }else{
                        return false;//返回false那么表单将不提交
                    }
                },
                complete: function() {

                },
                success: function(data) {

                    if(data.errorCode == 0){
                        $('#recipe-template-select-ui').hide();
                        showInfo(data.msg,'180px');
                        if(isRecipeCommitted == true){
        			        $.pjax.reload({container:'#recipePjax',url:recipeRecordUrl,cache:false,push:false,replace:false,scrollTo:false,timeout : 5000});  //Reload

                        }

                    }else{
                        isRecipeCommitted = false;
                        showInfo(data.msg,'180px',2);
                    }
                }
            });
        },
        formatRecipeTemplateMenu: function () {
            var prevType = -1;
            var prevTypeID = -1;
            var treeMenu = [];
            console.log(recipeTemplateMenu);
            for (var i = 0; i < recipeTemplateMenu.length; i++) {
                var template = recipeTemplateMenu[i];
                var nodeL1 = {};
                nodeL1.text = htmlEncodeByRegExp(recipeTemplateType[template.type]);
                nodeL1.nodes = [];
                nodeL1.selectable = false;

                var nodeL2 = {};
                nodeL2.text = htmlEncodeByRegExp(template.recipe_type_template_name);
                // nodeL2.text =  template.recipe_type_template_name ;
                nodeL2.nodes = [];
                nodeL2.selectable = false;

                var nodeL3 = {};
                nodeL3.text = htmlEncodeByRegExp(template.name);
                nodeL3.tags = [template.id];

                if (template.type != prevType || i == 0) {
                    treeMenu.push(nodeL1);
                    treeMenu[treeMenu.length - 1]['nodes'].push(nodeL2);

                } else if (template.recipe_type_template_id != prevTypeID) {
                    treeMenu[treeMenu.length - 1]['nodes'].push(nodeL2);
                }

                var indexL2 = treeMenu[treeMenu.length - 1]['nodes'].length - 1;
                treeMenu[treeMenu.length - 1]['nodes'][indexL2]['nodes'].push(nodeL3);
                prevType = template.type;
                prevTypeID = template.recipe_type_template_id;

            }

            tree = treeMenu;
        },
        initTemplateMenul: function () {
            $('#recipe-template-select').treeview({
                data: tree,
                levels: 3,
                highlightSelected: false,
                showBorder: false,
                expandIcon: "glyphicon glyphicon-chevron-right",
                collapseIcon: "glyphicon glyphicon-chevron-down",
                onNodeSelected: function (event, data) {
                    $('#recipe-template-select').hide();
                    $('#recipe-template-select').treeview('unselectNode', [data.nodeId, {silent: true}]);
                    var recipe_id = data.tags[0];
                    _self.addRecipeTemplate(recipe_id);

                }
            });

            $('body').on('click', function () {
                if (!isInTemplateMenu) {
                    $('#recipe-template-select').hide();
                }
            });

            $("#recipe-template-select").mouseover(function () {
                isInTemplateMenu = true;
            });

            $("#recipe-template-select").mouseout(function () {
                isInTemplateMenu = false;
            });


            $('.recipe-template-desc').unbind().bind('click', function (event) {
                event.stopPropagation();

                if ($('#recipe-template-select').is(":hidden")) {
                    if (tree.length > 0) {
                        $('#recipe-template-select').show();
                    } else {
                        showInfo('暂无模板', '180px', 1);
                    }
                } else {
                    $('#recipe-template-select').hide();
                }
            });

            $('#recipe-template-select').hide();

        },
        addRecipeTemplate: function (id) {
            $.ajax({
                type: 'post',
                url: getRecipeTemplateInfo,
                data: {
                    'id': id
                },
                dataType: 'json',
                success: function (json) {
                    var cancel_option = {
                        label: "取消",
                        className: 'btn-default  btn-form',
                    };
                    var confirm_option = {
                        label: "确定",
                        className: 'btn-cancel btn-form',
                    };
                    btns = {
                        confirm: confirm_option,
                        cancel: cancel_option
                    };
                    if (json.data.recipeTemplateInfo.length == 0) {
                        showInfo('该模板下无处方（处方已停用或已删除），请重新编辑模板。', '480px', 2);
                        return;
                    }
                    var highRiskStatus = false;
                    for (var k = 0; k < json.data.recipeList.length; k++) {
                        if (json.data.recipeList[k].high_risk == 1) {
                            highRiskStatus = true;
                            break;
                        }
                    }
                    if (highRiskStatus) {
                        bootbox.confirm(
                                {
                                    message: '该药品为高危药品，确定使用吗?',
                                    title: '系统提示',
                                    buttons: btns,
                                    callback: function (confirmed) {
                                        if (confirmed) {
                                            if (json.data.recipeList != undefined && json.data.recipeList instanceof Array) {
                                                $('.status-recipe-column').show();
                                                for (var i = 0; i < json.data.recipeList.length; i++) {
                                                    var recipet = json.data.recipeList[i];
                                                    _self.addRecipe(recipet, json.data.recipeTemplateInfo[i], 1);
                                                }
                                            } else {
                                                return true;
                                            }
                                        }
                                    }
                                });
                    } else {
                        if (json.data.recipeList != undefined && json.data.recipeList instanceof Array) {
                            $('.status-recipe-column').show();
                            for (var i = 0; i < json.data.recipeList.length; i++) {
                                var recipet = json.data.recipeList[i];
                                _self.addRecipe(recipet, json.data.recipeTemplateInfo[i], 1);
                            }
                        }
                    }

                },
                error: function () {
                    showInfo('获取模板处方失败', '180px', 2);
                },
            });
        },
        addRecipe: function (data, configData, entrance) {

            var totalNum = 0;
            var hasNums = 0;
            var hasOtherNums = 0;
            var hasTotalNumsValue = 0;
            var usedNum = 0;
            console.log('bb:'+data);
            $('#reciperecord-recipename').val('');
            var recipeListId = data['recipelist_id'];
            if (!(typeof (recipeTotalNumsList[recipeListId]) == "undefined")) {
                hasNums = parseInt(eval(recipeTotalNumsList[recipeListId].join('+')));
                totalNum = hasNums;
                hasTotalNumsValue = hasNums;
                $('.num_' + recipeListId).each(function () {
                    var val = $(this).val() ? $(this).val() : $(this).attr('readOnlyNum');
                    if (!val) {
                        val = 0;
                    }
                    var outVal = $(this).closest('tr').next('tr').find('.recipeOut').val();
                    var recipeOutData = $(this).closest('tr').next('tr').find('.recipeOutData').attr('recipeOutData');
                    if (outVal == 1 || recipeOutData == 1) {
                        usedNum += parseInt(val);
                    }

                });
                // if (!(typeof (nowTotalNums[id]) == "undefined")) {
                //     nowNums = parseInt(eval(nowTotalNums[id].join('+')));
                // }
                var hasChargedNum = 0;
//                if (hasChargeed != null) {
//                    if (!(typeof (hasChargeed[recipeListId]) == "undefined")) {
//                        hasChargedNum = parseInt(eval(hasChargeed[recipeListId]));
//                    }
//                }
                totalNum = hasNums - usedNum - hasChargedNum;
            }
            if (!(typeof (recipeUsedTotalNums[recipeListId]) == "undefined")) {

                hasOtherNums = parseInt(eval(recipeUsedTotalNums[recipeListId].join('+')));
                totalNum = totalNum - hasOtherNums;
                hasTotalNumsValue = hasNums - hasOtherNums;
            }
            if (totalNum <= 0) {
                totalNum = 0;
            }
            var showValue = '';
            if (data.manufactor != '') {
                showValue += '生产商：' + htmlEncodeByRegExp(htmlEncodeByRegExp(data.manufactor)) + '<br/>';
            }
            var recipeModel = template.compile(recipeTpl)({
                id: data.id,
                unit: unit,
                list: data,
                price:data.price,
                showValue: showValue,
                recipeList: JSON.stringify(data),
                defaultUsed: defaultUsed,
                dosage_form: dosage_form,
                defaultFrequency: defaultFrequency,
                skinTestList: skinTestList,
                defaultUnit: defaultUnit,
                defaultAddress: defaultAddress,
                baseUrl: baseUrl,
                hasNums: hasNums, //总库存量
                totalNum: totalNum, //被占用后，剩余库存量
                hasTotalNumsValue: hasTotalNumsValue, //目前除了当前就诊记录数量，还剩多少库存量
                itemUrl: itemUrl,
                skinTestStatusList: skinTestStatusList, //皮试的状态列表
                configs: configData,
            });
            var cancel_option = {
                label: "取消",
                className: 'btn-default  btn-form',
            };
            var confirm_option = {
                label: "确定",
                className: 'btn-cancel btn-form',
            };
            btns = {
                confirm: confirm_option,
                cancel: cancel_option
            };
            if (entrance == 1) { //当entrance为1时高危物品不需要重复确认是否发药
                $('.recipe-form tbody').append(recipeModel);
                $('.num_' + configData.recipe_id).last().blur();
            } else {
                if (data.high_risk == 1) {
                    bootbox.confirm(
                            {
                                message: '该药品为高危药品，确定使用吗?',
                                title: '系统提示',
                                buttons: btns,
                                callback: function (confirmed) {
                                    if (confirmed) {
                                        $('.recipe-form tbody').append(recipeModel);
                                    } else {
                                        return true;
                                    }
                                }
                            }
                    );
                } else {
                    $('.recipe-form tbody').append(recipeModel);
                }
            }

            if (configData['skin_test_status'] != undefined && configData['skin_test_status'] == 2) {
                $('select.skinTestStatus').each(function () {
                    if ($(this).val() == 2) {
                        $(this).parent().parent().find('.skin-test-status').hide();
                    }
                });
            }
            
        },
        recipeEdit: function () {
            $('body').off('click','.recipe-delete>img').on('click', '.recipe-delete>img', function () {
                $(this).parents('tr').prev('tr').hide();
                $(this).parents('tr').hide();
                $(this).parents('tr').next('tr.skinTestTr').hide();
                $(this).siblings('input[name="RecipeRecord[deleted][]"]').val(1);
            })
            $('body').off('change','#reciperecord-recipename').on('change', '#reciperecord-recipename', function (e) {
            	var data = $(this).select2("data")[0];
                _self.addRecipe(data, '');
                $('.status-recipe-column').each(function () {
                    $(this).css("display", "table-cell");
                });
            });

        },
        changeStock: function () {//当选择外购时修改库存
            $('body').on('change', '.recipeOut', function (e) {
                var id = $(this).data('id');//处方id
                // $('.num_' + id).last().blur();

                if ($(this).val() == 2) {//外购

                    // $('.totalNum_'+id).html(parseInt(totalNum)+parseInt(num));
                    $(this).closest('tr').find('td').eq(0).find('section').hide();
                } else {
                    // var nowNums = parseInt(totalNum)-parseInt(usedNum);
                    // if(nowNums < 0){
                    //     nowNums = 0;
                    // }
                    $(this).closest('tr').find('td').eq(0).find('section').show();
                }
            });
        },
    };
    return mainHighRisk;
})
