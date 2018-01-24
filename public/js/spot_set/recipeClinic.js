define(function (require) {
    var common = require('js/lib/common');
//    var select = require('plugins/select2/select2.full.min');
    var _self;
    var main = {
        init: function () {
            _self = this;
            this.bindEvent();
        },
        bindEvent: function () {
            _self.initSelect();
            _self.choseCheck();
        },
        initSelect: function () {
            $('.select2').select2();
        },
        choseCheck: function () {
            $('body').off('change', '#recipelistclinic-recipelist_id').on('change', '#recipelistclinic-recipelist_id', function () {
                var id = $(this).val();
                var recipeListClinic = recipeList[id];
                _self.initFormData(recipeListClinic);
            });
            if(error == 1){
            	var id = $('#recipelistclinic-recipelist_id').val();
            	var recipeListClinic = recipeList[id];
                _self.initFormData(recipeListClinic);
            }
        },
        initFormData: function (recipeListClinic) {
            $('#recipelistclinic-drug_type').val(recipeListClinic.drug_type);
            $('#recipelistclinic-specification').val(recipeListClinic.specification);
            $('#recipelistclinic-type').val(recipeListClinic.type);
            $('#recipelistclinic-dose_unit').val(recipeListClinic.dose_unit);
            $('#recipelistclinic-unit').val(recipeListClinic.unit);
            // $('#recipelistclinic-high_risk').val(checkListClinic.high_risk);
            $("input[name='RecipelistClinic[high_risk]'][value="+recipeListClinic.high_risk+"]").attr('checked','checked');
            $('#recipelistclinic-manufactor').val(recipeListClinic.manufactor);
            // 回填计量单位
            var doseUnitArr = recipeListClinic.dose_unit.split(',');
            for(v in doseUnitArr){
            	$("input[name='RecipelistClinic[dose_unit][]'][value="+doseUnitArr[v]+"]").attr('checked','checked');
            }
        }
    };
    return main;
})