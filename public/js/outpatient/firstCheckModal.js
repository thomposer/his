define(function (require) {
    var _self;
    var template = require('template');
    var checkTpl = require('tpl/outpatient/check.tpl');
    var firstCheckTpl = require('tpl/firstCheckTpl.tpl');
    var common = require('js/lib/common');
    var firstCheckMaxLen = 30;
    var main = {
        init: function () {
            _self = this;
            this.bindEvent();
        },
        bindEvent: function () {
            _self.initSelect2();
            _self.addFirst();
            _self.firstCheckRecordBind();
            _self.saveModalForm();
        },
        addFirst: function (parentObj) {
            parentObj = parentObj || '.first-check-content-modal';
            $('body').off('click', parentObj + ' .first-check-add').on('click', parentObj + ' .first-check-add', function () {
                var key = parseInt($(this).attr('data-key')) + 1;
                _self.appendCheckLine(key);
            });
            $('body').off('click', parentObj + ' .first-check-delete').on('click', parentObj + ' .first-check-delete', function () {
                $(this).parents('.first-check-line').remove();
                _self.dealWithSymbol();
                var len = $(parentObj + ' .first-check-line').length;
                if (len == 1) {
                    $(parentObj + ' .first-check-delete').hide();
                }
                $(parentObj + ' .first-check-add').last().show();
            });
        },
        appendCheckLine: function (key) {
            var html = template.compile(firstCheckTpl)({
                key: key,
            });
            $('.first-check-content-modal').append(html);//
            _self.initSelect2();
            _self.dealWithSymbol();
        },
        dealWithSymbol: function (parentObj) {
            parentObj = parentObj || '.first-check-content-modal';
            $(parentObj + ' .first-check-add').hide();
            $(parentObj + ' .first-check-delete').show();
            $(parentObj + ' .first-check-delete').last().show();
            $(parentObj + ' .first-check-add').last().show();
        },
        initSelect2: function () {
//            if ($(".CheckCodeSel").length < 1) {
//                return;
//            }
//            if ($(".CheckCodeSel").is(":hidden")) {
//                return;
//            }
            $('.CheckCodeSel:not(.hide)').each(function () {
                var $select2 = $(this).select2({
                    language: "zh-CN",
                    ajax: {
                        url: getCheckCodeList,
                        dataType: 'json',
                        delay: 200,
                        data: function (params) {
                            return {
                                search: params.term, // search term
                            };
                        },
                        processResults: function (data) {
                            return {
                                results: data.list,
                            };
                        },
                    },
                    escapeMarkup: function (markup) {

                        return markup;
                    }, // let our custom formatter work
                    minimumInputLength: 1, //至少输入多少个字符后才会去调用ajax
                    templateResult: function (repo) {
                        searchData = $('.select2-search__field').val();
                        if(typeof(searchData) != 'undefined'){
                            searchData = searchData.toString();
                            return searchHighlight(searchData,htmlEncodeByRegExp(repo.text));//高亮显示要搜索的数据
                        }
                        return htmlEncodeByRegExp(repo.text);
                    },
                    templateSelection: function (repo) {
                        return htmlEncodeByRegExp(repo.text);
                    },
                    width: "100%",
                });

                $select2.data('select2').$container.addClass("CheckCodeSel2");
            })

        },
        firstCheckRecordBind: function () {
            $('body').on('change', '.select-first-check', function () {
                if ($(this).val() == 1) {
                    $(this).parents('.first-check-type').siblings('.first-check-text').find('.CheckCodeSel').removeClass('hide');
                    $(this).parents('.first-check-type').siblings('.first-check-text').find('.select2-container').removeClass('hide');
                    $(this).parents('.first-check-type').siblings('.first-check-text').find('.first-check-custom').val('').addClass('hide');
                } else {
                    $(this).parents('.first-check-type').siblings('.first-check-text').find('.CheckCodeSel').val(0).addClass('hide');
                    $(this).parents('.first-check-type').siblings('.first-check-text').find('.select2-container').addClass('hide');
                    $(this).parents('.first-check-type').siblings('.first-check-text').find('.select2-container').find('.select2-selection__rendered').text('请选择');
                    $(this).parents('.first-check-type').siblings('.first-check-text').find('.first-check-custom').val('');
                    $(this).parents('.first-check-type').siblings('.first-check-text').find('.first-check-custom').removeClass('hide');
                }
            });
            $('body').off('change', '.CheckCodeSel').on('change', '.CheckCodeSel', function (e) {
                var val = $(this).val();
                var data = $(this).select2("data")[0];
                var item = {};
                item.check_code_id = data['id'];
                item.content = data['name'];
                var n = $(this).siblings('.field-firstcheck-content').find('.first-check-custom').val(item.content);
                $(this).parents('.first-check-text').find('.help-block').last().html('');
            });
            $('body').off('focusout', '.first-check-content-modal .first-check-custom').on('focusout', '.first-check-content-modal .first-check-custom', function (e) {
                if($(this).val()){
                    $(this).parents('.first-check-text').find('.help-block').last().html('');
                }
            })
        },
        initFirstCheckBtn: function (parentObj) {
            parentObj = parentObj || '.first-check-content-modal';
            console.log(parentObj, 'par');
            var len = $(parentObj).find('.first-check-line').length;
            console.log(len, 'len');
            if (len >= 2) {
                $(parentObj + " .first-check-delete").show();
                $(parentObj + " .first-check-add").hide();
                $(parentObj + ' .first-check-add').last().show();
            } else {
                $(parentObj + " .first-check-delete").first().hide();
            }
        },
        saveModalForm: function () {
            $('#first-check-weight').yiiAjaxForm({
                beforeSend: function () {
                    var status = 0;
                    var length = 0;
                    $('#first-check-weight .select-first-check').each(function () {
                        var val = $(this).val();
                        if (val == 1) {//icd-10
                            var contentId = $(this).parents('.first-check-type').siblings('.first-check-text').find('.CheckCodeSel').val();
                            console.log(contentId, 'contentId');
                            if (contentId == 0) {
                                console.log('haha');
                                $(this).parents('.first-check-type').siblings('.first-check-text').find('.first-check-right').addClass('has-error').find('.help-block:last').text('初步诊断不能为空');
                                status = 1;
                            }
                        } else {
                            var contentText = $(this).parents('.first-check-type').siblings('.first-check-text').find('.first-check-custom').val();
                            console.log(contentText, 'contentId');
                            if (contentText == '') {
                                console.log('haha');
                                $(this).parents('.first-check-type').siblings('.first-check-text').find('.first-check-right').addClass('has-error').find('.help-block:last').text('初步诊断不能为空');
                                status = 1;
                            }
                        }
                    });
//                    if(length == 0 && ordersStatus){
//                        status = true;
//                        $('#recordForm .first-check-right').find('.first-check-error').remove();
//                        $('#recordForm .first-check-right').append('<div class="first-check-error" style="padding-top: 3px;color: #ff4b00;">已开医嘱，初步诊断不能为空</div>');
//                    }
                    if (status) {
                        return false;
                    }
                },
                complete: function () {


                },
                success: function (data) {
                    if (data.errorCode == 0) {
                        //$('#progressWizard').find('li.border-none').eq(2).find('a').click();
                        showInfo('保存成功', '180px');
                        window.location.reload();
                    } else {
//                        isCheckCommitted = false
                        showInfo('操作失败', '250px', 2);
                    }
                },
            });
        }
    };
    return main;
});
