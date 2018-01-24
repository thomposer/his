

define(function (require) {
    var common = require('js/lib/common');
    var _self;
    var main = {
        init: function () {
            _self = this;
            this.bindEvent();
        },
        bindEvent: function () {
            _self.initSelect();
            _self.choseInspect();
            _self.changeDeliver();
        },
        initSelect: function () {
            $('.select2').select2();
        },
        choseInspect: function () {
            $('body').off('change', '#inspectclinic-inspect_id').on('change', '#inspectclinic-inspect_id', function () {
                var id = $(this).val();
                var inspect = inspectList[id];
                _self.initFormData(inspect);
            })
        },
        initFormData: function (inspect) {
            console.log(inspect, 'ins');
            $('#inspectclinic-inspectunit').val(inspect.unit);
            $('#inspectclinic-phonetic').val(inspect.phonetic);
            $('#inspectclinic-internationalcode').val(inspect.international_code);
            $('#inspectclinic-tagid').val(inspect.tagName);
            $('#inspectclinic-doctorremark').val(inspect.remark);
            $('#inspectclinic-parentstatus').val(inspectStatus[inspect.status]);
            $('#inspectclinic-englishname').val(inspect.inspect_english_name);
            if (inspect.inspectItem) {
                var a = '';
                for (var i = 0; i < inspect.inspectItem.length; i++) {
                    var item = inspect.inspectItem[i];
                    a += '<label class="recipe-list-form-label"><input type="checkbox" name="InspectClinic[item][]" value="' + item.id + '" checked="">' + item.item_name + '</label>';
                }
                $('#inspectclinic-item').html(a);
            }
        },
        _initSelect: function () {
            var obj = $(".item_list").eq(0);
            this.selectItem($('body'));
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
                    if ($(this).val() == item_id) {
                        if ($($(e.currentTarget).siblings()[0]).val()) {
                            $(e.currentTarget).val($($(e.currentTarget).siblings()[0]).val());
                        } else {
                            $(e.currentTarget).val(0);
                        }
                        record++;
                        return false;
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
                _par.find('.item-english_name').html(htmlEncodeByRegExp(item.english_name));
                _par.find('.item-unit').html(htmlEncodeByRegExp(item.unit));
                _par.find('.item-ref').html(htmlEncodeByRegExp(item.reference));
                _par.find('.checkitemid').val(item_id);
            });
        },
        changeDeliver: function () {
            $('body').on('click', 'input[name="InspectClinic[deliver]"]', function () {
                if ($(this).val() == 1) {
                    $('.show_hide').removeClass('hide');
                } else {
                    $('.show_hide').addClass('hide');
                    $('#inspectclinic-deliver_organization').val('');
                }
            })
        }
    };
    return main;
})