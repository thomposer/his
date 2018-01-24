

define(function (require) {
    var common = require('js/lib/common');
    var select = require('plugins/select2/select2.full.min');
    var template = require('template');
    var inspectTpl = require('tpl/inspect/inspect.tpl');
    var main = {
        init: function () {
            this.bindEvent();
        },
        bindEvent: function () {
            this.addInspectItemRealtion();
            this.checkData();
            this._initSelect();
        },
        addInspectItemRealtion: function () {
            var _self = this;
            var inspectTplStr = template.compile(inspectTpl)({
                list: item_list,
            });
//            var template = $('#relation-list-tpl').html();
            $('body').on('click', '.clinic-add', function () {
                var union = $('.inspect_union tbody');
                union.append(inspectTplStr);
                $('.select2').select2();
                _self._setItem();
                _self.selectItem($(this));
            });
            $('body').on('click', '.clinic-delete', function (e) {
                e.preventDefault();
                var _par = $(this).closest('tr');
                var deleted=_par.find('.deleted').val();
                var newRecord=_par.find('.new-record').val();
                if(newRecord==2){
                    _par.find('.deleted').val(1);
                    $(e.target).closest('tr').addClass('hide');
                }else{
                    $(e.target).closest('tr').remove();
                }
                _self._setItem();
//                if ($('#cure-record').find('.clinic-delete').length == 1) {
//                    $('#cure-record').find('.clinic-delete').first().addClass('hidden');
//                }
            });

        },
        _setItem: function () {
            $('#cure-record').find('tr:not(.hide)').find('.clinic-delete').show();
            $('#cure-record').find('tr:not(.hide)').find('.clinic-add').hide().last().show();
            if ($('#cure-record').find('tr:not(.hide)').find('.clinic-delete').length == 1) {
                $('#cure-record').find('tr:not(.hide)').find('.clinic-delete').first().hide();
            }
        },
        _initSelect: function () {
            var obj = $(".item_list").eq(0);
            this.selectItem($('body'));
        },
        checkData: function () {
            $('body').on('click', '#union_submit', function (e) {
                console.log($(".item_list"), 'domALL');
                for (var i = 0; i < $(".item_list").length; i++) {
                    if ($(".item_list")[i].value == 0) {
                        console.log($(".item_list")[i].value, 'dom value == ');
                        console.log($(".item_list")[i], 'dom singel == ');
                        showInfo('内容不能为空', '160px');
                        return false;
                    }
                }
                $('#cure-record').submit();
            });
        },
        selectItem: function (obj) {
            obj.off('change', '.item_list').on('change', '.item_list', function (e) {
//            $('.item_list').on('change', function (e) {
                var that = $(this);
                var thatVal = that.val();
                var textVal = that.siblings('.select2').find('.select2-selection__rendered').html();
                var _par = $(this).closest('tr');
                var item_id = $(this).val();
//                var length=$('.select-items').find('input[value=' + val + ']').length;
                var record = 0;
                $('.checkitemid').each(function () {
                    if($(this).parents('.select-items').find('.deleted').val() == 2){
                        if ($(this).val() == item_id) {
                            if ($($(e.currentTarget).siblings()[0]).val()) {
                                $(e.currentTarget).val($($(e.currentTarget).siblings()[0]).val());
                            } else {
                                $(e.currentTarget).val(0);
                            }
                            record++;
                            return false;
                        }
                    }
                });
                if (record > 0) {
                    showInfo('该项已经存在', '160px');
                    console.log(thatVal, 'that');
                    console.log($(this).val(), 'this');
                    console.log(textVal, 'textVal');
                    var thisVal = $(this).val();
                    if ((thatVal != thisVal)) {
                        e.preventDefault();
                        _par.find('.items-select-id').html('');
                        _par.find('.select2-selection__rendered').html('请输入项目名称');
                        _par.find('.item-english_name').html('');
                        _par.find('.item-unit').html('');
                        _par.find('.item-ref').html('');
                        _par.find('.checkitemid').val('');
                    }
                    return false;
                }
                console.log(item_id, 'item_id');
                var item = item_list[item_id];
                if(item_id != 0){
                    _par.find('.items-select-id').html(item.id);
                    _par.find('.item-english_name').html(htmlEncodeByRegExp(item.english_name));
                    _par.find('.item-unit').html(htmlEncodeByRegExp(item.unit));
                    _par.find('.item-ref').html(htmlEncodeByRegExp(item.reference));
                    _par.find('.checkitemid').val(item_id);
                }else{
                    _par.find('.items-select-id').html('');
                    _par.find('.item-english_name').html('');
                    _par.find('.item-unit').html('');
                    _par.find('.item-ref').html('');
                    _par.find('.checkitemid').val('');
                }
                
            });
        }
    };
    return main;
})