define(function () {
    return ''
        + ''
        + ''
        + '\t<tr class = "recipeNameTd">'
        + ''
        + '\t\t<td class="recipeName" data-type="recipeName"><span data-toggle="tooltip" data-html="true" data-placement="bottom" data-original-title="{{showValue}}">{{list.name}}'
        + '\t\t{{if list.specification !="" }}（{{list.specification}}）'
        + '\t\t{{/if}}</span>'
        + '{{if list.medicine_description_id != 0}}'
        + '<a href="{{itemUrl}}?id={{list.medicine_description_id}}" role="modal-remote" data-toggle="tooltip" data-modal-size="large" data-original-title="" data-request-method="post" title=""><i class="fa fa-question-circle recipe-question"></i></a>'
        + '{{/if}}'
        + '<input type="hidden" class="form-control" name="RecipeTemplateInfo[recipe_id][]" value = "{{recipeList}}"><input type="hidden" class="form-control" name="RecipeTemplateInfo[totalNum][]" value = "{{hasTotalNumsValue}}"></td>'
        + ''
        + '\t\t<td>'
        + '{{dosage_form[list.type]}}'
        + ''
        + '\t\t</td>'
        + ''
        + '\t\t<td><input type="text" class="form-control" name="RecipeTemplateInfo[dose][]" style="width: 45%;display: initial;">'
        + ''
        +'<select class="form-control" name="RecipeTemplateInfo[dose_unit][]" style="width: 50%; float: right;">'
        + ''
        + '\t\t\t\t\t{{if list.dose_unit_num != 1}}'
        + ''
        + '<option value=""></option>'
        + ''
        + '\t\t\t\t\t{{/if}}'
        + ''
        + '\t\t\t\t{{each list.dose_unit}}'
        + ''
        +'<option value="{{$index}}">{{$value}}</option>'
        + ''
        + '\t\t\t\t{{/each}}'
        + ''
        +'</select> '
        + ''
        +'</td>'
        + ''
        + '\t\t<td>'
        + ''
        + '\t\t\t<select class="form-control" name="RecipeTemplateInfo[used][]" value = "{{list.default_used}}">'
        + ''
        + '\t\t\t\t\t<option value="0">请选择</option>'

        + '\t\t\t\t{{each defaultUsed}}'
        + ''
        + '\t\t\t\t\t<option value="{{$index}}">{{$value}}</option>\t\t\t\t\t'
        + ''
        + '\t\t\t\t{{/each}}'
        + ''
        + '\t\t\t</select>'
        + ''
        + '\t\t</td>'
        + ''
        + '\t\t<td>'
        + ''
        + '\t\t\t<select class="form-control" name="RecipeTemplateInfo[frequency][]" value = "{{list.default_frequency}}">'
        + ''
        + '\t\t\t\t\t<option value="0">请选择</option>'
//        + '\t\t\t\t{{each defaultFrequency}}'
        + ''
//        + '\t\t\t\t\t<option value="{{$index}}">{{$value}}</option>'
        + '\t\t\t\t\t<option value="15">1小时一次（Q1h）</option>'
	    + '   <option value="16">2小时一次（Q2h）</option> '
	    + '   <option value="17">4小时一次（Q4h）</option> '
	    + '  <option value="18">6小时一次（Q6h）</option> '
	    + '  <option value="19">8小时一次（Q8h）</option> '
	    + '   <option value="20">12小时一次（Q12h）</option> '
	    + '   <option value="1">每天四次（QID）</option> '
	    + '    <option value="2">每天三次（TID）</option> '
	    + '    <option value="3">每天两次（BID）</option> '
	    + '    <option value="4">每天一次（QD）</option> '
	    + '    <option value="5">隔天一次（QOD）</option> '
	    + '    <option value="7">每周一次（QW）</option> '
	    + '    <option value="21">晚上一次（QN）</option> '
	    + '    <option value="8">必要时（PRN）</option> '
	    + '    <option value="9">立即</option> '
	    + '    <option value="10">空腹</option> '
	    + '    <option value="11">饭前</option> '
	    + '    <option value="12">饭中</option> '
	    + '    <option value="13">饭后</option> '
	    + '    <option value="14">睡前</option> '
        
        + ''
//        + '\t\t\t\t{{/each}}'
        + ''
        + '\t\t\t</select>'
        + ''
        + '\t\t</td>'
        + ''
        + '\t\t<td>'
        + ''
        + '\t\t\t<input type="text" class="form-control" name="RecipeTemplateInfo[day][]">'
        + ''
        + '\t\t</td>'
        + ''
        + '\t\t<td>'
        + ''
        + '\t\t\t<input type="text" class="form-control recipeNum num_{{id}}" data-id = "{{id}}" name="RecipeTemplateInfo[num][]">'
        + ''
        + '\t\t\t<span>{{unit[list.unit]}}</span>'
        + ''
        + ''
        + '\t\t</td>'
        + ''
        + '\t</tr>'
        + ''
        + '\t<tr>'
        + ''
        + '\t\t<td></td>'
        + ''
        + '\t\t<td colspan="4">'
        + ''
        + '\t\t\t<input type="text" class="form-control" name="RecipeTemplateInfo[description][]" placeholder = "请输入说明/描述,不超过35个字">'
        + ''
        + '\t\t</td>'
        + ''
        + '\t\t<td>'
        + ''
        + '\t\t\t<select class="form-control recipeOut" name="RecipeTemplateInfo[type][]" data-id="{{id}}">'
        + ''
        + '\t\t\t\t{{each defaultAddress}}'
        + ''
        + '\t\t\t\t\t<option value="{{$index}}">{{$value}}</option>'
        + ''
        + '\t\t\t\t{{/each}}'
        + ''
        + '\t\t\t</select>'
        + ''
        + '\t\t</td>'
        + ''
        + '\t\t<td class="recipe-delete op-group" style="display: table-cell;"><input type="hidden" class="form-control" name="RecipeTemplateInfo[deleted][]" value=""><img src="{{baseUrl}}/public/img/common/delete.png"></td>'
        + ''
        + '\t</tr>'
    	+ '{{if list.skin_test_status == 1}}'
    	+ '<tr class = "skinTestTr">'
    	+ '\t\t<td colspan = "2" >'
    	+ '皮试：'
    	+ '\t\t\t<select class="skinTestStatus cure-skin-select form-control" name="RecipeTemplateInfo[skin_test_status][]" style="display: inline-block;" >'
        + ''
        + '\t\t\t\t\t<option value="0">请选择</option>'
        + ''
        + '\t\t\t\t{{each skinTestStatusList}}'
        + ''
        + '\t\t\t\t\t<option value="{{$index}}">{{$value}}</option>'
        + ''
        + '\t\t\t\t{{/each}}'
        + ''
        + '\t\t\t</select>'
        + ''
        + '\t\t<td colspan = "3">'
        + '<label class="skin-test-content skin-test-status" style="display: none;">'
        + '{{if list.skin_test}}皮试内容：{{list.skin_test}}{{/if}}'
        + '</label>'
        + '\t\t</td>'
        + '\t\t<td colspan = "2">'
        + '<label class="skin-test-content skin-test-status" style="display: none;">皮试类型：</label>'
    	+ '\t\t\t<select class="skin-test-status curelistId  cure-skin-select form-control" name="RecipeTemplateInfo[curelist_id][]" style="display: none;" >'
        + ''
        + '\t\t\t\t{{each skinTestList}}'
        + ''
        + '\t\t\t\t\t<option value="{{$index}}">{{$value}}</option>'
        + ''
        + '\t\t\t\t{{/each}}'
        + ''
        + '\t\t\t</select>'
    	+ '<input type="hidden" name="RecipeTemplateInfo[skin_test][]" value = "{{list.skin_test}}">'

        + '\t\t</td>'
        
        + ''
    	+ '</tr>'
        + ''
    	+ '{{else}}'
        + ''
    	+ '<input type="hidden" name="RecipeTemplateInfo[skin_test_status][]" value>'
        + ''
        + ''
    	+ '<input type="hidden" name="RecipeTemplateInfo[curelist_id][]" value = 0>'
        + ''
    	+ '{{/if}}'
        + '';

});