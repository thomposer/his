

define(function (require) {
    var template = require('template');
    var select = require('plugins/select2/select2.full.min');
    var common = require('js/lib/common');
    // var cityPickerData = require('js/lib/city-picker.data');
    // var cityPicker = require('js/lib/city-picker');
    var csrfToken = $('meta[name="csrf-token"]').attr('content');
    var makeupRecipeTpl = require('tpl/makeup/recipe.tpl');
    var makeupInspectTpl = require('tpl/makeup/inspect.tpl');
    var makeupCureTpl = require('tpl/makeup/cure.tpl');
    var makeupCheckTpl = require('tpl/makeup/check.tpl');
    var _self;

    var main = {
        init: function () {
            _self = this;
            this.bindEvent();
        },
        bindEvent: function () {
            _self.recipeEdit();
            _self.inspectEdit();
            _self.checkEdit();
            _self.cureEdit();
            _self.receptionJump();
            _self.discountChange();
          //判断皮试的选择
            $(document).on('change','.skinTestStatus',function(){
            		var id = $(this).val();
            		if(id == 1){
            			$(this).parent('td').siblings('td').children('label.skinTestContent').show();
            		}else{
            			$(this).parent('td').siblings('td').children('label.skinTestContent').hide();
            		}
            });
        },
        /**
         *
         */
        recipeEdit: function () {
            $('body').on('click', '.recipe-delete>img', function () {
                $(this).parents('tr').hide();
                $(this).parents('tr').next('tr.skinTestTr').hide();
                $(this).siblings('input[name="RecipeRecord[deleted][]"]').val(1);
            })
            $('body').off('change', '#reciperecord-recipename').on('change', '#reciperecord-recipename', function (e) {
                var val = $(this).val();
                _self.addRecipe(val);
                $(this).val('');
            });

        },
        addRecipe: function (id) {
//            console.log(recipeList, 'recipeList');
//            console.log(id, 'id');
            var recipeModel = template.compile(makeupRecipeTpl)({
                id: id,
                unit: unit,
                list: recipeList[id],
                recipeList: JSON.stringify(recipeList[id]),
                defaultUsed: defaultUsed,
                defaultFrequency: defaultFrequency,
                dosage_form: dosage_form,
                defaultUnit: defaultUnit,
                defaultAddress: defaultAddress,
                baseUrl: baseUrl,
                hasNums: 1, //总库存量
                totalNum: 1, //被占用后，剩余库存量
                skinTestStatusList : skinTestStatusList
            });
            $('.recipe-form tbody').append(recipeModel);
        },
        inspectEdit: function () {

            $('body').on('click', '#inspect-record .op-group>img', function () {
                $(this).parents('.inspect-list').hide();
                $(this).parent('.op-group').siblings('.check-id').children('input[name="InspectRecord[deleted][]"]').val(1);
            })
            $('body').off('change', '#inspectrecord-inspectname').on('change', '#inspectrecord-inspectname', function (e) {
                e.preventDefault();
                var val = $(this).val();
                _self.addInspect(inspectList[val], inspectList[val]['name'], 'InspectRecord[inspect_id][]', 'InspectRecord[deleted][]', '.inspect-list', '.inspect-content', 1);
                $(this).val('');
            });
        },
        addInspect: function (list, name, inputName, deleted, parentClass, appendClass, type) {
            var checkModel = template.compile(makeupInspectTpl)({
                listString: htmlEncodeByRegExp(JSON.stringify(list)),
                list: list,
                uuid: _self.getTimeRndString(),
            });
            $(appendClass).append(checkModel);
        },
        checkEdit: function () {
            $('body').off('click','#check-record .op-group>img').on('click', '#check-record .op-group>img', function () {
                $(this).parents('.check-list').hide();
                $(this).parent('.op-group').siblings('.check-id').children('input[name="CheckRecord[deleted][]"]').val(1);
            })
            $('body').off('change', '#checkrecord-checkname').on('change', '#checkrecord-checkname', function (e) {
                e.preventDefault();
                var val = $(this).val();
                _self.addCheckTpl(checkList[val], checkList[val]['name'], 'CheckRecord[inspect_id][]', 'CheckRecord[deleted][]', '.check-list', '.check-content', 1);
                $(this).val('');
            });
        },
        addCheckTpl: function (list, name, inputName, deleted, parentClass, appendClass, type) {
            var checkModel = template.compile(makeupCheckTpl)({
                listString: htmlEncodeByRegExp(JSON.stringify(list)),
                list: list,
            });
            $(appendClass).append(checkModel);
        },
        cureEdit: function () {
            $('body').on('click', '.cure-form .op-group>img', function () {
                $(this).parents('tr').hide();
                $(this).siblings('input[name="CureRecord[deleted][]"]').val(1);
            })
            $('body').off('change', '#curerecord-curename').on('change', '#curerecord-curename', function (e) {
                var val = $(this).val();
                _self.addCure(cureList[val], cureList[val]['name'], cureList[val]['unit']);
                $('#curerecord-curename').val('');
                /*var tr_data = $(this).parents('.cure-record-form').siblings('.box').find('tr');
                 for(var i = 1; i < tr_data.length; i++){
                 
                 }*/
            });
        },
        addCure: function (id, name, unit) {
            var cureModel = template.compile(makeupCureTpl)({
                id: JSON.stringify(id),
                name: name,
                unit: unit,
                baseUrl: baseUrl
            });
            $('.cure-form tbody').append(cureModel);
        },
        getTimeRndString: function () {
            var tm = new Date();
            var str = tm.getMilliseconds() + tm.getSeconds() * 60 + tm.getMinutes() * 3600 + tm.getHours() * 60 * 3600 + tm.getDay() * 3600 * 24 + tm.getMonth() * 3600 * 24 * 31 + tm.getYear() * 3600 * 24 * 31 * 12;
            return str;
        },
        receptionJump: function () {
            $('body').off('click','.blank').on('click', '.blank', function (e) {
                e.preventDefault();
                window.open($(this).attr('href'));
            })
        },
        discountChange:function (){
            $('#umprecord-discounttype').on('change',function(){
               if($(this).val()!=1){
                   $('.discount').show();
               }else{
                   $('.discount').hide();
               }
            })
        }
    };
    return main;
})